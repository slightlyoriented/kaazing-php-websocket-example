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

function send_data($commands, $client) {
	$start = milliseconds();
	$client->send("{$commands[1]} {$commands[2]}");
	$diff = milliseconds() - $start;
	return $diff;
}


$client = new Client("ws://localhost:7000/call_pick_up", array('timeout' => 10));

// Send data and trap for time out
try {
	$diff = send_data($argv, $client);
	echo "{$client->receive()}\n";
	echo "Round trip response time: {$diff} milliseconds\n";
} catch (Exception $e) {
	if ($e instanceof WebSocket\ConnectionException) {
		echo "Timed out.\n";
	} else { 
		throw $e; 
	}
} finally {
	$client->send("exit");
}

// Shutdown gracefully
$client->send("exit");

?>
