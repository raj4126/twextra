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
		$margin_top = 20;
	}else{
		$margin_top=45;
	}
	
	$follow_on_twitter = "<div class='b4' style='float:right;margin-top:".$margin_top."px;width:200px;text-align:right;'>
	<a href='http://twitter.com/TwextraDotCom' target='_blank' > Follow us</a> on Twitter for updates</div>";
		
	$banner = '';
	$banner .= "
<div class='$banner_class' >
<div class='banner_content_index'>
<div class='lbanner'><a href='".$hostname."'><img src='/images/twextra_logo.png' alt ='twextra logo' /></a></div>
<div class='rbanner'>Create <span style='font-size:22px;font-weight:bold;'>large</span> (140+), 
<span style='color:blue;background-color:yellow;'>rich-text</span> messages <br /> for Twitter, LinkedIn, 
   or anywhere else
</div>
<br style='clear:both' />
</div>
<div class='b1' >";
	$banner .= $greeting2;
	$banner .= $message_history;
	
	$banner .= "<div class='b3' > ";
	$banner .= $signinwithtwitter;
	$banner .= $signout2;
	//$banner .= $follow_on_twitter;
	
	$banner .= "</div>";
	$banner .= $follow_on_twitter;
	
	$banner .= "
<br style='clear:both;' />
</div>";
	$banner .= "</div>";
		
	return $banner;
}

?>