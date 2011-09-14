<?php
//.............................................
/*
tw_page2_cookie  after twitter post3:status2: stdClass Object
(
    [in_reply_to_screen_name] => 
    [truncated] => 
    [contributors] => 
    [geo] => 
    [in_reply_to_status_id] => 
    [source] => <a href="http://twetest.com" rel="nofollow">twetest.com</a>
    [created_at] => Tue Jul 06 06:16:23 +0000 2010
    [in_reply_to_user_id] => 
    [favorited] => 
    [user] => stdClass Object
        (
            [time_zone] => Pacific Time (US & Canada)
            [description] => 
            [location] => USA
            [profile_link_color] => 0084B4
            [profile_background_image_url] => http://s.twimg.com/a/1278188204/images/themes/theme1/bg.png
            [profile_sidebar_fill_color] => C0DFEC
            [lang] => en
            [notifications] => 
            [profile_background_tile] => 
            [created_at] => Wed Apr 21 18:44:01 +0000 2010
            [profile_image_url] => http://s.twimg.com/a/1278188204/images/default_profile_5_normal.png
            [statuses_count] => 116
            [profile_sidebar_border_color] => a8c7f7
            [profile_use_background_image] => 
            [followers_count] => 1
            [screen_name] => kapil8865
            [contributors_enabled] => 
            [following] => 
            [friends_count] => 13
            [protected] => 1
            [geo_enabled] => 
            [profile_background_color] => 022330
            [name] => Kapil
            [favourites_count] => 1
            [url] => 
            [id] => 135588654
            [verified] => 
            [utc_offset] => -28800
            [profile_text_color] => 333333
        )

    [place] => 
    [id] => 17848350019
    [coordinates] => 
    [text] => testtstsewewe... http://twetest.com/49f1nf
    [success] => Your message was posted successfully.
)
*/
//.............................................
if (session_id() == "") session_start();

$docroot = $_SERVER['DOCUMENT_ROOT'];
require_once $docroot."/config.php";
require_once $docroot."/models/twextra_model.php";
require_once $docroot."/tw.lib.php";
require_once $docroot."/banner.php";
require_once $docroot."/header_html.php";
require_once $docroot."/lib/embed_in_link.php";

//validate access;
validate_access_twetest();

if (isset ( $_GET['message_id'] )) {
	$message_id = $_GET ['message_id'];
}else{
	$message_id = '';
}

if (isset ( $_GET['mthd'] )) {
	$method = $_GET ['mthd'];
}else{
	$method = '';
}

if ($method == 'displayTweet') {
	
	$view = new TwextraView ( );
	$view->{$method} ( $message_id );
} else if ($method == 'displayTranslatedTweet') {
	$src_lang = $_GET ['src_lang'];
	//$src_lang_value = $_GET ['src_lang_value'];
        $src_lang_value = '';
	$tgt_lang = $_GET ['tgt_lang'];
	//$tgt_lang_value = $_GET ['tgt_lang_value'];
        $tgt_lang_value = '';
	$view = new TwextraView ( );
	$view->{$method} ($src_lang, $src_lang_value, $tgt_lang, $tgt_lang_value, $message_id);
}

class TwextraView{
	public $language_list = array('af'=>'Afrikaans','sq'=>'Albanian','ar'=>'Arabic',
	'be'=>'Belarusian','bg'=>'Bulgarian','ca'=>'Catalan','zh'=>'Chinese','hr'=>'Croatian',
	'cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','en'=>'English','et'=>'Estonian',
	'fil'=>'Filipino','fi'=>'Finnish','fr'=>'French','gl'=>'Galician','de'=>'German',
	'el'=>'Greek','he'=>'Hebrew','hi'=>'Hindi','hu'=>'Hungarian','is'=>'Icelandic',
	'id'=>'Indonesian','ga'=>'Irish','it'=>'Italian','ja'=>'Japanese','ko'=>'Korean',
	'lv'=>'Latvian','lt'=>'Lithuanian','mk'=>'Macedonian','ms'=>'Malay',
	'mt'=>'Maltese','no'=>'Norwegian','fa'=>'Persian','pl'=>'Polish','pt'=>'Portuguese',
	'ro'=>'Romanian','ru'=>'Russian','sr'=>'Serbian','sk'=>'Slovak','sl'=>'Slovenian',
	'es'=>'Spanish','sw'=>'Swahili','sv'=>'Swedish','th'=>'Thai','tr'=>'Turkish',
	'uk'=>'Ukrainian','vi'=>'Vietnamese','cy'=>'Welsh','yi'=>'Yiddish');
	
