Example for PHP WebSocket using Kaazing's WebSocket Gateway and a Multi Threaded Socket Server based on pcntl_fork()
====================

Requirements
---------------------
* PHP5	
* sockets (http://www.php.net/manual/en/sockets.installation.php)
* pcntl (http://www.php.net/manual/en/pcntl.installation.php)
* pcntl on the Mac use Homebrew
* cd to php-ws
* php composer.phar install

Run Echo Example
---------------------
In the root directory
* ./kaazing-gateway-community-5.1.0/bin/gateway.start 
* php server.php
* In less than a minute you should see the Kaazing gateway connect to the PHP server, please wait until this happens
* php php-ws/ws-client.php
* You should see 10 requests come through in the terminal running server.php
* You should also see this echoed back to the terminal running ws-client.php

Run Indian Mutual Fund Example
------------------------------
Steps
* Sign up at https://market.mashape.com/nviror/mutual-fund-nav-india for the API and get a key
* Add the key to your environment variables with `export MASHAPE_KEY=<Your Key>`

In the root directory
* Start the Kaazing gateway with `./kaazing-gateway-community-5.1.0/bin/gateway.start` 
* Start the PHP server which will get prices and descriptions from the API with `php indian-mutual-fund-server.php`
* In less than a minute you should see the Kaazing gateway connect to the PHP server, please wait until this happens, the Kaazing gateway sets up a pool of initial connections to improve performance
* Get a list of mutual funds with *birla* in the description `php php-ws/indian-mutual-fund-server.php LIST birla`
* You should see a list of 10 funds with their identifiers on the left and description on the right
* Pick one of them and run `php php-ws/indian-mutual-fund-server.php PRICE <identifier>`

Run Indian Mutual Fund Example with Caching
-------------------------------------------
Steps
* Install Redis
* Sign up at https://market.mashape.com/nviror/mutual-fund-nav-india for the API and get a key
* Add the key to your environment variables with `export MASHAPE_KEY=<Your Key>`

In the root directory
* Start the Kaazing gateway with `./kaazing-gateway-community-5.1.0/bin/gateway.start` 
* Start the PHP server which will get prices and descriptions from the API with `php indian-mutual-fund-caching-server.php`
* In less than a minute you should see the Kaazing gateway connect to the PHP server, please wait until this happens, the Kaazing gateway sets up a pool of initial connections to improve performance
* Get a list of mutual funds with *birla* in the description `php php-ws/indian-mutual-fund-server.php LIST birla`
* You should see a list of 10 funds with their identifiers on the left and description on the right
* Pick one of them and run `php php-ws/indian-mutual-fund-server.php PRICE <identifier>`
* Run it again with the same identifier and you will see how much faster it returns
* With a combination of WebSocket and caching amazing response times are possible!
* This particular API is ragte limited to 1 request per second, however, with this pattern, you can support more users
* Since this is mutual fund nav data, which changes once a day, you would set the cache timer to a much higher number, or a time of day