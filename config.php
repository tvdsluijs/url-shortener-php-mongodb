<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * email: info@ts-intermedia.nl
 * Date: 16/03/15
 * Please donate a coffee, to keep me coding on this url shortner !!!
 * Bitcoin : 18aJm8qj47iafT5gTgHrBAXzboDS8jEfZM
 * Paypal : http://snurl.eu/coffee
 */
defined('_SHORT_INCLUDE_ONLY') or die(header('HTTP/1.0 404 Not Found'));

/**
 * ---------------------- make changes below this line --------------------
 */

// change to limit short url creation to a single or more IP's (just add an array)
//$LIMIT_SHORTEN_TO_IP = array($_SERVER['REMOTE_ADDR']); //check on IP array!
$LIMIT_SHORTEN_TO_IP = NULL; // dont wanna check on IP ?

//you should limit access !!!
$backend_ip = NULL;
$LIMIT_BACKEND_TO_IP = $backend_ip; //check on IP array!

define('SITE_NAME', 'your site name!');

define('GOOGLE_ANALYTICS_ID', 'UA-XXXXXXX-XX'); //UA-xxxxxx-xx set to NULL when you don't wanna use

define('MONGO_DB_NAME', 'urls');

define('SITE_TITLE', 'Your own Url Shortener');
define('SITE_SUBTITLE', 'Your own Url Shortener');

define('SITE_AUTHOR', 'Your own author name');
define('SITE_DECRIPTION', 'Your own site description');

define('NOT_ALLOWED_TO_SHORTEN', 'Sorry, you are not allowed to shorten URLs with this service.');
define('HOUSTON_PROBLEM', 'Houston, we have a problem!');
define('URL_NOT_RIGHT', 'Unable to shorten that link. It is not a valid url.');

//output definition
define('PLAIN_TEXT', 0);
define('JSON', 1);

//what are the collection names?
define('MONGO_COLLECTION_MAIN', 'short_urls'); //should be the main collection
define('MONGO_COLLECTION_CLICKS', 'short_url_click'); //should be the click collection

define('FAKE_URL_FRONTPAGE_COUNT', TRUE); //just for the few couple of months to show some fake counts
define('SHOW_URL_FRONTPAGE_COUNT', TRUE); //show frontpage counts?

define('SHOW_OWN_SHORT_FIELD', TRUE); //show frontpage shorter?
$SHOW_OWN_SHORT_FIELDBY_IP = NULL; //array(); //show frontpage short? //or NULL for not!

define('BACKGROUND_IMAGE', 'link to your own image'); //background image

// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/'); //I guess no changes needed

// change to TRUE to start tracking referrals
define('TRACK_SIMPLE', TRUE);
define('TRACK_EXTENDED', TRUE);

// check if URL exists first
define('CHECK_URL', FALSE);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', FALSE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');

//if so, show all errors... on live site set FALSE !!
define('SHOW_ERRORS', TRUE);

define('URL_NOT_RESPONSIVE_MESSAGE', 'We have a unresponsive URL! 404!!!');
define('SHORT_URL_EXISTS', 'Sorry, that short URL already exists!!');