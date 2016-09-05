Example for PHP WebSocket using Kaazing's WebSocket Gateway and a Multi Threaded Socket Server based on pcntl_fork()
====================

These are a series of examples getting progressively more functional of using WebSocket on a client, standalone or a browser to connect to backend services without the overhead of polling. The last example demonstrates how a REST API can benefit from using WebSocket using caching strategies.

The first example simply show how to write a PHP server which can use WebSocket provided by the Kaazing WebSocket Gatway (Free Community Edition). The second example, show how to connect to an API and sets the stage for the third example which shows the true speed and power of WebSocket.

Kaazing (https://kaazing.com) also offers the Enterprise Edition which has advanced security, high availability, scaling and other features. A connection limited free version can be downloaded from http://kaazing.com/download .

Requirements
---------------------
* PHP5	
* sockets (http://www.php.net/manual/en/sockets.installation.php)
* pcntl (http://www.php.net/manual/en/pcntl.installation.php)
* pcntl on the Mac use Homebrew
* `php composer.phar install`

Run Echo Example
---------------------
In the root directory
* `./kaazing-gateway-community-5.1.0/bin/gateway.start` 
* `php server.php`
* In less than a minute you should see the Kaazing gateway connect to the PHP server, please wait until this happens
* `./ws-client.php`
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
* Get a list of mutual funds with *birla* in the description `./indian-mutual-fund-client.php LIST birla`
* You should see a list of 10 funds with their identifiers on the left and description on the right
* Pick one of them and run `./indian-mutual-fund-client.php PRICE <identifier>`

Run Indian Mutual Fund Example with Caching
-------------------------------------------
Steps
* Install Redis
* Sign up at https://market.mashape.com/nviror/mutual-fund-nav-india for the API and get a key
* Add the key to your environment variables with `export MASHAPE_KEY=<Your Key>`

In the root directory
* Install Redis support with `php composer.phar install`
* Start the Kaazing gateway with `./kaazing-gateway-community-5.1.0/bin/gateway.start` 
* Start the PHP server which will get prices and descriptions from the API with `php indian-mutual-fund-caching-server.php`
* In less than a minute you should see the Kaazing gateway connect to the PHP server, please wait until this happens, the Kaazing gateway sets up a pool of initial connections to improve performance
* Get a list of mutual funds with *birla* in the description `./indian-mutual-fund-client.php LIST birla`
* You should see a list of 10 funds with their identifiers on the left and description on the right
* Pick one of them and run `./indian-mutual-fund-client.php PRICE <identifier>`
* Run it again with the same identifier and you will see how much faster it returns

Example
Without Caching
```
131670 02/09/2016 45.11 +0.10 +0.22%     Birla Sun Life Dynamic Asset Allocation Fund - Direct Plan - Growth Option
Round trip response time: 1.2733249664307 seconds
```
With Caching
```
131670 02/09/2016 45.11 +0.10 +0.22%     Birla Sun Life Dynamic Asset Allocation Fund - Direct Plan - Growth Option
Round trip response time: 0.010161161422729 seconds
```

* The cache is expired after 60 seconds
* With a combination of WebSocket and caching amazing response times are possible!
* This particular API is rate limited to 1 request per second, however, with this pattern, you can support more users
* Since this is mutual fund nav data, which changes once a day, you would set the cache timer to a much higher number, or a specific time of day

Typical Errors
--------------
* Most common is to leave the Gateway running while trying to start another one
* Same for the PHP server
* Get rate limited by the API shown with a tme out message
* Forgetting to set the environment variable MASHAPE_KEY with your key so the API will work.
* An API key error is shown as a 401 response on the PHP server output log
