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

if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	die('That is not a valid short url');
}
$M_DB = new M_DB();

$shortened_id = (int) $M_DB->getINTFromShortenedURL($_GET['url']);

if(CACHE)
{
	$long_url = file_get_contents(CACHE_DIR . $shortened_id);
	if(empty($long_url) || !preg_match('|^https?://|', $long_url))
	{
		$long_url = $M_DB->getLongUrl($shortened_id);
		@mkdir(CACHE_DIR, 0777);
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	}
}
else
{
	$long_url = $M_DB->getLongUrl($shortened_id);
}

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;