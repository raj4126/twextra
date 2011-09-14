<?php
if (session_id () == "") session_start ();
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
//.......................................................
function banner($user = '', $banner_class) {
	
	//$hostname = 'http://twetest.com';
	
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
//...................	
if (session_id () == "") session_start ();
	
	if (isset ( $_SESSION ['user'] ) && (trim ( $_SESSION ['user'] != '' ))) {
		
		$user = $_SESSION ['user'];
		
		$greeting = '';
		
		$greeting .= "<a href='http://twitter.com/" . $user . "' target='_blank' style='float:right;' >" . $user . "@twitter</a>
			<div style='float:right;' >Hi&nbsp;</div>";
		
		$greeting2 = "<div class='b2' >".$greeting. "</div>";
		
		
		$message_history = "<div style='float:right;' ><a href='$hostname/displayMessageHistory.php'>
		Message History</a></div>";
		
		$signout = "<span >Sign out</span>";
		$signout2 = "<a href='" . $hostname . "/signout.php' style='float:right' >" . $signout . " </a>";
		
		
		$signinwithtwitter = '';
	
	} else if(isset($_COOKIE['tw_user_id']) && (trim($_COOKIE['tw_user_id']) != '')){
		
		$user_id_hash = $_COOKIE['tw_user_id'];
		$model = new TwextraModel();
		$user = $model->get_screen_name($user_id_hash);
		
		$_SESSION['user'] = $user;
		
		$greeting = '';
		
		$greeting .= "<a href='http://twitter.com/" . $user . "' target='_blank' style='float:right;' >" . $user . 
		"@twitter</a><div style='float:right;' >Hi&nbsp;</div>";
		
		$greeting2 = "<div class='b2'>".$greeting. "</div>";
		
		$message_history = "<div style='float:right;' >
		<a href='$hostname/displayMessageHistory.php'>
		Message History</a></div>";
		
		$signout = "<span >Sign out</span>";  
		
		$signout2 = "<a href='" . $hostname . "/signout.php' style='float:right' >" . $signout . " </a>";
		
		$signinwithtwitter = '';
		
	}else{
		
		$greeting = '';
		$greeting2 = $greeting;
		
		$signout = '';
		
		$signout2 = $signout;
		
		$signinwithtwitter = "<a href='$hostname/router.php?route=signin_twitter' style='float:right;' >
		<img src='$hostname/images/sign-in-with-twitter-l.png' /></a>";
	
	}
	
//.....................................
	//<div class='rbanner'>When you NEED more than 140 characters</div>
	if(!empty($user)){
		//$margin_top = 20;
		$margin_tops = 0;
		$margin_topf = 5;
	}else{
		//$margin_top=45;
		$margin_tops=25; 
		$margin_topf=5; 
	}
	
	$follow_on_twitter = "<div class='b4' style='float:right;margin-top:".$margin_topf."px;width:265px;text-align:right;'>
	Follow us for updates: <a href='http://twitter.com/TwextraDotCom' target='_blank' >Twitter</a> | <a href='http://www.facebook.com/twextra' target='_blank' >Facebook</a></div>";
		
	$banner = '';
	$banner .= "
<div class='$banner_class' >
<div class='banner_content_index'>
<div class='lbanner'><a href='".$hostname."'><img src='/images/twextra_logo.png' alt ='twextra logo' /></a></div>

<div class='rbanner'>Create posts with rich-text, images, & videos.<br/>
Share them on Twitter, LinkedIn, or wherever.
</div>

<br style='clear:both' />
</div>
<div class='b1' >";
	$banner .= $greeting2;
	$banner .= $message_history;
	
	$banner .= "<div class='b3' > ";
	$banner .= $signinwithtwitter;
	$banner .= "<div  style='float:right;margin-top:{$margin_tops}px; width:265px;'> ";
	$banner .= "<a href='$hostname/searchTwextra.php' style='float:right'>Search Twextra</a>"; 
	$banner .= "</div>";
    $banner .= "<br style='clear:both' />";
	$banner .= $signout2;
	//$banner .= $follow_on_twitter;
	
	$banner .= "</div>";

   // $banner .= "<br style='clear:both' />";
	
	$banner .= $follow_on_twitter;
	
	$banner .= "
<br style='clear:both;' />
</div>";
	$banner .= "</div>";
		
	return $banner;
}

?>