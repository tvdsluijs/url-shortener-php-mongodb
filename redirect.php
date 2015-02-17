<?php
/**
 * Original by Brian Cray
 * http://briancray.com/posts/free-php-url-shortener-script/
 *
 * Rewritten by Theo van der Sluijs for usage with MongoDB
 * Date: 03/02/15
 * Time: 21:52
 */

define('_SHORT_INCLUDE_ONLY', 1);

require_once('config.php');
require_once('functions.php');
require_once('mongodb.php');

if(SHOW_ERRORS){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

if(!isset($_GET['url'])){ //no url!
	die('There\'s no short url');
}

if(isset($_GET['url']) && !preg_match('/([0-9a-zA-Z-_])/', $_GET['url']))
{
	die('That is not a valid short url');
}
$M_DB = new M_DB(MONGO_COLLECTION_MAIN);

//$shortened_id = (int) $M_DB->getINTFromShortenedURL($_GET['url']);

$url_data = $M_DB->getLongUrl($_GET['url']);

if(isset($url_data['long_url']) && $url_data['long_url'] != ''){
	//keep number of visits?
	if (TRACK_SIMPLE) {
		$M_DB->saveVisits($url_data); //save simple visists
	}
	if(TRACK_EXTENDED){
		$timestamp = time();
		$today = date('Y-m-d');
		$click = array();
		$click['short_url'] = $url_data['short_url'];
		$click['date'] = $today; //date to easy group on date!
		$click['created'] = $timestamp; //timestamp for searching between dates
		$click['referrer'] = (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : '' ;
		$click['client_ip'] = (isset($_SERVER['HTTP_CLIENT_IP']))? $_SERVER['HTTP_CLIENT_IP'] : '' ;

		$M_DB = new M_DB(MONGO_COLLECTION_CLICKS);
		$M_DB->saveExtentedVisits($click);
	}
}


header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $url_data['long_url']);
exit;