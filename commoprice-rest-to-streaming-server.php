<?php
// Kaazing Corporation. Enterprise grade, scale, security and support for WebSocket.
// The Inventors of WebSocket

require_once 'vendor/autoload.php';
/**
 * Check dependencies
 */
if( ! extension_loaded('sockets' ) ) {
	echo "This example requires sockets extension (http://www.php.net/manual/en/sockets.installation.php)\n";
	exit(-1);
}

if( ! extension_loaded('pcntl' ) ) {
	echo "This example requires PCNTL extension (http://www.php.net/manual/en/pcntl.installation.php)\n";
	exit(-1);
}

// Initialize Redis
$redis = new Predis\Client();

function milliseconds() {
    $mt = explode(' ', microtime());
    return $mt[1] * 1000 + round($mt[0] * 1000);
};

/**
 * Connection handler
 */
function onConnect( $client ) {
	$pid = pcntl_fork();
	
	if ($pid == -1) {
		 die('could not fork');
	} else if ($pid) {
		// parent process
		return;
	}
	
	$read = '';
	printf( "[%s] Connected at port %d\n", $client->getAddress(), $client->getPort() );

	date_default_timezone_set('UTC');
	
	while( true ) {
		$read = $client->read();
		if( $read != '' ) {
			// $client->send( 'Type HELP for a list of commands' );
		}
		else {
			break;
		}
		
		if( preg_replace( '/[^a-z]/', '', $read ) == 'exit' ) {
			break;
		}
		if( $read === null ) {
			printf( "[%s] Disconnected\n", $client->getAddress() );
			return false;
		}
		else {
			echo "Client IP: {$client->getAddress()} ";
			$start = milliseconds();
			$client->send( imfCommandRouter( $read ) );
			$diff = milliseconds() - $start;
			echo "Round trip response time: {$diff} milliseconds\n";
		}
	}
	$client->close();
	printf( "[%s] Disconnected\n", $client->getAddress() );
	
}

function imfCommandRouter( $request ) {
	echo "Request: {$request}\n";
	$commandLine = explode( ' ', $request );
	if ( count( $commandLine ) < 2 ) {
		return "Too few parameters\n";
	};
	switch (strtolower( $commandLine[0] ) ) {
		case 'list':
			return imfSearch( $commandLine[1] );
			break;
		case 'price':
			return imfPrice( $commandLine[1] );
			break;
		default:
			echo 'Enter LIST or PRICE <mutual fund identifier>';
	}
}

function cachePrice($security_id, $result) {
	global $redis;
	// Create topic to retrieve from cache
	$topic = "kaazing.mutualfund.india.".$security_id;
	$redis->set($topic, $result);
	// Expire cache after 60 seconds
	$ttl = 60 * 1000;
	$redis->pexpire($topic, $ttl);
	return $result;
}

function getCachedPrice($security_id) {
	global $redis;
	$topic = "kaazing.mutualfund.india.".$security_id;
	return $redis->get($topic);
}

function imfPrice( $request ) {
	echo "Price: {$request}\n";	
	$cached = getCachedPrice($request);
	echo $cached;
	if ($cached) {
		return $cached;
	}
	$mashape_key = getenv ( "MASHAPE_KEY" );
	$response = Unirest\Request::post("https://mutualfundsnav.p.mashape.com/",
	  array(
	    "X-Mashape-Key" => $mashape_key,
	    "Content-Type" => "application/json",
	    "Accept" => "application/json"
	  ),
	  "{\"scodes\":[\"{$request}\"]}"
	);
	echo "Response code: {$response->code}\n";

	$result = "";

	foreach ($response->body as $key => $value) {
		$security_id = $key;
		$result .= $key." ".$value->date." ".$value->nav." ".$value->change->value." ".$value->change->percent."     ".$value->fund;
	}	
	cachePrice($security_id, $result);
	return $result;
}

function imfSearch( $request ) {
	echo "Search: {$request}\n";
	$mashape_key = getenv ( "MASHAPE_KEY" );
	$response = Unirest\Request::post("https://mutualfundsnav.p.mashape.com/",
	  array(
	    "X-Mashape-Key" => $mashape_key,
	    "Content-Type" => "application/json",
	    "Accept" => "application/json"
	  ),
	  "{\"search\":\"{$request}\"}"
	);
	echo "Response code: {$response->code}\n";

	$result = "";
	foreach ($response->body as $key => $value) {
		$result .= $value[0]." - ".$value[3]."\n";
	};
	if ( $result  == '' ) { return "Nothing Found"; };
	return $result;
}


require "sock/SocketServer.php";

$server = new \Sock\SocketServer();
$server->init();
$server->setConnectionHandler( 'onConnect' );
$server->listen();
