#!/usr/local/bin/php -q
<?php
// Kaazing Corporation. Enterprise grade, scale, security and support for WebSocket.
// The Inventors of WebSocket

error_reporting(E_ALL);

require('vendor/autoload.php');

use WebSocket\Client;

function milliseconds() {
    $mt = explode(' ', microtime());
    return $mt[1] * 1000 + round($mt[0] * 1000);
};

if( count($argv) < 3 ) {
	echo "Usage: LIST <pattern> or PRICE <mutual fund identifier number>\n";
	exit ( 1 );
};

$client = new Client("ws://localhost:7000/call_pick_up");

$start = milliseconds();
$client->send("{$argv[1]} {$argv[2]}");
$diff = milliseconds() - $start;
echo "{$client->receive()}\n";
echo "Elapsed time: {$diff} milliseconds\n";

// Shutdown gracefully
$client->send("exit");

?>
