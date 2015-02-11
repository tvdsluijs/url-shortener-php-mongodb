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

function formatBytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'k', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}