<?php
/**
 * Original by Brian Cray
 * http://briancray.com/posts/free-php-url-shortener-script/
 *
 * Rewritten by Theo van der Sluijs for usage with MongoDB
 * Date: 03/02/15
 * Time: 21:52
 */
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
if(isset($_REQUEST['short'])) {
	$shortUrl = (string)trim($_REQUEST['short']);
}

if(!empty($url_to_shorten) && preg_match('|^https?://|', $url_to_shorten))
{

	// check if the client IP is allowed to shorten
	if(!isset($LIMIT_TO_IP) && in_array($_SERVER['REMOTE_ADDR'], $LIMIT_TO_IP))
	{
		die('You are not allowed to shorten URLs with this service.');
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
			die('Not a valid URL');
		}
		
	}

	$M_DB = new M_DB();

	$shortened_url = $M_DB->getShortUrl($url_to_shorten);

	if(empty($already_shortened))
	{
		$shortened_url = $M_DB->insertUrl($url_to_shorten, $shortUrl);
	}
	echo BASE_HREF . $shortened_url;
}else{
	echo 'nothing to do!';
}
