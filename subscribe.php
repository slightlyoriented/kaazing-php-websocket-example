#!/usr/local/bin/php -q
<?php
error_reporting(E_ALL);


$redis = new Redis();

$redis->connect('127.0.0.1', 6379, 9999);

$channel2 = "channel2";
$sub = $redis->subscribe(array($channel2), "messages");

function messages($redis, $chan, $message){
    var_dump($chan);
    var_dump($message);
}

while (1) {}
?>
