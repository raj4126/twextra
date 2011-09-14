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
$data = $_REQUEST;

display_api_support($screen_name, $data);

//.........................................................................................
function display_api_support($screen_name='', $data) {
	
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
	
	$email = '';
	$firstname = '';
	$lastname = '';
	$application = '';
	$company = '';
	$web_site = '';
	$twextra_key = '';
	$error = '';
	$error_email = '';
	$error_firstname = '';
	$error_lastname = '';
	$error_application = '';
	$error_company = '';
	$error_web_site = '';
	
	//save logs
	if ($enable_stats) {
		$model = new TwextraModel (); //
		$model->saveStat ();
	}
    
    if($screen_name == '' && $data['submit'] == 'Submit'){
    	$error = "<h2 style='color: red' >In order to get Twextra key, please authenticate with Twitter by pressing 
    	the 'Sign in with Twitter' button at the top right of this page. </h2>";
    } 
    if($screen_name != '' && $data['submit'] == 'Submit'){
        //store user data->get twextra key -> display key -> suppress the form
        $email = $data ['email'];
        $firstname = $data ['firstname'];
        $lastname = $data ['lastname'];
        $application = $data['application'];
        $company = $data ['company'];
        $web_site = $data ['web_site'];
    }
    
	if($data['clear']=='Clear'){
		$email=$firstname=$lastname=$application=$company=$web_site='';
	}
		
	//validate user input
	if ($data ['submit'] == 'Submit') {
		$email_pattern = "/^.+@.+\..+/";
		$email_validate = preg_match ( $email_pattern, $email );
		
		if (! $email_validate) {
			$error_email = 1;
		}
		if (empty($firstname)) {
			$error_firstname = 1;
		}
		if (empty($lastname)) {
			$error_lastname = 1;
		}
		if (empty($application)) {
			$error_application = 1;
		}
		if (empty($company)) {
			$error_company = 1;
		}
		if (empty($web_site)) {
			$error_web_site = 1;
		}
	}
	
	if($error_email){
		$api_email = 'api_error';
	}else{
		$api_email = '';
	}
	if($error_firstname){
		$api_firstname = 'api_error';
	}else{
		$api_firstname = '';
	}
	if($error_lastname){
		$api_lastname = 'api_error';
	}else{
		$api_lastname = '';
	}
	if($error_application){
		$api_application = 'api_error';
	}else{
		$api_application = '';
	}
	if($error_company){
		$api_company = 'api_error';
	}else{
		$api_company = '';
	}
	if($error_web_site){
		$api_web_site = 'api_error';
	}else{
		$api_web_site = '';
	}
	
	if(($api_email || $api_firstname || $api_lastname || $api_application || $api_company || $api_web_site)&& empty($error)){
		$error = "<span class='api_error'>Please fill in info for the fields marked in red.</span>";
	}
    
    
    
    if(!$error_email && !empty($firstname) && !empty($lastname) && !empty($application) && 
             !empty($company) && !empty($web_site) && $data['submit']=='Submit'){
    	$model = new TwextraModel();
    	$twextra_key = $model->get_twextra_key($email, $firstname, $lastname, $application, $company, $web_site, $screen_name);
    	$model->update_api_tables($email, $firstname, $lastname, $application, $company, $web_site, $screen_name, $twextra_key);
    	mail('contact@twextra.com', 'New Twextra Key generated', "A New Twextra Key has been generated.");
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
	$message .="<h3>API Support</h3>";
	$message .= "API support is available for posting messages to Twitter using Twextra's  RESTful API.
Please fill out the following form to get your Twextra key.";

    $message .= "<br /><br />";

	if($twextra_key != ''){
		//$email=$firstname=$lastname=$application=$company='';
	}
	$message .= "<div>
	  <form method='POST' action='api_support.php'>
	                  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	              <span class='$api_email'>Email:</span> <input type='text' name='email' value='$email' size='40' maxlength= '100' /></p>
	     <p>&nbsp;<span class='$api_firstname'>First Name:</span> <input type='text' name='firstname' value = '$firstname'  size='40' maxlength= '100' /></p>
	     <p>&nbsp;<span class='$api_lastname'>Last Name:</span> <input type='text' name='lastname' value = '$lastname'  size='40' maxlength= '100' /></p>
	     <p>&nbsp;<span class='$api_application'>Application:</span> <input type='text' name='application' value = '$application'  size='40' maxlength= '100' /></p>
	     <p>&nbsp;&nbsp;&nbsp;<span class='$api_company'>Company:</span> <input type='text' name='company' value = '$company'  size='40' maxlength= '100' /></p>
	     <p>&nbsp;&nbsp;&nbsp;&nbsp;<span class='$api_web_site'>Web-Site:</span> <input type='text' name='web_site' value = '$web_site'  size='40' maxlength= '100' /></p>
	     <p>
	     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	     <input type='submit' name='submit' value='Submit' />&nbsp;&nbsp;&nbsp;&nbsp;
	     <input type='submit' name='clear' value='Clear' /></p> 
	  </form>
	                  
	</div>";
	
	if($error != ''){
		$message .= $error;
	}
	
	if($twextra_key != ''){
		$message .= "<h2 style='color: blue' >Thank you for registering your application. Your Twextra Key is: $twextra_key</h2>";
	}
	
	$message .= "<h3>Overview</h3>  

The Twextra Twitter API lets you post large messages (upto 100,000 characters) to Twitter. It is used with normal HTTP POST 
requests.  The API uses Twitter's OAuth method to authenticate the identity of a Twitter user.</p>";
	
	$message .= "<h3>Method: upload </h3>

<h3>URL: http://twextra.com/router.php </h3>


<h3 class='api_h3' >Fields</h3>
<pre style='font-size:15px;'>
       route
       editor
       twextra_key
       twitter_user_id
       twitter_access_token
       twitter_access_token_secret
       social
</pre>";
	
	$message .= "<h3>Format: json</h3>";
	$message .='<h3>Sample json response</h3><pre style="font-size:12px;">
{
	"place":null,
	"in_reply_to_screen_name":null,
	"retweeted":false,
	"coordinates":null,
	"geo":null,
	"source":"http://twextra.com",
	"retweet_count":null,
	"favorited":false,
	"in_reply_to_status_id":null,
	"created_at":"Mon Oct 18 06:39:31 +0000 2010",
	"in_reply_to_user_id":null,
	"user":{
		"listed_count":0,
		"verified":false,
		"description":null,
		"follow_request_sent":false,
		"profile_sidebar_fill_color":"DDEEF6",
		"time_zone":"Pacific Time (US & Canada)",
		"profile_sidebar_border_color":"C0DEED",
		"followers_count":0,"url":null,
		"show_all_inline_media":false,
		"notifications":false,
		"profile_use_background_image":true,
		"friends_count":4,
		"lang":"en",
		"statuses_count":871,
		"created_at":"Tue Aug 10 06:24:42 +0000 2010",
		"profile_background_color":"C0DEED",
		"profile_image_url":"http://s.twimg.com/a/1287010001/images/default_profile_5_normal.png",
		"location":null,
		"profile_background_image_url":"http://s.twimg.com/a/1287010001/images/themes/theme1/bg.png",
		"favourites_count":1,"protected":true,"contributors_enabled":false,
		"profile_text_color":"333333",
		"screen_name":"your screen name",
		"name":"your name",
		"following":false,
		"geo_enabled":false,
		"profile_background_tile":false,
		"id":nnnnnnnnn,"utc_offset":-28800,
		"profile_link_color":"0084B4"
	},
			
	"contributors":null,
	"id":nnnnnnnnnnn,
	"truncated":false,
	"text":"your message prefix... http://twextra.com/5252h3",
	"success":"Your message was posted successfully."
}</pre>
	';
	
	$message .= '<h3>Sample curl command</h3><pre style="font-size:12px;">
curl  http://twextra.com/router.php  
-X POST 
-d route="tweet_post" 
-d editor1="message to be posted" 
-d twextra_key="mmmmmmmm" 
-d twitter_user_id="nnnnnnnn" 
-d twitter_access_token="twitter access token value"  
-d twitter_access_token_secret="twitter access token secret value" 
-d social="twitter"</pre>';
	
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