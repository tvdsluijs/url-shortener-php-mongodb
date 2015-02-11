<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 11/02/15
 * Time: 07:12
 */
define('_SHORT_INCLUDE_ONLY', 1);
setlocale(LC_TIME, 'NL_nl');
date_default_timezone_set('Europe/Amsterdam');

require_once('../config.php');
$time_start = microtime(true);
/**
 * THERE IS ALREADY A TEST FILE THAT YOU CAN USE !!
 * usage
 * create a sitemap from any site with
 * https://www.xml-sitemaps.com/
 * Download the .TXT file, name it "urllist.txt" and put it in your backend folder.
 * Adust the below config vars and run this script
 * After the import is done, the import txt file will be renamed so you cannot import it twice
 */
/** CONFIG */
$startDateInt = strtotime ( '-2 month' , strtotime ( date('Y-m-d') )) ; //today two months ago
$startDate =  date('Y-m-d', $startDateInt) ;//today last month

$endDateInt =  strtotime ( '+1 day' , strtotime ( date('Y-m-d') )) ;  //today
$endDate = date('Y-m-d',$endDateInt);    //today

$filename = "urllist.txt";

/** END CONFIG */
if(SHOW_ERRORS){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once('../functions.php');
require_once('../mongodb.php');

if (!file_exists($filename)) {
    die("No Import file present! (named: {$filename})");
}

$contents = file($filename);

$M_DB = new M_DB(MONGO_COLLECTION_MAIN);
$M_DB_C = new M_DB(MONGO_COLLECTION_CLICKS);

$u = 0;
$c = 0;

//lets create some fake url data
foreach($contents as $long_url) {
    $u++; //lets keep the number of added urls
    $newDateInt = mt_rand($startDateInt,$endDateInt);
    $newDate = date('Y-m-d',$newDateInt);

    //number of fake clicks
    $fakeClicks = mt_rand(0,250);

    $url_data = $M_DB->insertUrl($long_url, null, $newDateInt, $fakeClicks);

    //lets generate some fake clicks
    for($i=0;$i<=$fakeClicks;$i++){
        $c++; //lets keep the number of added clicks

        //use $newDateInt as start date as it cannot be before the that creation date!
        $click_date_int = mt_rand($newDateInt,$endDateInt);
        $click_date = date('Y-m-d',$click_date_int);

        $click = array();
        $click['short_url'] = $url_data['short_url'];
        $click['date'] = $click_date; //date to easy group on date!
        $click['created'] = $click_date_int; //timestamp for searching between dates
        $click['referrer'] = (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : '' ;
        $click['client_ip'] = (isset($_SERVER['HTTP_CLIENT_IP']))? $_SERVER['HTTP_CLIENT_IP'] : '' ;

        $M_DB_C->saveExtentedVisits($click);
    }

}
$timestamp = time();
$renamed_file = "{$timestamp}_urllist.txt";
rename($filename, $renamed_file);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "All Done!<br/>";
echo "Added {$u} URLS!<br/>";
echo "Added {$c} Clicks!<br/>";
echo "Renamed the file to {$renamed_file}!!!<br/>";
echo "Have fun with your fake data!!!<br/>";
echo "<i>This script took {$time} seconds to finish</i><br/>";