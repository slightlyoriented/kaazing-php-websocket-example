#!/usr/local/bin/php -q
<?php
// Kaazing Corporation. Enterprise grade, scale, security and support for WebSocket.
// The Inventors of WebSocket

error_reporting(E_ALL);

require('vendor/autoload.php');

use WebSocket\Client;

if( count($argv) < 3 ) {
	echo "Usage: LIST <pattern> or PRICE <mutual fund identifier number>\n";
	exit ( 1 );
};

$client = new Client("ws://localhost:7000/call_pick_up");

$client->send("{$argv[1]} {$argv[2]}");
echo "{$client->receive()}\n";

// Shutdown gracefully
$client->send("exit");

?>
