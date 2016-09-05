<?php

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
			$client->send( imfCommandRouter( $read ) );
		}
	}
	$client->close();
	printf( "[%s] Disconnected\n", $client->getAddress() );
	
}

function imfCommandRouter( $request ) {
	echo "Request: {$request}\n";
	$commandLine = explode( ' ', $request );
	if ( count( $commandLine ) < 2 ) {
		break;
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

function imfPrice( $request ) {
	echo "Price: {$request}\n";
	$mashape_key = 'eMgiTAYGa2mshZLjB48mo0BN2rmap1T40IdjsnwOoaoyOjSFNv';
	$response = Unirest\Request::post("https://mutualfundsnav.p.mashape.com/",
	  array(
	    "X-Mashape-Key" => $mashape_key,
	    "Content-Type" => "application/json",
	    "Accept" => "application/json"
	  ),
	  "{\"scodes\":[\"{$request}\"]}"
	);
	$result = '';
	foreach ($response->body as $key => $value) {
		$result .= $key." ".$value->date." ".$value->nav." ".$value->change->value." ".$value->change->percent."     ".$value->fund;
	}	
	return $result;
}

function imfSearch( $request ) {
	echo "Search: {$request}\n";
	$mashape_key = 'eMgiTAYGa2mshZLjB48mo0BN2rmap1T40IdjsnwOoaoyOjSFNv';
	$response = Unirest\Request::post("https://mutualfundsnav.p.mashape.com/",
	  array(
	    "X-Mashape-Key" => $mashape_key,
	    "Content-Type" => "application/json",
	    "Accept" => "application/json"
	  ),
	  "{\"search\":\"{$request}\"}"
	);

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
