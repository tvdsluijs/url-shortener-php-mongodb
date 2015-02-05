<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 03/02/15
 * Time: 21:52
 */

define('MONGO_DB_NAME', 'urls');

define('MONGO_TABLE_NAME', 'short_urls');

// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// change to limit short url creation to a single IP
define('LIMIT_TO_IP', array($_SERVER['REMOTE_ADDR']));

// change to TRUE to start tracking referrals
define('TRACK', TRUE);

// check if URL exists first
define('CHECK_URL', FALSE);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', TRUE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');

//if so, show all errors... on live site set FALSE !!
define('SHOW_ERRORS', TRUE);