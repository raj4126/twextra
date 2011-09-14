<?php
//.......................................................................
if (session_id() == "") session_start();
ini_set('display_errors', 0);    //debugging: 1;      production: 0
ini_set('error_reporting', 0);   //debugging: E_ALL;  production: 0 (turns off all error reporting): 
//..........................................................................
class Config{
	private static $debug = 1;                      //debugging: 1;      production: 0
	private static $cache_type = 'memcache';//{'memcache','apc','nocache'}
	//private static $cache_type = 'apc';//{'memcache','apc','nocache'}
	private static $enable_stats = 1;  //for gathering daily uniques stats
	private static $show_maintenance_page = 0;//set to 1 for downtime during maintenance
	private static $print_full_message_list=0;//default=0; set to 1 for printing traj4126's full message list
	private static $docroot;
//.......................................................................
	private static $server_name = 'twextra';
//	private static $server_name = 'twetest';
//.........................................................................
	private static $watch_demo = "http://www.youtube.com/watch?v=sAPdjQHdkVs";
	private static $tweet_size_max = 100000;//100000 (default)
	private static $tweet_size_max_google = 0;//1500 (default)
	private static $session_time_max = 1800;//30 minutes
	private static $prefix_size_max = 140;//140 characters max
	private static $twitter_embedded_token_max = 5000;
	
