Example for PHP WebSocket using Kaazing's WebSocket Gateway and a Muilti Threaded Socket Server based on pcntl_fork()
====================

Requirements
---------------------
* PHP5	
* sockets (http://www.php.net/manual/en/sockets.installation.php)
* pcntl (http://www.php.net/manual/en/pcntl.installation.php)
* pcntl on the Mac use Homebrew
* cd to php-ws
* php composer.phar install

Run
---------------------
In the root directory
* ./kaazing-gateway-community-5.1.0/bin/gateway.start 
* php server.php
* In less than a minute you should see the Kaazing gateway connect the server
* php php-ws/ws-client.php
* You should see 10 requests come through in the terminal running server.php
* You should also see this echoed back to the terminal running ws-client.php
