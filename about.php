<?php
if (session_id () == "") session_start ();
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/controllers/twextra_controller.php";
require_once $docroot . "/banner.php";
require_once $docroot . "/header_html.php";

$screen_name = $_SESSION['user'];

require_once $docroot . "/tw.lib.php";

//validate access;
validate_access_twetest();

display_about($screen_name);

//.........................................................................................
function display_about($screen_name='') {
	
	//configuration parameters:
	$config_params = Config::getConfigParams ();
	$css = $config_params ['css'];
	$tweet_size_max = $config_params ['tweet_size_max'];
	$tweet_size_max_google = $config_params ['tweet_size_max_google'];
	$hostname = $config_params ['hostname'];
	$doctype = $config_params ['doctype'];
	$html_attribute = $config_params ['html_attribute'];
	$banner = banner('', 'banner'); //(user, banner_class)
	$footer = $config_params ['footer']; //
	$docroot = $config_params ['docroot'];
	$godaddy_analytics = $config_params ['godaddy_analytics'];
	$debug = $config_params ['debug'];
	$enable_stats = $config_params ['enable_stats'];
	
	$script_path = __FUNCTION__;
	
	//save logs
	if ($enable_stats) {
		$model = new TwextraModel (); //
		$model->saveStat ();
	}
	
	$header = header_html ( $prefix ); //
	//..........................................................

	header ( "Pragma: no-cache" );
	header ( "cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
	header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past

	$message = '';
	$message .= $doctype;
	$message .= "<html $html_attribute >\n";
	
	$message .= $header;
	
	$message .= "<body>\n";
	
	$message .= "<div class='p5_wrapper'>\n";
	$message .= "<div class='p5_page'>\n";
	
	$message .= $banner;  
	
	$message .= "<div style='margin-left:auto;margin-right:auto;margin-bottom:20px;width:768px;font-size:0.8em;' >\n"; //p5_main
	
	$message .= "<h3>About Twextra</h3>";
	$message .= "Ever wish you could write a message longer than 140 characters on
Twitter or Linkedin?  Well, that's exactly what Twextra enables,
plus much more.  Take a look at Twextra's features:
<ul style='margin:0px 0px 10px 25px'>
<li>
<span style='background-color:yellow;'>Super-size</span> - With Twextra you can create huge messages of up to
<i>100,000 characters</i>.
</li>

<li>
<span style='background-color:yellow;'>Rich-text</span> - Create messages with formatting such as<i> bold, italic,
underline, colored-text, emoticons, </i>etc.
</li>
<li>
<span style='background-color:yellow;'>Multi-media</span> - Embed <i>videos</i> and <i>images</i> into your messages to enable more interesting communication.
</li>
</ul>

Give Twextra a try today!";
	$message .= "<br style='clear:both;' />";
	
	$message .= "</div>\n"; //p5_main

	$message .= $footer;
	$message .= "</div>\n"; //page
	$message .= "</div>\n"; //wrapper

	$message .= $godaddy_analytics;
	
	//$message .= phpinfo();
	
	$message .= "</body>\n</html>\n";
	
	echo $message;
	
} 


?>