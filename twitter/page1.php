<?php
if (session_id() == "") session_start();
/* Start session and load library. */

//$sess = new SessionManager();


$docroot = $_SERVER['DOCUMENT_ROOT'];
require_once  $docroot.'/twitter/twitteroauth/twitteroauth.php';
require_once  $docroot.'/twitter/config.php';
//require_once  $docroot."/sessions.php"; 
require_once $docroot."/tw.lib.php";
//configuration parameters:
$config_params = Config::getConfigParams();
$consumer_key = $config_params['consumer_key'];
$consumer_secret = $config_params['consumer_secret'];
$oauth_callback = $config_params['oauth_callback'];

logger("Tw/Page1 Top:");

$_SESSION['message'] = $_REQUEST['message'];
$_SESSION['prefix']  = $_REQUEST['prefix'];

/* Build TwitterOAuth object with client credentials. */
$connection = new TwitterOAuth($consumer_key, $consumer_secret);
 
/* Get temporary credentials. */
$request_token = $connection->getRequestToken($oauth_callback);

logger("Tw/Page1: After Request Token:");

/* Save temporary credentials to session. */
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 
/* If last connection failed don't display authorization link. */
switch ($connection->http_code) {
  case 200:
    /* Build authorize URL and redirect user to Twitter. */
    $url = $connection->getAuthorizeURL($token);
    header('Location: ' . $url); 
    break;
  default:
    /* Show notification if something went wrong. */
    echo 'Could not connect to Twitter. Refresh the page or try again later.';
}

?>
