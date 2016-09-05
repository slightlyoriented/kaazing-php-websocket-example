<?php

error_reporting(E_ALL);
require('vendor/autoload.php');

$redis = new Predis\Client();

$redis->set('library', 'predis');
$retval = $redis->get('library');
echo "{$retval}\n";

$topic = 'kaazing.mutualfund.india.118533';
$redis->set(
	$topic, 
	'"118533": {
    "fund": "Franklin India PRIMA FUND - Direct - Growth",
    "date": "02-Jun-2016",
    "nav": "730.5176",
    "change": {
      "value": "+5.09",
      "percent": "+0.70%"
    },
    "repurchase": "723.2124",
    "category": "Open Ended",
    "fund_type": "Growth",
    "amc": "Franklin Templeton Mutual Fund");}'
);

$mfval = $redis->get($topic);
echo "{$mfval}\n";

//Expire topic
$ttl = 1000;
$redis->pexpire($topic, $ttl);
echo "Exists?: {$redis->exists($topic)}\n";
sleep(1.2);
echo "Exists?: {$redis->exists($topic)}\n";

?>
