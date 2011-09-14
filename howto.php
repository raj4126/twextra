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

display_howto($screen_name);

//.........................................................................................
function display_howto($screen_name='') {
	
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
	
	$message .= "<div style='margin-left:auto;margin-right:auto;margin-bottom:20px;width:768px;font-size:0.8em;padding-top:20px;' >\n"; //p5_main
	$message .= "<h2 style='text-decoration:underline;'>How To</h2>"; 
	
	$message .= "<div>Here you will find help for the more advanced features of Twextra.</div>";//  
	
	$message .= '<ol style="clear:both;font-size:1.0em;padding-bottom:30px;border-bottom:1px solid black;" >
<li> <a href="#embed_video">Embedding Videos Into Twextra</a></li>
<li> <a href="#embed_image">Embedding Images Into Twextra</a></li>
<li> <a href="#contents">Creating A Table Of Contents In Twextra</a></li> 
<li> <a href="#embed_html">Embedding HTML Code Into Twextra</a></li> 
</ol>
';

	$message .= '
<ol style="clear:both;margin-top:50px;font-size:1.5em;" >
<a name="embed_video"></a>
<li> Embedding Videos Into Twextra
<p><object width="576" height="462"><param name="movie" value="http://www.youtube.com/v/Ju2X-Q22krg?fs=1&amp;hl=en_US"></param>
<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<embed src="http://www.youtube.com/v/Ju2X-Q22krg?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" 
allowfullscreen="true" width="576" height="462"></embed></object>
</p>
</li>
<a name="embed_image"></a>
<li> Embedding Images Into Twextra
<p>
<object width="576" height="462"><param name="movie" value="http://www.youtube.com/v/qRecMbTu3ek?fs=1&amp;hl=en_US">
</param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<embed src="http://www.youtube.com/v/qRecMbTu3ek?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" 
allowscriptaccess="always" allowfullscreen="true" width="576" height="462"></embed></object>
</p>
</li>
<a name="contents"></a>
<li> Creating A Table Of Contents In Twextra 
<p>
<object width="576" height="462"><param name="movie" value="http://www.youtube.com/v/ICLBsumCFNA?fs=1&amp;hl=en_US"></param>
<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<embed src="http://www.youtube.com/v/ICLBsumCFNA?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" 
allowscriptaccess="always" allowfullscreen="true" width="576" height="462"></embed></object>
</p>
</li>
<a name="embed_html"></a>
<li> Embedding HTML Code Into Twextra 
<p>
<object width="576" height="462"><param name="movie" value="http://www.youtube.com/v/YiSD10fuDvQ?fs=1&amp;hl=en_US"></param>
<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<embed src="http://www.youtube.com/v/YiSD10fuDvQ?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" 
allowscriptaccess="always" allowfullscreen="true" width="576" height="462"></embed></object>
</p>
</li>

</ol>
';

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