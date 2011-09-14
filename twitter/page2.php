<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
//$sess = new SessionManager();
if(!isset($_SESSION)){
  session_start();
}

$docroot = $_SERVER['DOCUMENT_ROOT'];

require_once $docroot."/twitter/twitteroauth/twitteroauth.php";
require_once $docroot."/twitter/config.php";
require_once $docroot."/models/twextra_model.php";
require_once $docroot."/tw.lib.php";
require_once $docroot."/config.php";
//configuration parameters:
$config_params = Config::getConfigParams();
$consumer_key = $config_params['consumer_key'];
$consumer_secret = $config_params['consumer_secret'];
$oauth_callback = $config_params['oauth_callback'];
$hostname = $config_params['hostname'];

$script_path = $_SERVER['PHP_SELF'] ;
if (isset ( $_SESSION ['message_id'] )) {
	$message_id = $_SESSION ['message_id'];
} else {
	$message_id = '-';
}

   logger($script_path."  Top: ");
   logger($script_path."  CONSUMER_KEY: ", $consumer_key);
   logger($script_path."  CONSUMER_SECRET: ", $consumer_secret);
   logger($script_path."  OAUTH_CALLBACK: ", $oauth_callback);
//..............................................................................//

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
  logger($script_path."  connection object1: ", $connection);
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);//<<<<<
  logger($script_path."  access token1: ", $access_token);//

$http_code = $connection->http_code;
  logger($script_path."  http code1: ", $http_code);//
//$screen_name = $access_token['screen_name'];
$user_id = $access_token['user_id'];

if (($http_code >= 200) && ($http_code < 300)) {
	$model = new TwextraModel ();
	$error = $model->set_tw_access_token ( $access_token ); //<<<
	logger ( $script_path . "  set_tw_access_token: success " ); //
} else {
	$err_message = "twitter_error";
	header ( "Location: $hostname/index.php?error=$err_message" );
	exit ();
}
	
if (! isset ( $_SESSION ['signin_with_twitter'] )) {
	$error2 = $model->update_message_user_id ( $message_id, $user_id ); //<<<<<<<<<<<
	logger ( $script_path . "  message table updated: ", $error2 ); //
} else {
	$error2 = '';
}

if(($error == 'error')||($error2=='error')){
	$err_message=urlencode("Error! Please try again.");
	  header("Location: $hostname/index.php?error=$err_message");
        exit();
}

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';
  /* statuses/update */
   date_default_timezone_set('GMT');

   $status_url = "$hostname/$message_id";
   $prefix = $_SESSION['prefix'];

   $status_twextra = $prefix.$status_url;
   $status_twextra = substr($status_twextra, 0, 140);//make sure max tweet is upto 140 characters
   
   $parameters = array('status' => "$status_twextra");
     logger($script_path."  before twitter post5: ");
	
	if (! isset ( $_SESSION ['signin_with_twitter'] )) {
		$status_obj = $connection->post ( 'statuses/update', $parameters ); //<<<<<<<<<<
	}else{
		$status_obj = $connection->get ( 'statuses/user_timeline', array() ); //<<<<<<<<<<
		$status_obj = $status_obj[0];
	}
   
     logger($script_path."  status object: ", $status_obj);
   //store user profile info into database (page2.php is accessed for new users only):
	if ((!empty($user_id)) && (!empty($status_obj->user->id))) {
		$error3 = $model->update_user_info ( $user_id, $status_obj );//<<<<<<<<
     logger($script_path."  after update_user_info: ", $error3);	
	}else{
		//$err_message=urlencode("Error! Please try again.");
		//header("Location: $hostname/index.php?error=$err_message");
        //exit();
	}
   
   $_SESSION['profile_image_url'] = $status_obj->user->profile_image_url;
   $_SESSION['user'] = $status_obj->user->screen_name;
     logger($script_path."  after twitter post5: ");
   //$_SESSION['user']=$access_token['screen_name']; //
	
	if (($error3 != 'error') && (! isset ( $_SESSION ['signin_with_twitter'] ))) {
   		$_SESSION['update_tw']=1;
		header ( "Location: $status_url" );
		exit ();
	} else {
		unset ( $_SESSION ['signin_with_twitter'] );
		header ( "Location: $hostname" );
		exit ();
	}

} else {
  /* Save HTTP status for error dialog on connnect page.*/
	$err_message = "twitter_error";
  header("Location: $hostname/index.php?error=$err_message");
  exit;
}
