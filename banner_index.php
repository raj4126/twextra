<?php
if (session_id () == "") session_start ();
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
//.......................................................
function banner_index($user = '') {
	
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
		$message_history = "<div style='float:right;font-size:0.8em;' ><a href='$hostname/displayMessageHistory.php'>
		Message History</a></div>";
		
		$signout = "<span >Sign out</span>";
	
	} else if(isset($_COOKIE['tw_user_id']) && (trim($_COOKIE['tw_user_id']) != '')){
		
		$user_id_hash = $_COOKIE['tw_user_id'];
		$model = new TwextraModel();
		$user = $model->get_screen_name($user_id_hash);
		
		$_SESSION['user'] = $user;
		
		$greeting = '';
		
		$greeting .= "<a href='http://twitter.com/" . $user . "' target='_blank' style='float:right;' >" . $user . "@twitter</a>
			<div style='float:right;' >Hi&nbsp;</div>";
		$message_history = "<div style='float:right;font-size:0.8em;' ><a href='$hostname/displayMessageHistory.php'>
		Message History</a></div>";
		
		$signout = "<span >Sign out</span>";  
		
	}else{
		
		$greeting = '';
		
		$signout = '';
	
	}
	
//.....................................
	//<div class='rbanner'>When you NEED more than 140 characters</div>
	$banner = '';
	$banner .= "
<div class='banner_index'>
<div class='banner_content_index'>
<div class='lbanner'><a href='" . $hostname . "'><img src='/images/twextra_logo.png' alt ='twextra logo' /></a></div>
<div class='rbanner'>Create <span style='font-size:22px;font-weight:bold;'>large</span> (140+), 
<span style='color:blue;background-color:yellow;'>rich-text</span> messages <br /> for Twitter, LinkedIn, or anywhere else
</div>
<br style='clear:both' />
</div>
<div style='float:right; width:200px; margin-right:10px;' >";
	$banner .= "
<div style='float:right; margin-top:10px; width:100%; color:black; font-size:0.8em;' >" . $greeting . "   </div>";
	$banner .= "$message_history
<div style='float:right; clear:right; width:100%; font-size:0.8em;' > ";
	$banner .= "
<a href='" . $hostname . "/signout.php' style='float:right' >" . $signout . " </a></div>
<br style='clear:both;' />
</div>
</div>";
	
	return $banner;
}

?>