#!/usr/local/bin/php -q
<?php
error_reporting(E_ALL);

require('vendor/autoload.php');

use WebSocket\Client;

# $client = new Client("ws://echo.websocket.org/");
# $client = new Client("ws://localhost:8000/echo/");
$client = new Client("ws://localhost:7000/call_pick_up");

// echo $client->receive();

for ($x = 0; $x <= 10; $x++) {
    echo "Iteration: $x\n";
		$client->send("Hello WebSocket.org {$x}! ");
		echo $client->receive();
		echo "\n";
}

// Shutdown gracefully
$client->send("exit");

?>
