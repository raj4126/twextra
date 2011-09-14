<?php 
  if (session_id () == "") session_start ();
  $docroot=$_SERVER['DOCUMENT_ROOT'];
  require_once $docroot."/config.php";
  require_once $docroot . "/models/twextra_model.php";
  require_once $docroot . "/controllers/twextra_controller.php";
  require_once $docroot . "/banner.php";
  require_once $docroot . "/header_html.php";
//......................................................................................//
function validate_access_twetest() {
	
	$config_params = Config::getConfigParams();
	$hostname = $config_params['hostname'];
	
	$home = '98.207.107.28';
	$work = '63.146.170.70';
	
	$allowed_addresses = array ($home,$work);
	$remote_address = $_SERVER ['REMOTE_ADDR'];
	
	if (($hostname == 'http://twetest.com') && (! in_array ( $remote_address, $allowed_addresses ))) {
		echo "<br /><h2>Access denied.</h2>";//temporarily mask it for testing...
		exit ();
	}
}
//......................................................................................//
function validate_access_restricted() {
	
	$config_params = Config::getConfigParams();
	$hostname = $config_params['hostname'];
	
	$home = '98.207.107.28';
	$work = '169.254.84.42';
	
	$allowed_addresses = array ($home,$work);
	$remote_address = $_SERVER ['REMOTE_ADDR'];
	
	if (($hostname == 'http://twetest.com') && (! in_array ( $remote_address, $allowed_addresses ))) {
		echo "<br /><h2>Access denied..</h2>";
		exit ();
	}
}
//..........................................................................................//
function validate_access_admin() {
	
	$home = '98.207.107.28';
	$work = '10.0.0.117';
	
	$allowed_addresses = array ($home,$work);
	$remote_address = $_SERVER ['REMOTE_ADDR'];
	
	if (! in_array ( $remote_address, $allowed_addresses )) {
		echo "<br /><h2>Access denied2...</h2>";
		exit ();
	}
}
//...........................................................................................//
function validate_access_admin_test() {
	
	$home = '98.207.107.28';
	$work = '169.254.84.42';
	
	$allowed_addresses = array ($home,$work);
	$remote_address = $_SERVER ['REMOTE_ADDR'];
	
	if (in_array ( $remote_address, $allowed_addresses )) {
		return true;
	}else{
		return false;
	}
}
//.........................................................................................//
function maintenance_page(){
	
	$config_params = Config::getConfigParams();
	$show_maintenance_page = $config_params['show_maintenance_page'];
	
	if($show_maintenance_page == 1){
		header("Location:$hostname/maintenance_page.php");
	}
}
//..........................................................................................//
function last_viewed($last_viewed){
	
//		$model = new TwextraModel ( );
//		$result = $model->readTweet ( $message_id, $view_inc );
//        $last_viewed = $result['last_viewed'];
            logger($script_path."  last_viewed1: ", $last_viewed);
            logger($script_path."  time1: ", time());
            if($last_viewed == null){
            	$last_viewed = time();
            }
            $last_viewed = time() - $last_viewed;
			$num = $last_viewed/86400;
			$days = floor($num);
			$num2 = ($num - $days)*24;
			$hours = floor($num2);
			$num3 = ($num2 - $hours)*60;
			$mins = floor($num3);
			$num4 = ($num3 - $mins)*60;
			$secs = floor($num4);
			$last_viewed = '';
		if ($days > 0) {
			$plural = plural ( $days );
			$last_viewed .= "$days day$plural ago";
		} else if ($hours > 0) {
			$plural = plural ( $hours );
			$last_viewed .= "$hours hour$plural ago";
		} else if ($mins > 0) {
			$plural = plural ( $mins );
			$last_viewed .= "$mins minute$plural ago";
		} else {
			$plural = plural ( $secs );
			$last_viewed .= "$secs second$plural ago";
		}
		
	 return $last_viewed;	
}
//...............................................................................................
function plural($data) {
		if ($data == 1) {
			$plural = '';
		} else {
			$plural = 's';
		}
		return $plural;
}
//...............................................................................................
function tw_page2_cookie($prefix, $message, $access_token_curl = '', $access_token_secret_curl = ''){

	//configuration parameters:
	$config_params = Config::getConfigParams();
	$hostname = $config_params['hostname'];
	$consumer_key = $config_params['consumer_key'];
	$consumer_secret = $config_params['consumer_secret'];
	$oauth_callback = $config_params['oauth_callback'];
	$docroot = $config_params['docroot'];
	$debug = $config_params['debug']; 
	$cookie_name = $config_params['cookie_name']; 
	
	$err_message = '';
	
	$script_path = __FUNCTION__;
	
   $_SESSION['prefix']=$prefix;
   $_SESSION['message']=$message;
   $web_api_call = false;
	
	if (isset ( $_COOKIE [$cookie_name] )) {
		$tw_user_id_hash = $_COOKIE [$cookie_name];
		
   	 	$model = new TwextraModel();
		$access_token = $model->get_tw_access_token ( $tw_user_id_hash ); //
   		$oauth_token = $access_token['oauth_token'];
   		$oauth_token_secret = $access_token['oauth_token_secret'];
	}else if($access_token_curl != '' && $access_token_secret_curl != ''){
		$oauth_token = $access_token_curl;
		$oauth_token_secret = $access_token_secret_curl;
		$web_api_call = true;
	}else{
		$_SESSION['user'] = '';
	}
	
     logger($script_path."  access_token_curl: ", $access_token_curl);
     logger($script_path."  access_token_secret_curl: ", $access_token_secret_curl);
     logger($script_path."  before model get1: ");
     logger($script_path."  after model get1: ");
	
	if (isset ( $access_token )) {
		logger ( $script_path."  access_token: ", $access_token );
		
		if ($access_token == 'error') {
			$err_message = "Error! Wrong credentials. Please try again.";
			if ($web_api_call) {
				$err_message = array ('error' => $err_message );
				print_r ( json_encode ( $err_message ) );
				exit ();
			} else {
				$err_message = urlencode ( $err_message );
				header ( "Location: $hostname/index.php?error=$err_message" );
				exit ();
			}
		}
	}
	
     logger($script_path."  before twitter access2: ");
   
   $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
     logger($script_path."  after twitter access2: ");
     logger($script_path."  http_code before post: connection: ", $connection);

  $_SESSION['status'] = 'verified';

   $prefix = $_SESSION['prefix'];
   $message_id = $_SESSION['message_id'];
   $status_url = "$hostname/$message_id";

   logger($script_path."  status_url: ", $status_url);

   $status_twextra = $prefix.$status_url;
   $status_twextra = substr($status_twextra, 0, 140);//make sure max tweet is upto 140 characters
   
   $parameters = array('status' => "$status_twextra");
   
   //...............test show message timeline..............................
   //$parameters_timeline = array('count'=>5);
   //$status_timeline = $connection->get('statuses/home_timeline', $parameters_timeline);
   // logger($script_path."  home_timeline: ", $status_timeline);
//     logger($script_path."  profile image url: ", $status_timeline[0]->user->profile_image_url);
   //.................test ends.......................

     logger($script_path."  before twitter post3: ");
   $status_obj = $connection->post('statuses/update', $parameters);//<<<<<<<POST
   logger($script_path."  status_object: ", $status_obj);
   $user_id = $status_obj->user->id;
   $_SESSION['user'] = $status_obj->user->screen_name;//
     logger($script_path."  user_id: ", $user_id);
     logger($script_path."  url string: ", $message_id);
     $model = new TwextraModel();
   $error2 = $model->update_message_user_id ( $message_id, $user_id );//<<<<<<<
     logger($script_path."  profile image url: ", $status_obj->user->profile_image_url);
     $_SESSION['profile_image_url'] = $status_obj->user->profile_image_url;
     
     //....................
	if (!empty($user_id)) {
		$error3 = $model->update_user_info ( $user_id, $status_obj );///<<<<<<<
     	logger($script_path."  after update_user_info: ", $error3);	
	}else{
		$err_message="Error! Please try again.";
		if ($web_api_call) {
			//print_r ( array ('error' => $err_message ) );
			$err_message = array('error'=>$err_message);
			print_r ( json_encode ($err_message) );
			exit ();
		} else {
			$err_message = urlencode ( $err_message );
			header ( "Location: $hostname/index.php?error=$err_message" );
			exit ();
		}
	} 
     //....................
	
	if (! isset ( $status_obj->error ) && !($error2 == 'error')) {
		$status_obj->success = 'Your message was posted successfully.';
	} else {
		$err_message = urlencode("Error! Please try again.");
	}
     logger($script_path."  after twitter post3:_obj: ", $status_obj);
     
     if($web_api_call){
     	print_r(json_encode($status_obj));
     	exit;
     }

   $_SESSION['update_tw']=1;
	
	if (empty ( $err_message )) {
		header ( "Location: $status_url" );
		exit ();
	} else {
		header ( "Location: $hostname/index.php?error=$err_message" ); //
		exit (); 
	}
}
//.............................................................................................//
function tw_page1($prefix, $message) {
	
	//configuration parameters:
	$config_params = Config::getConfigParams();
	$hostname = $config_params['hostname'];
	$consumer_key = $config_params['consumer_key'];
	$consumer_secret = $config_params['consumer_secret'];
	$oauth_callback = $config_params['oauth_callback'];
	$docroot = $config_params['docroot'];
	$debug = $config_params['debug']; 
	
	$script_path = __FUNCTION__;
	
	if (session_id () == "") session_start ();
	
	require_once $docroot . '/twitter/twitteroauth/twitteroauth.php';
	
	logger ( $script_path."  Top:" );
	
	$_SESSION ['message'] = $message;
	$_SESSION ['prefix'] = $prefix;
	
	/* Build TwitterOAuth object with client credentials. */
	$connection = new TwitterOAuth ( $consumer_key, $consumer_secret );
	
	/* Get temporary credentials. */
	$request_token = $connection->getRequestToken ( $oauth_callback );
	
	logger ( $script_path."  After Request Token:" );
	
	/* Save temporary credentials to session. */
	$_SESSION ['oauth_token'] = $token = $request_token ['oauth_token'];
	$_SESSION ['oauth_token_secret'] = $request_token ['oauth_token_secret'];
	
	/* If last connection failed don't display authorization link. */
	switch ($connection->http_code) {
		case 200:
    /* Build authorize URL and redirect user to Twitter. */
    $url = $connection->getAuthorizeURL ( $token );
			header ( 'Location: ' . $url );
			exit ();
			break;
		default:
    /* Show notification if something went wrong. */
			$err_message= urlencode("Error! Could not connect to Twitter. Please try again.");
			header("Location: $hostname/index.php?error=$err_message");
  			exit;
	}
}
//......................................................................................................//
function ln_page1($prefix, $message){
	
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$linkedin_access = $config_params['linkedin_access'];
		$linkedin_secret = $config_params['linkedin_secret'];
		$docroot = $config_params['docroot'];
		$debug = $config_params['debug']; 
		
		$script_path = __FUNCTION__;
	
        if (session_id() == "") session_start();

	    include_once $docroot."/linkedin/linkedin.php";
	 
	    $config['base_url']             =   "$hostname/linkedin/page1.php";
	    $config['callback_url']         =   "$hostname/linkedin/page2.php";
	    $config['linkedin_access']      =   $linkedin_access;
	    $config['linkedin_secret']      =   $linkedin_secret;

	    logger($script_path."  Ln/Top: "); 
            
	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
	    //$linkedin->debug = true;
            
	    # Now we retrieve a request token. It will be set as $linkedin->request_token
	    $linkedin->getRequestToken();

            logger($script_path."  request_token: ", $linkedin->request_token);
             
	    $_SESSION['requestToken'] = serialize($linkedin->request_token);

            $_SESSION['message'] = $message;//
            $_SESSION['prefix'] = $prefix;
	 
	    # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
	    
	    header("Location: " . $linkedin->generateAuthorizeUrl());
            exit;
}
//........................................................................................................//
function logger($message, $data=null, $delete_file=false){
  
	//configuration parameters:
  	$config_params = Config::getConfigParams();
	$docroot = $config_params['docroot'];
	$debug = $config_params['debug']; 
	$hostname = $config_params['hostname'];
	
	if ($debug) {
		//flush memcache periodically...
		//another option restart the memcached daemon
		//$model = new TwextraModel ();
		//$model->mc_flush ();
	}
	
	//logging is only for debugging in dev (twetest) environment
	if($hostname != 'http://twetest.com'){
		//return;
	}
  
  $filename = $docroot."/logs/logs.txt";
  $filesize_max = 100000; //100kB
  
  if(!file_exists($filename)){
      touch ($filename);
  }else if($delete_file || (filesize($filename) > $filesize_max)){
  	  	$handle = fopen($filename, 'r+');
		@ftruncate($handle, 0);
		@fclose($handle);
  }

  $out = '';
  if($debug==1){

    //$out .= "//................... logger ...............................//\n";
  
    //$out .= print_r($message,true)."\n";
    $out .= print_r($message,true);
    $out .= print_r($data, true)."\n";
   // $out .= date("H:i:s:U")."\n";//uncomment after testing

    //write to log file (logs/logs.txt)
    $fh = fopen($filename, "a");
    fwrite($fh, $out);
    fclose($fh);

  }
}
//.......................................................................................................//
function index($tweet='', $error = '', $message_id = '') {
  if (session_id () == "") session_start ();
  	logger($script_path." index start:");
  	
	//configuration parameters:
	$config_params = Config::getConfigParams();
	$hostname = $config_params['hostname'];
	$watch_demo = $config_params['watch_demo'];
	$docroot = $config_params['docroot'];
	$debug = $config_params['debug']; 
	$enable_stats = $config_params ['enable_stats'];
	$header = header_html();//
	$footer = $config_params['footer']; 
	$doctype = $config_params['doctype']; 
	$html_attribute = $config_params['html_attribute']; 
	$css = $config_params['css']; 
	$google_analytics = $config_params['google_analytics']; 
	$godaddy_analytics = $config_params['godaddy_analytics']; 
	$cookie_name = $config_params['cookie_name'];
	$ep4 = $config_params['ep4'];
	$show_maintenance_page = $config_params['show_maintenance_page'];
	$fb_app_id = $config_params ['fb_app_id'];
	$search_count_offset = $config_params ['search_count_offset'];
	
	if($show_maintenance_page == 1){
		maintenance_page();
	}
	
	if(isset($_REQUEST['screen_name_reply'])){
		$screen_name_reply = $_REQUEST['screen_name_reply'];
	}else{
		$screen_name_reply = '';
	}
	
	if(isset($_REQUEST['message_id_reply'])){
		$message_id_reply  = $_REQUEST['message_id_reply'];
	}else{
		$message_id_reply = '';
	}
	
	logger($script_path."  index page: ");
	logger($script_path." cookie:", $_COOKIE);
	
	//---------------------------------------------------
	   $model = new TwextraModel ( );
	   $message_totals = $model->get_message_totals();
    //round to nearest thousand:
    $message_totals = $message_totals - ($message_totals % 1000);
    //$message_totals = $message_totals - $search_count_offset;
    $message_totals = number_format($message_totals);//format into human readable form
	//---------------------------------------------------
		
	//get twitter credentials and store in session variables for use in banner
	if (isset ( $_COOKIE [$cookie_name] )) {
		$controller = new TwextraController();
		$result_t = $controller->getTwCredentials ( $_COOKIE [$cookie_name] ); //
		$screen_name = $result_t['screen_name'];
		logger($script_path." result_t:", $result_t);
	}
	
	$script_path = __FUNCTION__;
	
	logger($script_path."  session: ", $_SESSION);
	logger($script_path."  request: ", $_REQUEST);
	
	//save logs
	if ($enable_stats) {
		$model = new TwextraModel ( );
		$model->saveStat ();
	}
		
	//set error flag if a twitter error, or any other error
	if ((isset ( $_REQUEST ['error'] )) && ($_REQUEST ['error'] == 'twitter_error')) {
		$error = "Twitter is returning an error right now. Please try again later. 
		Follow <a href='http://twitter.com/twextradotcom' target='_blank' >@twextradotcom</a> for updates.";
	} else if (isset ( $_REQUEST ['error'] ) && $_REQUEST ['error'] == 'ep4') {
		$error = $ep4;
	} else if (isset ( $_REQUEST ['error'] )) {
		$error = urldecode ( $_REQUEST ['error'] );
	} else if (isset ( $_REQUEST ['signout'] )) {
		$error = "You are now signed out of Twextra, but you may still be signed in on Twitter.  " . $error;
		$_SESSION['user']='';
	} else if (isset ( $_SESSION ['error_size'] )) {
		$error = $_SESSION ['error_size'];
		unset ( $_SESSION ['error_size'] );
	}
  
  if(isset($_SESSION['tweet'])){
  	$tweet = $_SESSION['tweet'];
  	unset($_SESSION['tweet']);
  }
	
	if (! empty ( $screen_name )) {
		$twitter_checked = 'checked';
		$twextra_checked = '';
	} else if (! empty ( $screen_name_reply )) {
		$twitter_checked = 'checked';
		$twextra_checked = '';
	} else {
		$twitter_checked = '';
		$twextra_checked = 'checked';
	}
  
  if(!empty($message_id)){
  	   	$model = new TwextraModel();
		$result = $model->readTweet ( $message_id, false ); //
		$tweet = $result['tweet'];
  }else if($screen_name_reply != ''){
  	$tweet = '@'.$screen_name_reply.'&nbsp;';
  }
  
   $docroot = $_SERVER['DOCUMENT_ROOT'];
   
	$useragent = $_SERVER ['HTTP_USER_AGENT'];
	$useragent = strtolower($useragent);
	$useragent = "  ".$useragent;//so that strpos works fine..
	
//	$blackberry = preg_match ( "/blackberry/i", $useragent );
//	$iphone = preg_match ( "/iphone/i", $useragent );
	
	$blackberry = strpos($useragent, "blackberry");
	$iphone = strpos($useragent, "iphone");
	$symbian = strpos($useragent, "symbian");
	$android = strpos($useragent, "android");
	$nokia = strpos($useragent, "nokia");
	$samsung = strpos($useragent, "samsung");
	$sony = strpos($useragent, "sony");
	$vodafone = strpos($useragent, "vodafone");
	$nintendo = strpos($useragent, "nintendo");
	$sprint = strpos($useragent, "sprint");
	$playstation = strpos($useragent, "playstation");
	$mot = strpos($useragent, "mot");
	$docomo = strpos($useragent, "docomo");
	$palm = strpos($useragent, "palm");
	$avantgo = strpos($useragent, "avantgo");
	
	if ($blackberry || $iphone || $symbian || $android || $nokia || $samsung || $sony || $vodafone || $nintendo || $sprint
	     || $playstation || $mot || $docomo || $palm || $avantgo) {
		$editor = 'editorI';
		$_SESSION['useragent'] = 'device';
	} else {
		$editor = 'editor';
		$_SESSION['useragent'] = 'desktop';
	}
	
	header("Pragma: no-cache");
    header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    
   print $doctype;
   print "<html $html_attribute >";

echo header_html();

$body_top ="<body>
<div class='wrapper'>
<div class='page'>";
echo $body_top;
 
if(!empty($error)){
	$screen_name = '';
	$twitter_checked= '';
	$twextra_checked='checked';
}

$banner = banner($screen_name, 'banner_index');//(user, banner_class)

echo $banner;

logger("index/hostname:", $hostname);

$message = '';
?>


<div class='p1main'>
<div class='p1main_inner'>
<div class='p1desc'>

<?php //print any error messages here: ?>
<?php if(!empty($error)){ ?>
<div class='p1main_form_label' id='p1main_form_label'
	style='border: solid 2px red; padding: 10px; background-color: pink; width: 744px; margin: 10px 0;'> 
	<?php echo $error ?>
	<a href='javascript: hide_message_status("p1main_form_label")'
	style='float: right; font-size: 0.8em;'>Close</a></div>
<?php } ?>

<div style='float: left; width: 768px; padding-bottom: 10px;'>
<h4 class='p1main_form_label'>Type your message below:&nbsp;</h4>
<div style='font-size: 11px; float: left;'>(<a
	href=<?php echo $hostname.'/howto.php'; ?>>Tips for Videos, Images, etc</a>)</div>

<br style='clear: both' />
</div>

<?php 
$bookmarklet = "
<div class='bookmarklet_outer' style='width: 300px;line-height: 150%;'>
       <div>
       <a class='bookmarklet_inner' href=\"javascript:(function(){_twextra_bookmarklet=document.createElement('SCRIPT');
        _twextra_bookmarklet.type='text/javascript';_twextra_bookmarklet.src='http://twetest.com/scripts/twextra_bookmarklet.js?
        x='+(Math.random());document.getElementsByTagName('head')[0].appendChild(_twextra_bookmarklet);})();\">
        Twextra Bookmarklet</a>
        </div>
</div>";

//echo $bookmarklet;
?>

<?php //form and editor; ?>
<form method='post' action='/router.php' id='tweet_post'
	accept-charset="utf-8" class='p1form'><textarea class='ckeditor'
	id="<?php echo $editor ?>" name="<?php echo $editor; ?>" rows="15"
	cols="100" style="width: 768px"><?php echo $tweet; ?>
</textarea> <script type="text/javascript">
	CKEDITOR.replace( 'editor' );
</script> <input type='hidden' name='route' value='tweet_post'></input>
<input type='hidden' name='message_id_reply'
	value="<?php echo $message_id_reply; ?>"></input> <input type='hidden'
	name='message_id' value="<?php echo $message_id; ?>"></input>

<div class='p1form_buttons'><input type="submit" name="save" id="save"
	value="Post Message" class='button'></input></div><?php //p1form_buttons; ?>

<?php //show buttons: ?>	
<div class='p1form_social_buttons'>
<div
	style='font-size: 18px; border-bottom: 1px solid black; margin-bottom: 10px;'>Post
message to:</div>

<div style='margin-top: 5px;'><input type="radio" name="social"
	value="twitter" <?php echo $twitter_checked; ?>
	style="margin-left: 0px; float: left; margin-top: 5px; margin-right: 10px;"></input>
<img src="/images/twitter_100px.png" /></div>

<div style='margin-top: 5px; margin-bottom: 5px;'><input type="radio"
	name="social" value="linkedin"
	style="margin-left: 0px; float: left; margin-top: 7px; margin-right: 10px;"></input>
<img src="/images/linkedin_100px.png" /></div>

<div style='margin-top: 5px; margin-bottom: 5px;'><input type="radio"
	name="social" value="twextra" <?php echo $twextra_checked; ?>
	style="margin-left: 0px; float: left; margin-top: 7px; margin-right: 10px;"></input>
<img src="/images/twextra_100px.png" /></div>

</div>
</form>

<br style='clear: both;' />

<?php 
	$message .= "<div class='p1_buttons' >";
		//tweet button
		$message .= "<div class='p1_twitter_button' style='float:right;' >";//p1_twitter_button
		$message .= '<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" 
		>Tweet</a><script type="text/javascript" 
		src="http://platform.twitter.com/widgets.js"></script>';
		$message .= "</div>";//p1_twitter_button
		
		//facebook like button
		$message .= "<div class='p1_facebook_button' style='float:right;' >";//p1_facebook_button
		$message .= "
<fb:like href=\"http://twextra.com\" layout=\"button_count\" show_faces=\"false\" width=\"100px\"></fb:like>

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
		$message .= "</div>";//p1_facebook_button
		//$message .= "<br style='clear:both;' />";  
	$message .= "</div>";//p1_buttons
    $message .= "&nbsp;&nbsp;&nbsp;&nbsp;<h4 style='text-align:center'>Over $message_totals messages created</h4>"; 
	
	echo $message;
?>	
</div>
</div>
</div>
<!-- p1main -->
<a
	style='margin: 20px auto 20px auto; display: block; text-align: center;'
	href='http://twitter.com/melaniejane88'> <img src='/images/twex.jpg'
	style='width: 600px; height: 100px' /></a>
<?php  
echo $footer;
?>
</div>
</div>
<?php echo $godaddy_analytics; ?>
</body>
</html>
<?php } ?>