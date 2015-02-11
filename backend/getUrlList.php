<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 10/02/15
 * Time: 23:21
 */
define('_SHORT_INCLUDE_ONLY', 1);
setlocale(LC_TIME, 'NL_nl');
date_default_timezone_set('Europe/Amsterdam');

require_once('../config.php');

if(SHOW_ERRORS){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once('../functions.php');
require_once('../mongodb.php');

$M_DB_C = new M_DB(MONGO_COLLECTION_CLICKS);
$M_DB_M = new M_DB(MONGO_COLLECTION_MAIN);

$startDate = (isset($_REQUEST['startdate']))? $_REQUEST['startdate'] : null;
$endDate = (isset($_REQUEST['enddate']))? $_REQUEST['enddate'] : null;


$records = $M_DB_C->getRecordsCountByShortUrl($startDate, $endDate);

$newData = array();
$short_urls = array();
foreach($records as $record){
    $newData[$record['_id']]['short_url'] = BASE_HREF . $record['_id'];
    $newData[$record['_id']]['count'] = $record['count'];
    $short_urls[] = $record['_id'];
}
unset($records);
//search all long urls
$records = $M_DB_M->getLongUrlRecordsByShortUrls($short_urls);
foreach($records as $record){
    $newData[$record['short_url']]['long_url'] = $record['long_url'];
}
unset($records);


foreach($newData as $record){
    $records[] = $record;
}

$newData = array();
$newdata['data'] = $records;
echo json_encode($newdata);