	private static $doctype;
	private static $html_attribute = 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"';
	private static $google_analytics;
	private static $godaddy_analytics;
	private static $hostname;
	private static $cookie_name;
	private static $hostdb;
	private static $database;
	private static $userdb;
	private static $pwdb;
	private static $linkedin_access;
	private static $linkedin_secret;
	private static $consumer_key;
	private static $consumer_secret;
	private static $oauth_callback;
	private static $css;
	//private static $header;//
	//private static $banner;//
	private static $footer;
	private static $ep4 = "Error: You must be signed in to view message history";
	private static $fb_app_id;
	private static $api_daily_max = 5000;
	private static $search_len_max = 100;
	private static $search_len_size = 20;
	private static $search_count_offset = 2;  //subtract this offset from total message count to state: "search over nnn Twextra messages"
//......................................................................	
public function getConfigParams(){
	
//$doctype_str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
$doctype_str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
self::$doctype = $doctype_str;

$godaddy_analytics_str = "<!--GODADDY ANALYTICS:--><script type=\"text/javascript\">
var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
try {
var pageTracker = _gat._getTracker(\"UA-15905158-1\");
pageTracker._trackPageview();
} catch(err) {}
</script>
<script type=\"text/JavaScript\">var TFN='';var TFA='';var TFI='0';var TFL='0';var tf_RetServer=\"rt.trafficfacts.com\";
var tf_SiteId=\"14120g9493f85cbde65e99dd8c83e9c3f3473b29529d1fh10\";
var tf_ScrServer=document.location.protocol+\"//rt.trafficfacts.com/tf.php?k=14120g9493f85cbde65e99dd8c83e9c3f3473b29529d1fh10;c=s;v=5\";
document.write(unescape('%3Cscript type=\"text/JavaScript\" src=\"'+tf_ScrServer+'\">%3C/script>'));</script>
<noscript>
<img src=\"http://rt.trafficfacts.com/ns.php?k=14120g9493f85cbde65e99dd8c83e9c3f3473b29529d1fh10\" height=\"1\" width=\"1\" alt=\"\"/>
</noscript>";

$google_analytics_str = "<!--GOOGLE ANALYTICS:--><script type=\"text/javascript\">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15905158-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>";
		

self::$docroot = $_SERVER ['DOCUMENT_ROOT'];

		if (self::$server_name == 'twextra') {
			
			self::$godaddy_analytics = $godaddy_analytics_str;
			self::$google_analytics =  '' ;

			self::$hostname = "http://twextra.com";

			//self::$cookie_name = "tw_tok";
			self::$cookie_name = "tw_user_id";

			self::$hostdb = '173.201.185.107';

			self::$database = 'twextra';

			self::$userdb = 'twextra';

			self::$pwdb = 'Spearmint1';

			self::$linkedin_access = 'U6zyYyGUwE5o0zvIkMnGosw2WpQusl-Pa4JNQyIy9IDdsDLKdp2by6qzwFM7H6Rv'; //linkedin-twextra

			self::$linkedin_secret = 'GB-ssm8NUpUVD2h1X2dNxv2eESi8Tgc57hxciklT8-KsSPwNC9KcbOP1drSHSNUL'; //linkedin-twextra

			self::$consumer_key = 'OIWt7zoGUChqHkELTyYx8g'; //twitter-twextra

			self::$consumer_secret = 'oqLgz87EFUCNZYxNr7yEeZqHwTTPwv4CcwWS2K7YIg'; //twitter-twextra
			self::$oauth_callback = self::$hostname."/twitter/page2.php"; //twitter-twextra
			self::$fb_app_id = '144934438868111';

		} else if (self::$server_name == 'twetest') {
			
			self::$godaddy_analytics = '';
			self::$google_analytics =  '' ;

			self::$hostname = "http://twetest.com";

			//self::$cookie_name = "tw_tok";
			self::$cookie_name = "tw_user_id";

			self::$hostdb = '173.201.217.33';

			self::$database = 'twetest';

			self::$userdb = 'twetest';

			self::$pwdb = 'Spearmint1';

			self::$linkedin_access = 'YMKaHlPF6xv8YTMs_FftnoC1tq_0Fgoz9Y8me0PvcR1Sm9WxzuPI18hZr2yP3fFq'; //linkedin-twetest
			self::$linkedin_secret = 'PuKdmBOQFdR1vibAe0LX3yRkKhu-NWlZaqC3EwnsiiMw1OL0EZ_J_rmh5PjzHXfg'; //linkedin-twetest

			self::$consumer_key = 'JBRzgN0LeUJFzCo2K1koGw'; //twitter-twetest
			self::$consumer_secret = '1DuqRvKXXGHSs77XlkYFUiIqWaL0XFgymL1iprZV8'; //twitter-twetest
			self::$oauth_callback = self::$hostname."/twitter/page2.php"; //twitter-twetest
			self::$fb_app_id = '138321036207048';
			
			//$auth_header = header('WWW-Authenticate: Basic realm="My Realm"',true,401); //
			//self::$doctype = $auth_header.self::$doctype; //		
		} else {

			exit ( "Error: server not found" );

		}
//..........................................................................	
if (isset ( $_SESSION ['useragent'] ) && $_SESSION ['useragent'] == 'device') {

			self::$css = "/scripts/main.css"; //docroot not included for href tags..
} else {

			self::$css = "/scripts/main.css"; //docroot not included for href tags..
}
//.............................................................................
self::$footer = '';

self::$footer .= "
<div class='footer'>
Viewista, Inc. &#169; 2010  <br /> 
<a href='".self::$hostname."/contact.php'>Contact</a> |
<a href='".self::$hostname."/about.php'>About</a> |
<a href='".self::$hostname."/privacy.php'>Privacy Policy</a> |
<a href='".self::$hostname."/terms.php'>Terms</a> |
<a href='".self::$hostname."/faqs.php'>FAQs</a> |
<a href='".self::$hostname."/api_support.php'>API Support</a> |
<a href='".self::$hostname."/howto.php'>How To</a>
<div style='padding:10px 0px;' >
Follow us for updates: <a href='http://twitter.com/TwextraDotCom' target='_blank' style='color:blue'>Twitter</a> | 
<a href='http://www.facebook.com/twextra' target='_blank' style='color:blue' >Facebook</a>
</div>

</div>
";
//..........................................................................

	$config_params = array('debug'=>self::$debug,
	                       'cache_type'=>self::$cache_type,
						   'enable_stats'=>self::$enable_stats,
						   'docroot'=>self::$docroot,
	                       'watch_demo'=>self::$watch_demo,
	                       'tweet_size_max'=>self::$tweet_size_max,
	                       'tweet_size_max_google'=>self::$tweet_size_max_google,
	                       'session_time_max'=>self::$session_time_max,
	                       'prefix_size_max'=>self::$prefix_size_max,
	                       'doctype'=>self::$doctype,
	                       'html_attribute'=>self::$html_attribute,
	                       'google_analytics'=>self::$google_analytics,
	                       'godaddy_analytics'=>self::$godaddy_analytics,
	                       'hostname'=>self::$hostname,
	                       'cookie_name'=>self::$cookie_name,
	                       'hostdb'=>self::$hostdb,
	                       'database'=>self::$database,
	                       'userdb'=>self::$userdb,
	                       'pwdb'=>self::$pwdb,
	                       'linkedin_access'=>self::$linkedin_access,
	                       'linkedin_secret'=>self::$linkedin_secret,
	                       'consumer_key'=>self::$consumer_key,
	                       'consumer_secret'=>self::$consumer_secret,
	                       'oauth_callback'=>self::$oauth_callback,
	                       'css'=>self::$css,
	                       'docroot'=>$_SERVER['DOCUMENT_ROOT'],
	                      // 'header'=>self::$header,
	                      // 'banner'=>self::$banner,
	                       'footer'=>self::$footer,
						   'twitter_embedded_token_max'=>self::$twitter_embedded_token_max,
	                       'ep4'=>self::$ep4,
	                       'fb_app_id'=>self::$fb_app_id,
	                       'show_maintenance_page'=>self::$show_maintenance_page,
	                        'api_daily_max'=>self::$api_daily_max,
							'print_full_message_list'=>self::$print_full_message_list,
	                        'search_len_size'=>self::$search_len_size,
	                        'search_len_max'=>self::$search_len_max,
	                        'search_count_offset'=>self::$search_count_offset
	);
	
	return $config_params;
	
}//function

}//class
//.........................................................................................

?>