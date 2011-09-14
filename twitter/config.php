<?php

/**
 * @file
 * A single location to store configuration.
 */
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
//configuration parameters:
$config_params = Config::getConfigParams();
$consumer_key = $config_params['consumer_key'];
$consumer_secret = $config_params['consumer_secret'];
$oauth_callback = $config_params['oauth_callback'];

define('CONSUMER_KEY', $consumer_key);
define('CONSUMER_SECRET', $consumer_secret);
define('OAUTH_CALLBACK', $oauth_callback);
