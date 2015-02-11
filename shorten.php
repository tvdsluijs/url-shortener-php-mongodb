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

$url_to_shorten = null;
if(isset($_REQUEST['longurl'])){
	$url_to_shorten = (string) trim($_REQUEST['longurl']);
}

$shortUrl = null;
if(isset($_REQUEST['shorturl']) && $_REQUEST['shorturl'] != '') {
	$shortUrl = (string) trim($_REQUEST['shorturl']);
}

if(!empty($url_to_shorten) && preg_match('|^https?://|', $url_to_shorten))
{
	// check if the client IP is allowed to shorten
	if(isset($LIMIT_SHORTEN_TO_IP) && !in_array($_SERVER['REMOTE_ADDR'], $LIMIT_SHORTEN_TO_IP))
	{
		die('Sorry, you are not allowed to shorten URLs with this service.');
	}
	
	// check if the URL is valid
	if(CHECK_URL)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_to_shorten);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		$response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($response_status == '404')
		{
			die(URL_NOT_RESPONSIVE_MESSAGE);
		}
	}

	$M_DB = new M_DB(MONGO_COLLECTION_MAIN);

	$url_data = $M_DB->getLongUrl($shortUrl);

	if(isset($url_data) && isset($url_data['long_url'])){
		die(SHORT_URL_EXISTS);
	}

	$url_data = $M_DB->getShortUrl($url_to_shorten);

	if(!isset($url_data['short_url']) || $url_data['short_url'] == '')
	{
		$url_data = $M_DB->insertUrl($url_to_shorten, $shortUrl);
	}
	echo BASE_HREF . $url_data['short_url'];
}else{
	die('URL_NOT_RIGHT');
}
