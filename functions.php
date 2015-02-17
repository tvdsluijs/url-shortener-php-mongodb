<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 03/02/15
 * Time: 21:52
 */

defined('_SHORT_INCLUDE_ONLY') or die(header('HTTP/1.0 404 Not Found'));

if(SHOW_ERRORS){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

/**
 * function to check if ip is okay!
 * @param $ips
 * @return bool
 */
function allowedIP($ips){

    //check if there is a x forwarded remote address ip (varnish nginx)
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
        if(in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $ips)){
            return true;
        }else{
            return false;
        }
    }

    //check if there is a normal remote address ip
    if(isset($_SERVER['REMOTE_ADDR'])){
        if(in_array($_SERVER['REMOTE_ADDR'], $ips)){
            return true;
        }else{
            return false;
        }
    }
    //nothing? Then false!
    return false;
}

/**
 * Like a var dump but better for arrays
 * @param $arg
 * @param string $title
 */
function print_pre($arg, $title = '')
{
    $bt = debug_backtrace();
    $file = $bt[0]['file'];
    $line = $bt[0]['line'];
    echo "<pre>[$file:$line]\n";
    if ($title) {
        echo "$title:";
    }
    if (is_array($arg)) {
        $n = count($arg);
        echo "[$n elements] ";
    }
    print_r($arg);
    echo "</pre>";
}

/**
 * function to show size of file
 * @param $size
 * @param int $precision
 * @return string
 */
function formatBytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'k', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}