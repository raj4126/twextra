<?php 

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";

function header_html($prefix = '') {
	
//if (session_id () == "") session_start ();

	$config_params = Config::getConfigParams ();
	$hostname = $config_params ['hostname'];
	$tweet_size_max = $config_params ['tweet_size_max'];
	$prefix_size_max = $config_params ['prefix_size_max'];
	$docroot = $config_params ['docroot'];
	$google_analytics = $config_params ['google_analytics'];
	$css = $config_params ['css'];
	$fb_app_id = $config_params ['fb_app_id'];
	
	if($hostname=='http://twextra.com'){
	    $host='Twextra';
	}else if($hostname=='http://twetest.com'){
	    $host='Twetest';
	}else{
	    header("Location:$hostname?error=Hostname not recognized.");
	    exit();
	}
	
	$header = "<head>\n";
	$header .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
	
	$header .= '<meta http-equiv="Pragma" content="no-cache" />' . "\n";
	$header .= '<meta http-equiv="Cache-Control" content="no-store, must-revalidate, no-cache, private, 
max-age=0, post-check=0, pre-check=0" />' . "\n";
	$header .= '<meta http-equiv="Expires" content="Fri, 1 Jan 2010 01:00:00 GMT" />' . "\n";
	
	$header .= "<meta name='description' content='Twextra is a service for sharing long, rich-text messages with images 
on Twitter, LinkedIn and more. Twextra also provides translation for messages in multiple languages.' /> 
<meta name='keywords' content='Tweets, Twitter, LinkedIn, Instant Messaging' />\n";
	
	$header .= "<link rel='stylesheet' type='text/css' href='" . $css . "' />\n";
	if (isset ( $prefix ) && trim ( $prefix) != '') {
		$header .= '<title>Twextra - ' . $prefix . '</title>' . "\n";
		//$_SESSION['prefix']= '';//initialize prefix after consuming it, so as to be ready for next tweet
	} else {
		$header .= '<title>Twextra - When you NEED more than 140 characters</title>' . "\n";
	}
	$header .= "<script type='text/javascript' src='/scripts/jquery/jquery.js' ></script>\n";
	$header .= "<link rel='shortcut icon' type='image/x-icon' href='/favicon.ico' />\n";
	
	$header .= "<script type='text/javascript' src='/lib/twextra.lib.js'></script>\n";
	
	$header .= $google_analytics;
	
	$header .= "<meta property=\"og:site_name\" content=\"$host\">
		<meta property=\"fb:app_id\" value=\"$fb_app_id\">
		<meta property=\"fb:admins\" content=\"$host\">";
	
	$header .= "</head>\n";
	
	return $header;
}

?>