	function displayTweet($message_id, $error='') {
	
	    $time1 = time();
		
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$css = $config_params['css'];
		$tweet_size_max = $config_params['tweet_size_max'];
		$tweet_size_max_google = $config_params['tweet_size_max_google'];
		$hostname = $config_params['hostname'];
		$doctype = $config_params['doctype'];
		$html_attribute = $config_params['html_attribute'];
		
		$footer = $config_params ['footer']; //
		$docroot = $config_params ['docroot'];
		$godaddy_analytics = $config_params ['godaddy_analytics'];
		$debug = $config_params ['debug'];
		$enable_stats = $config_params ['enable_stats'];
		$fb_app_id = $config_params ['fb_app_id'];
		$show_maintenance_page = $config_params ['show_maintenance_page'];
		
		if ($show_maintenance_page == 1) {
			maintenance_page ();
		}
				
		$script_path = __FUNCTION__ ;
		
		//save logs
		if($enable_stats){
			$model = new TwextraModel();//
			$model->saveStat();
		}
		
		
		$display_remote_ip = trim($_SERVER['REMOTE_ADDR']);
			logger($script_path."  display_remote_ip: ", $display_remote_ip);
			
		$view_inc = true;	
		//don't count twitter bot access for correct view count..	
		if ($_SERVER ['REMOTE_ADDR'] == '128.242.241.134') {
			//$view_inc = false;
                        return;
		}
		if(isset($_SESSION['error_size'])){
			$_SESSION['error_size'] = '';
		}
		
        logger($script_path."  before model read1:");
		
		$model = new TwextraModel ( );
		
		$user_info = $model->get_user_info ( $message_id );
		
        logger($script_path."  user_info1: ", $user_info);
           
		$last_viewed_timestamp = $user_info['last_viewed'];
		
		$model->incViewCount($message_id);
		
        $last_viewed = last_viewed($last_viewed_timestamp);
           logger($script_path."  message_id1: ", $message_id);
           logger($script_path."  user_info1: ", $user_info);
		
		$screen_name_poster = trim($user_info['screen_name']);//screen name of poster

		$name = trim($user_info['name']);
		$location = trim($user_info['location']);
		$description = trim($user_info['description']);
		$user_image_url = trim($user_info['user_image_url']);
		$message_id_reply = trim($user_info['message_id_reply']);
		
		if(!empty($message_id_reply)){
			$message_created_by2 = " (in reply to <a href='$hostname/$message_id_reply' target='_blank' >$message_id_reply</a>)";
		}else{
			$message_created_by2 = "";
		}
		
		$banner = banner('', 'banner'); //(user, banner_class)
		
		if (! empty ( $screen_name_poster )) {
			$message_created_by = "<a href='http://twitter.com/$screen_name_poster' 
			target='_blank' >@$screen_name_poster</a> created this message (<a href=$hostname/profile.php?screen_name=$screen_name_poster>see profile</a>)";
			$message_postfix = ':';
		} else if(!empty($message_created_by2)) {
			$message_created_by = ' Message';
			$message_postfix = ':';
		}else{
			//$message_created_by = " Message";
			$message_postfix = '';
		}
		
		$message_created_by .= $message_created_by2 . '';
		
		logger ( $script_path . " message created by:", $message_created_by );
		
		if (($screen_name_poster == '') && ($message_created_by2 == '')) {
			//$message_created_by = "Message:";
		} 

           //logger("View page/displayTweet peak memory usage: ", memory_get_peak_usage(true));
           logger($script_path."  get memory usage: ", memory_get_usage(true));
           logger($script_path."  IP Address: ", $_SERVER['REMOTE_ADDR']);
           //logger("View page/displayTweet get memory limit: ", memory_limit);

		$tweet = $user_info['tweet'];
		
		$tweetlen = strlen($tweet);
		
		//this code snippet can slow down response time drastically!!
		if(($debug == 1) && ($hostname == 'http://twetest.com')){
			for($i=0; $i<$tweetlen; $i++){
			
			        $hex = dechex(ord($tweet[$i]));
			
			logger($script_path."tweet characters:", $i."-".$tweet[$i]."-".$hex."\n");
			}
		}
		//add non-breaking space at the end to fix embedded hashtags..
		$tweet = $tweet . "&nbsp;";
		
		//embed links for all cases (Twitter, LinkedIn, Twextra)
		//rule: must process links first, then at_replies, hash_tags, and emails
		$pattern_to_embed = 'link';
		$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
		$tweet = $embed->embedPattern ();
		
		//embed @replies for all cases
		$pattern_to_embed = 'at_reply';
		$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
		$tweet = $embed->embedPattern ();
		
		//embed #hashtags for all cases
		$pattern_to_embed = 'hash_tag';
		$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
		$tweet = $embed->embedPattern ();
		
		//embed links for all cases (Twitter, LinkedIn, Twextra)
//		$pattern_to_embed = 'email';
//		$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
//		$tweet = $embed->embedPattern ();
		
		//$view_count = $result['view_count'];
		$view_count = $user_info['view_count'] + 1;
		//$created = $result['created'];
		$created = $user_info['created'];
		$url_page = $hostname."tweet_display.php";
		$query_string ="?message_id=$message_id&mthd=displayTweet";
		$url = $url_page.$query_string;
        $url_rewrite = "$hostname/$message_id";
		$url_size = strlen($url_rewrite);
		$url_box_size = $url_size + 2;
		
		$tweet_size = strlen ( $tweet );
		
		$controller = new TwextraController();
		$prefix = $controller->getPrefix ($tweet, $message_id);
		$header = header_html($prefix);//
		//..........................................................
        
        header("Pragma: no-cache");
    	header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1
   		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        
		$message = '';
		$message .= $doctype; 
		$message .= "<html $html_attribute  xmlns:fb=\"http://www.facebook.com/2008/fbml\" >\n";	
		$message .= $header;	
		$message .= "<body>\n";
		$message .= "<div class='p2_wrapper'>\n";
		$message .= "<div class='p2_page'>\n";	
        $message .= $banner;	
		$message .= "<div class='p2_main'>\n";//p2_main
//.................................................		
		
		$message .= "<div class='p2_main_inner'>\n";
		
			if (isset ( $_SESSION ['update_li'] ) && ($_SESSION ['update_li'] == 1)) {
			$message .= "<div style='text-align:center; margin-top:20px; margin-bottom:30px;
			border:solid 2px #244f29;background-color:#bdf2c2;padding:10px;' id='message_status_l'  >";
			$message .= "Your message was posted successfully on LinkedIn.";
			$message .= "<a href='javascript: hide_message_status(\"message_status_l\")' style='float:right;font-size:0.8em;' >Close</a>";
			$message .= "</div>";
			$_SESSION['update_li'] = 0;
		}
		
			if (isset ( $_SESSION ['update_tw'] ) && ($_SESSION ['update_tw'] == 1)) {
			$message .= "<div style='text-align:center; margin-bottom:30px;margin-top:10px;
			border:solid 1px #244f29;background-color:#bdf2c2;padding:10px;' id='message_status_t' >";
			$message .= "Your message was posted successfully on Twitter.";
			$message .= "<a href='javascript: hide_message_status(\"message_status_t\")' style='float:right;font-size:0.8em;' >Close</a>";
			$message .= "</div>";
			$_SESSION['update_tw'] = 0;
		}
		
			if (!empty($error)) {
			$message .= "<div style='text-align:center; margin-bottom:30px;
			border:solid 1px #244f29;background-color:#bdf2c2;padding:10px;' id='message_status_t' >";
			$message .= $error;
			$message .= "<a href='javascript: hide_message_status(\"message_status_t\")' style='float:right;font-size:0.8em;' >Close</a>";
			$message .= "</div>";
			$_SESSION['update_tw'] = 0;
		}
		
		if ($screen_name_poster != '') {
			$message .= "<div class='p2_sidebar_image' style='float:left;margin-right:5px;' >
			<a href='http://twitter.com/$screen_name_poster' target='_blank' style='clear:both;float:left;' >
			<img src='" . $user_image_url . "' style='width:48px;height:48px;' /></a></div>"; //
		}
		
        $message .= "<div class='p2_actions' >";//marker 3.1
        
		$message .= "<a href='$hostname/index.php?screen_name_reply=$screen_name_poster&message_id_reply=$message_id' >Reply</a>";
        $message .= " | <a href='$hostname/index.php?message_id=$message_id'>Edit</a>";
        $message .= " | <a href='$hostname' >New</a>";
        if(($_SESSION['user'] != '')&&($screen_name_poster == $_SESSION['user'])){
        	//$message .= " | <a href='$hostname/router.php?route=delete_msg&message_id=$message_id&message_poster=$screen_name_poster' >Delete</a>";
        	$message .= " | <a id='delete' style='color:blue;' onclick=_delete('$hostname','$message_id','$screen_name_poster')>Delete</a>";
        }
        $message .=  "</div>"; //marker 3.1     
		$message .= "<div class='p2_createdby' >"; //p2_createdby
		
		$message .= $message_created_by . $message_postfix;
		$message .= "</div>";//p2_createdby
		
		$message .= "<div class='p2_message_metadata' >";//non-twitter div
		$message .= "<div class='p2_created'> <strong>Created:</strong><br /> ".$created." (Pacific Time)</div>\n";
			
		$message .= "<div class='p2_count'>\n";
		$message .=  "<strong>Views:</strong><br /> $view_count";
		$message .= "</div>\n";
		$message .= "<div class='p2_count'>\n";
		$message .=  "<strong>Last viewed:</strong><br /> $last_viewed";
		$message .= "</div>\n";
		
		$message .= "<div class='p2_tweet_url'><strong>URL:</strong><br /> ";
		
		$message .= $url_rewrite;
		
		$message .= "</div>\n";//p2_tweet_url	
		$message .= "</div>";//non-twitter div
		
//		$message .= "<div class='p2_tweet_header'>\n";
//		$message .= "<div style='float:right; width:400px;' >";//marker 1	
//		$message .= "<div class='p2_count'>Message size (incl. formatting): " . $tweet_size . "</div>\n";	
//		$message .= "</div>";//marker 1
//		$message .= "<div style='float:left' >";//marker 2
//		$message .= "</div>";//marker 2
//		$message .= "<br style='clear:both' />";
//		$message .= "</div>";//tweet_header

		$message .= "<div class='p2_tweet'>";
		
		//decode four special entity characters inside object tags to be able to play videos
		$pattern_to_embed = 'link';
		$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
		$tweet = $embed->entityDecodeObject ($tweet);
		
		$message .= "{$tweet}";  
		
	$message .= "</div>\n";//p2_tweet
		
	$message .= "<div class='p2_buttons' >";
		//tweet button
		$message .= "<div class='p2_twitter_button' style='float:left;' >";//p2_twitter_button
		$message .= '<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" >Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		$message .= "</div>";//p2_twitter_button
		
		//facebook like button
		$message .= "<div class='p2_facebook_button' style='float:left;' >";//p2_facebook_button
		$message .= "
    <fb:like show_faces='false' font='arial' ></fb:like>

    <div id=\"fb-root\"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({appId: '$fb_app_id', status: true, cookie: true,
                 xfbml: true});
      };
      (function() {
        var e = document.createElement('script');
        e.type = 'text/javascript';
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
		";
		$message .= "</div>";//p2_facebook_button
		$message .= "<br style='clear:both;' />";
	$message .= "</div>";//p2_buttons
	
	$message .= "<hr />";
	
//disqus javascript- part 1
		
		$message .= "<div id='disqus_thread'></div>
<script type='text/javascript'>

  /**
    * var disqus_identifier; [Optional but recommended: Define a unique identifier (e.g. post id or slug) for this thread] 
    */
 
  (function() {
   var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
   dsq.src = 'http://twextra.disqus.com/embed.js';
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();
</script>
<noscript>Please enable JavaScript to view the <a href='http://disqus.com/?ref_noscript=twextra'>comments powered by Disqus.</a></noscript>
<a href='http://disqus.com' class='dsq-brlink'>blog comments powered by <span class='logo-disqus'>Disqus</span></a>";

		
//		if ($screen_name_poster != '') {
//			$message .= "<div class='p2_twitter_bio'>"; //p2_twitter_bio
//			$message .= "<div class='p2_sidebar_image' style='float:left; clear:both;margin-bottom:5px;' >
//			<a href='http://twitter.com/$screen_name_poster' target='_blank' style='clear:both;float:left;' >
//			<img src='" . $user_image_url . "' style='width:48px;height:48px;' /></a></div>"; //???
//			
//
//			$message .= "<div class='p2_names'>"; //p2_names
//			$message .= "<div class='p2_sidebar_screen_name' style='float:left; margin-bottom:10px;' >
//	   		&nbsp;&nbsp;<a href='http://twitter.com/$screen_name_poster' target='_blank'>$screen_name_poster</a>
//	   		</div>";
//			
//			if ($name != '') {
//				$message .= "<div class='p2_twitter_name'  ><strong>Name:</strong> $name</div>";
//			}
//			$message .= "<br style='clear:both;' />";
//			$message .= "</div>"; //p2_names
//			if ($location != '') {
//				$message .= "<div class='p2_location' ><strong>Location:</strong> $location </div>";
//			}
//			
//			if ($description != '') {
//				$message .= "<div class='p2_bio' ><strong>Bio:</strong> $description</div>";
//			}
//			$message .= "</div>"; //p2_twitter_bio
//		}
		
		//.................................................			
		//$message .= "<div class='p2_main_sidebar'>"; //p2_main_sidebar
		//google adsense
		
		//$message_adsense2 = '<div style="width:728px; margin-left:auto; margin-right:auto;" >
//<script type="text/javascript"><!--
//google_ad_client = "pub-6612446954643048";
///* 728x90, created 8/23/10 */
//google_ad_slot = "6010217329";
//google_ad_width = 728;
//google_ad_height = 90;
//-->
//</script>
//<script type="text/javascript"
//src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
//</script>

//</div>';
		//block out google ads and use direct/clean ads later..
		//$message .= $message_adsense2;
		
		//$message .= "</div>"; //p2_main_sidebar
		
        echo $message;
		
		$message = "";
		if ($tweet_size < $tweet_size_max_google) {
			$message .= "<div class='p2_main_form'>\n";
			$message .= "<div class='p2_main_form_inner'>\n";
			
			$message .= "<form method='post' action='router.php'  >\n";
			$message .= "<div class='p2_main_trans'>\n";
			$message .= "Translate message: \n";
			$message .= "</div>\n"; //main_trans
			
			$message .= "<div class='p2_src_tgt_tran'>";
			$message .= "<div class='p2_src_lang'>\n";
			$message .= "Source Language: ";
			$message .= "<select name='src_lang'>\n";
			foreach ( $this->language_list as $key => $val ) {
				$option = "<option value='$key'>$val</option>\n";
				$message .= $option;
			}
			$message .= "</select>";
			$message .= "</div>\n"; //src_lang

			$message .= "<div class='p2_tgt_lang'>Target Language: \n";
			$message .= "<select name='tgt_lang'>\n";
			foreach ( $this->language_list as $key => $val ) {
				$option = "<option value='$key'>$val</option>\n";
				$message .= $option;
			}
			$message .= "</select></div>\n"; //tgt_lang
			$message .= "<div class='p2_sub'>\n";
			$message .= "<input class='button_translate' type='submit' name='submit' value='Translate' />\n";
			$message .= "</div>\n"; //sub
			$message .= "<br style='clear:both;' />";
			$message .= "</div>\n"; //p2_src_tgt_tran
			$message .= "<br style='clear:both;' />";
			
			$message .= "<div>\n"; //hidden
			$message .= "<input type='hidden' name='route' value='tweet_translate' ></input>\n";
			$message .= "<input type='hidden' name='url' value='{$url}' ></input>\n";
			$message .= "<input type='hidden' name='message_id' value='{$message_id}' ></input>\n";
			$message .= "</div>\n"; //hidden
			$message .= "</form>\n";
			$message .= "<br style='clear:both;'>";
			$message .= "</div>\n"; //main_form_inner
			$message .= "</div>\n"; //main_form
		}
		
			$message .= "<div style='margin-left:auto;margin-right:auto;width:600px; margin-top:50px;'>
		               <a href='http://www.YouTube.com/officialsampepper' target='_blank'>
		               <img src='$hostname/images/banner_page_b.jpg' /></a></div>";  
		
		$message .= "<br style='clear:both;'>\n";
		$message .= "</div>\n";//main_inner
		$message .= "<br style='clear:both;' />";
		$message .= "</div>\n";//p2_main
		
        $message .= $footer;
        $message .= "</div>\n";//page
		$message .= "</div>\n";//wrapper
		
        //$message .= $google_analytics;
		$message .=  $godaddy_analytics;
		
//disqus javascript-part 2

		$message .= "<script type='text/javascript'>
var disqus_shortname = 'twextra';
(function () {
  var s = document.createElement('script'); s.async = true;
  s.src = 'http://disqus.com/forums/twextra/count.js';
  (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
}());
</script>
		";

		
		    $time2 = time();
		    //echo "diff:", ($time2 - $time1);
		
		$message .= "</body>\n</html>\n";
		
		echo $message;           
	}
	//.........................................................................................
	function displayTranslatedTweet($src_lang, $src_lang_value='', $tgt_lang, $tgt_lang_value='', $message_id){
		
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$css = $config_params['css'];
		$tweet_size_max_google = $config_params['tweet_size_max_google'];
		$hostname = $config_params['hostname'];
		$doctype = $config_params['doctype'];
		$html_attribute = $config_params['html_attribute'];
		//$header = $config_params['header'];//
		//$banner = $config_params['banner'];//
		$banner = banner('', 'banner'); //(user, banner_class)
		$footer = $config_params['footer'];//
		$docroot = $config_params['docroot'];
		$godaddy_analytics = $config_params['godaddy_analytics']; 
		$show_maintenance_page = $config_params ['show_maintenance_page'];
		
		if ($show_maintenance_page == 1) {
			maintenance_page ();
		}
				
		$url = $hostname."tweet_display.php?message_id=$message_id&#38;mthd=displayTweet";

                $url_rewrite = "$hostname/$message_id";
 
                 $src_lang_value = $this->language_list[$src_lang];
                 $tgt_lang_value = $this->language_list[$tgt_lang];
		
		//get src_tweet from model;
		$model = new TwextraModel ( );
		$result = $model->readTweet ( $message_id, false );
		$src_tweet = $result['tweet'];
		
		//embed @replies for all cases
		$pattern_to_embed = 'at_reply';
		$embed = new EmbedInLink ( $src_tweet, $pattern_to_embed );
		$src_tweet = $embed->embedPattern ();
			
		//embed links for all cases (Twitter, LinkedIn, Twextra)
		$pattern_to_embed = 'link';
		$embed = new EmbedInLink ( $src_tweet, $pattern_to_embed );
		$src_tweet = $embed->embedPattern ();
		
		$created = $result['created'];
		
		$controller = new TwextraController();
		$prefix = $controller->getPrefix ($src_tweet, $message_id);
		$header = header_html($prefix);//
			
		//get tgt_tweet from google service
		//CHECK MAX SIZE, AND FREQUENCY OF TRANSLATIONS..
		if (trim ( $src_tweet ) != '') {
			$tgt_tweet = $this->translate ( $src_lang, $tgt_lang, $src_tweet );
			$tweet_object = json_decode ( $tgt_tweet );
			if (isset ( $tweet_object->responseData->translatedText )) {
				$tgt_tweet = $tweet_object->responseData->translatedText;
			} else {
				$tgt_tweet = '';
			}
		} else {
			$tgt_tweet = '';
			$url = '';
		}
		
		header("Pragma: no-cache");
    	header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1
   		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
   	
		$message  = '';
		
		$message .= $doctype; 
		$message .= "<html $html_attribute >\n";
		$message .= $header;
		
		$message .= "<body>";
		$message .= "<div class='p3_wrapper'>";
		$message .= "<div class='page'>";
		
        $message .= $banner;
        
		$message .= "<div class='p3main'>\n";
		$message .= "<div class='p3main_inner'>\n";
		
		$message .= "<div class='p3main_text'>\n";
		
		$message .= "<div class='p3tweet_header'>\n";
		$message .= "<div class='p3count'> Created: ".$created." (Pacific Time)</div>\n";
		$message .= "<div style='float:left;'>\n";
		$message .= "<div class='p3tweet_url'>Message URL: </div>\n";
		$message .= "<div class='p3url_outer'><a href='{$url_rewrite}'>".$url_rewrite."</a></div>\n";
		$message .= "</div>\n";
		$message .= "<br style='clear:both;' />\n";
		$message .= "</div>\n";
		
		$message .= "<div>";
		$message .="<div class='p3tweet_title'>Source language ({$src_lang_value}):</div>\n";
		
		$message .= "<div style='float:right; text-align:center;padding-bottom:5px;'><a href='$hostname' >
        New</a> | <a href='$hostname/index.php?message_id=$message_id'>Edit</a></div>"; 
		
		$message .= "<br style='clear:both;' />";
		$message .= "</div>";
		$message .= "<div class='p3tweet'>{$src_tweet}</div>";

		$message .= "<div class='p3tweet_title_tr'>Target language ({$tgt_lang_value}):</div>\n";
		$message .= "<div class='p3tweet'>{$tgt_tweet}</div>";
		
		$message .= "</div>\n";//main_text
		
		$message .= "<div class='p3main_form'>\n";
		$message .= "<div class='p3main_form_inner'>\n";
		$message .= "<form method='post' action='$hostname/router.php' name='tweet_translate' >\n";
		
		$message .= "<div class='p3main_trans'>\n";
		$message .= "Translate again: \n";
		$message .= "</div>\n";
		
		$message .= "<div class='p3_src_tgt_tran'>";
		$message .= "<div class='p3main_src' >Source Language: \n";
		$message .= "<select name='src_lang'>\n";
		$selected = '';
		foreach ( $this->language_list as $key => $val ) {
			if ($src_lang == $key) {
				$selected = "selected='selected'";
			}
			$option = "<option value='$key' $selected >$val</option>\n";
			$message .= $option;
			$selected = '';
		}
		$message .= "</select>\n";
		$message .= "</div>\n";
		
		$message .= "<div class='p3main_tgt' >Target Language: \n";
		$message .= "<select name='tgt_lang'>\n";
		foreach($this->language_list as $key=>$val){
			$option = "<option value='$key'>$val</option>\n";
			$message .= $option;
		}
		$message .= "</select>\n";
		$message .= "<input type='hidden' name='route' value='tweet_translate' ></input>\n";
		$message .= "<input type='hidden' name='url' value='{$url}' ></input>\n";
		$message .= "<input type='hidden' name='message_id' value='{$message_id}' ></input>\n";	
		$message .= "</div>\n";
		$message .= "<div class='p3sub'>\n";
		$message .= "<input class='button_translate' type='submit' name='submit' value='Translate' />\n";
		$message .= "</div>\n";//p3sub
		$message .= "<br style='clear:both;' />";
		$message .= "</div>";//p3_src_tgt_tran
		$message .= "</form>\n";
		$message .= "<br style='clear:both' />\n";
		$message .= "</div>\n";//p3main_form_inner
		$message .= "</div>\n";//p3main_form
		
		$message .="</div>\n";//main_inner
		$message .="</div>\n";//main
		
         $message .= $footer;
		
		$message .="</div>\n";//page
		$message .="</div>\n";//wrapper
		$message .=  $godaddy_analytics;
		$message .= "</body></html>\n";
		
		echo $message;
	}
	
////............................................................................................	
function translate($src_lang, $tgt_lang, $tweet){
 
  $prefix = 'q=';
  $query = urlencode($tweet);
  $query_string = $prefix.$query;
  $request = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&".$query_string."&langpair=".$src_lang."%7C".$tgt_lang;

  //$tweet_translated = file_get_contents($request);
   	 $ch = curl_init();
   	 curl_setopt($ch, CURLOPT_URL, $request);
   	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   	 curl_setopt($ch, CURLOPT_HTTPGET, true);
   	 $tweet_translated = curl_exec($ch);
   	 curl_close($ch);
   	 
    return $tweet_translated;
  }
 /////////////////////////////////////////////////////////////////////// 
  
	function displayFooterFields($content) {
		
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$css = $config_params['css'];
		$hostname = $config_params['hostname'];
		$doctype = $config_params['doctype'];
		$html_attribute = $config_params['html_attribute'];
		$header = $config_params['header'];//
		$banner = $config_params['banner'];//
		$footer = $config_params['footer'];//
		$docroot = $config_params['docroot'];
		$godaddy_analytics = $config_params['godaddy_analytics']; 
		
		$message = $doctype; 
		$message .= "<html $html_attribute >\n";
		$message .= $header;
		
		$message .= "<body>\n";
		
		$message .= "<div class='wrapper'>\n";
		$message .= "<div class='page'>\n";
		
        $message .= $banner;

		$message .= "<div class='p4_main'>\n";
		$message .= "<div class='p4_main_inner'>\n";
		
		$message .= "<div class='p4_tweet'>";
		$message .= "{$content}";
		$message .= "</div>\n";//tweet
		
		$message .= "<br style='clear:both;'>\n";
		$message .= "</div>\n";//main_inner
		$message .= "</div>\n";//main
		
        $message .= $footer;
        $message .= "</div>\n";//page
		$message .= "</div>\n";//wrapper
		
		//add godaddy analytics:
		
		$message .= $godaddy_analytics;

		$message .= "</body>\n</html>\n";
		
		echo $message;
	}
//	public function plural($data) {
//		if ($data == 1) {
//			$plural = '';
//		} else {
//			$plural = 's';
//		}
//		return $plural;
//	}
}
?>
<script type='text/javascript'>

function _delete(hostname, msg_id, msg_poster){

	var test;

	test = confirm("Are you sure you want to delete this message?");

	if(test == 1){
		window.location = hostname+"/router.php?route=delete_msg&message_id="+msg_id+"&message_poster="+msg_poster;            
	}
}
</script>