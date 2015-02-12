<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 12/02/15
 * Time: 08:24
 *
 *
 * NO NO NO don't look here! This part is written terrible!!!!!!
 *
 * okay bare with me, this isn't written very nice but is works... I guess.... sort of ..... :'-}
 */

defined('_SHORT_INCLUDE_ONLY') or die(header('HTTP/1.0 404 Not Found'));

//gatering some data!
$data = shell_exec('uptime');
$uptime = explode(',', $data);
$uptime = explode(' up ', $uptime[0]);
$uptime = explode(':', $uptime[1]);

$today = strtotime(date('Y-m-d'));

if(isset($uptime[1])){
    $server_uptime = $uptime[0] . "h" . $uptime[1] . "m";
}else{
    $server_uptime = $uptime[0];
}
$M_DB_M = new M_DB(MONGO_COLLECTION_MAIN);

$stats = $M_DB_M->mongoDbStats();
$dbsize = formatBytes($stats['dataSize']);

$nr_of_rows = $M_DB_M->getNrRecords();

$rows_today = $M_DB_M->getNrRecords($today);

$last_monts_urls_nrs = $M_DB_M->getNrRecordsPerDay();

foreach ($last_monts_urls_nrs as $value) {
    $last_monts_urls_nrs_dates[$value['_id']] = $value['_id'];
    $last_monts_urls_nrs_nr[$value['_id']] = $value['count'];
}

$M_DB_C = new M_DB(MONGO_COLLECTION_CLICKS);

$last_months_urls_clicks = $M_DB_C->getNrRecordsPerDay();

$eightDaysAgo = strtotime('-8 day', strtotime(date('Y-m-d')));
$yesterDay = strtotime('-1 day', strtotime(date('Y-m-d')));


$Clicks_last_seven_days_array = $M_DB_C->getNrRecordsPerDay($eightDaysAgo, $yesterDay);

foreach($Clicks_last_seven_days_array as $value){
    $weekday = date('l', strtotime($value['_id']));
    $Clicks_last_seven_days[] = "['{$weekday}', {$value['count']}]";
}
$Clicks_last_seven_days = implode(',', $Clicks_last_seven_days);

$last_months_urls_clicks = $M_DB_C->getNrRecordsPerDay();

$last_months_urls_clicks_dates = array();
foreach ($last_months_urls_clicks as $value) {
    $last_months_urls_clicks_dates[$value['_id']] = $value['_id'];
    $last_months_urls_clicks_nr[$value['_id']] = $value['count'];
}

foreach ($last_monts_urls_nrs_dates as $dates) {
    $seriesdates[] = $dates;
    $series1[] = (isset($last_monts_urls_nrs_nr[$dates])) ? $last_monts_urls_nrs_nr[$dates] : 0;
    $series2[] = (isset($last_months_urls_clicks_nr[$dates])) ? $last_months_urls_clicks_nr[$dates] : 0;
}

$last_eight_days_clicks = $M_DB_C->getNrRecords($eightDaysAgo, $yesterDay);

$Urls_last_seven_days_array = $M_DB_M->getNrRecordsPerDay($eightDaysAgo, $yesterDay);

$Urls_last_seven_days = array();
foreach($Urls_last_seven_days_array as $value){
    $weekday = date('l', strtotime($value['_id']));
    $Urls_last_seven_days[] = "['{$weekday}', {$value['count']}]";
}

$Urls_last_seven_days = implode(',', $Urls_last_seven_days);

$today_clicks = $M_DB_C->getNrRecords($today);

$last_month_same_day = strtotime('-1 month', strtotime(date('Y-m-d')));
$last_month_same_day_clicks = $M_DB_C->getNrRecords($last_month_same_day);
$last_month_same_day_urls = $M_DB_M->getNrRecords($last_month_same_day);


$difference_day_clicks = $today_clicks - $last_month_same_day_clicks;
$difference_day_urls = $rows_today - $last_month_same_day_urls;


$avg_eight_days = ($last_eight_days_clicks / 7);
if ($avg_eight_days > 0 && $today_clicks > 0) {
    $avg_today = round(($today_clicks / $avg_eight_days) * 100);
} else {
    $avg_today = $today_clicks;
}

$avg_not_today = 100 - $avg_today;

$timestamp = time();

$this_month_name = date('F');
$first_day_this_month = strtotime(date('Y-m-01'));

$firstday_last_month = strtotime('-1 month', strtotime(date('Y-m-01')));
$last_month_name = date('F', $firstday_last_month);

$firstday_prelast_month = strtotime('-2 month', strtotime(date('Y-m-01')));
$prethis_month_name = date('F', $firstday_prelast_month);

$thismonth_clicks = $M_DB_C->getNrRecords($first_day_this_month, $timestamp);
$thismonth_urls = $M_DB_M->getNrRecords($first_day_this_month, $timestamp);

$lastmonth_clicks = $M_DB_C->getNrRecords($firstday_last_month, $first_day_this_month);
$lastmonth_urls = $M_DB_M->getNrRecords($firstday_last_month, $first_day_this_month);

$prelastmonth_clicks = $M_DB_C->getNrRecords($firstday_prelast_month, $firstday_last_month);
$prelastmonth_urls = $M_DB_M->getNrRecords($firstday_prelast_month, $firstday_last_month);
