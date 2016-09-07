#!/usr/local/bin/php -q
<?php
error_reporting(E_ALL);

require('vendor/autoload.php');
 
//publish.php    
$redis = new Predis\Client();

$channel2 = "channel2";
$userdata = "Hello World2!";

$redis->publish($channel2, $userdata);
 
?>