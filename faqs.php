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

display_faqs($screen_name);

//.........................................................................................
function display_faqs($screen_name='') {
	
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
	$message .= "<h3>Frequently Asked Questions</h3>"; 

	$message .= "
<ol style='clear:both' >
<li> Are mobile devices supported?
<p>Yes, most mobile devices are supported including iPhone, Blackberry etc.
If your mobile device is not supported, send us an email at contact@twextra.com.
<p>

<li>Is rich text supported on mobile devices?</li>
<p>
Rich text is only supported on desktop/laptop computers, or through
our API.
</p>
<li>Is cookie support required in the browser?</li>
<p>Yes, our application requires that the cookies be enabled in the browser.
</p>";
	
//<li>Why does the language translation not work?</li>
//<p>You must enable UTF-8 (Unicode) encoding in your browser to use the language translation feature. 
//</p>
$message .= "
<li>Can I post messages in languages other than English?</li>
<p> Yes, you can, provided you have enabled UTF-8 support in your browser.
</p>

</ol>

";

	$message .= "<br style='clear:both;' />";
	
	$message .= "</div>\n"; //p5_main

	$message .= $footer;
	$message .= "</div>\n"; //page
	$message .= "</div>\n"; //wrapper

	$message .= $godaddy_analytics;
	$message .= "</body>\n</html>\n";
	
	echo $message;
	
} 


?>