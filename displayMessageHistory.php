<?php

if (session_id () == "") session_start ();
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/controllers/twextra_controller.php";
require_once $docroot . "/banner.php";
require_once $docroot . "/header_html.php";

require_once $docroot . "/tw.lib.php";

//validate access;
validate_access_twetest();

//for admin testing purposes, you can provide screen name in the query string
if (validate_access_admin_test () && isset ( $_REQUEST ['screen_name'] )) {

	$screen_name = $_REQUEST ['screen_name'];

} else {

	$screen_name = $_SESSION ['user'];

}

displayMessageHistory($screen_name);

//.........................................................................................
function displayMessageHistory($screen_name='') {
	
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
	$show_maintenance_page = $config_params ['show_maintenance_page'];
	$print_full_message_list = $config_params ['print_full_message_list'];
	$search_len_max = $config_params ['search_len_max'];
	$search_len_size = $config_params ['search_len_size'];
		
	if ($show_maintenance_page == 1) {
		maintenance_page ();
	}
	
	$script_path = __FUNCTION__;
	
	if(empty($screen_name)){
		header("location: $hostname/index.php?error=ep4");
		exit;
	}
	
	//save logs
	if ($enable_stats) {
		$model = new TwextraModel (); //
		$model->saveStat ();
	}
	
	if (isset ( $_REQUEST ['message_from'] )) {
		$message_from = $_REQUEST ['message_from'];
		//validate data..
		
	} else {
		$message_from = 0;
	}
	
	if (isset ( $_REQUEST ['order'] )) {
		$order = $_REQUEST ['order'];
		//validate data..	
	}
	
	if (isset ( $_REQUEST ['toggle'] )) {
		if ($_REQUEST ['asc_desc'] == 'asc') {
			$asc_desc = 'desc';
		} else{
			$asc_desc = 'asc';
		}
	} else {
		$asc_desc = $_REQUEST ['asc_desc'];
	}
	
	//if not set, define the default values
	if (empty ( $order )) {
		$order = 'created';
	}
	if (empty ( $asc_desc )) {
		$asc_desc = 'desc';
	}
	

	
	if(isset($_REQUEST['less'])){
		$next = 'less';
	}else{
		$next = 'more';
	}
	$search = $_REQUEST['search'];
	
	$model = new TwextraModel (); //
	//$message_id, $created, $view_count, $last_viewed, $prefix
	$length = 20;//number of messages to get
	$message_history = $model->get_message_history($screen_name, $message_from, $next, $order, $asc_desc, $length, $search);
	
    $msg_cnt = $message_history[0]['msg_cnt'];
    $msg_pages = ceil($msg_cnt/20);
    $msg_current = ceil($message_from/20) + 1;
    $msg_page_of_pages = "Page $msg_current of $msg_pages";

    $model = new TwextraModel ();
	$user_info = $model->get_user_info ( $message_history[0]['message_id'] );
	
	$display_remote_ip = trim ( $_SERVER ['REMOTE_ADDR'] );
	logger ( $script_path . "  display_remote_ip: ", $display_remote_ip );
	
	//don't count twitter bot access for correct view count..	
	if ($_SERVER ['REMOTE_ADDR'] == '128.242.241.134') {
		//$view_inc = false;
		return;
	}
		
	$screen_name = trim ( $user_info ['screen_name'] ); //screen name of poster
	$name = trim ( $user_info ['name'] );
	$location = trim ( $user_info ['location'] );
	$description = trim ( $user_info ['description'] );
	$user_image_url = trim ( $user_info ['user_image_url'] );
	if ($screen_name == '') {
		$message_created_by = "Twitter Message History";
	} else {
		if (isset ( $_SESSION ['profile_image_url'] )) {
			$message_created_by = ""; //redundant, was used to concatenate profile_image_url
		}
		$message_created_by = " <span style='float:left'>&nbsp;&nbsp;<a href='$hostname/displayMessageHistory.php'>$screen_name's</a> Twitter Message History (Total: $msg_cnt)</span>"; 
		$message_created_by .= "&nbsp;&nbsp;&nbsp;&nbsp;<span style='float:right'>
		<input type='text' name='search' size='$search_len_size' maxlength='$search_len_max' value='$search' id='search' ></input>
		<input class='button_del' type='submit' value='Search history' name='submit'  />
		</span>";
		
		$message_created_by .= "<br style='clear:both; width:100%' />";//test
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
	
	$message .= "<div class='p5_main'>\n"; //p5_main
	
	$message .= "<div class='p5_main_inner'>\n";//p5_main_inner
	
	
	$message .= "<div class='p5_tweet'>"; //p5_tweet
	

	if ($msg_cnt > 0) {
		$message .= "<form method='post' action='/displayMessageHistory.php'>";
		$message .= "<p style='text-align:center;font-weight:bold;' >$message_created_by</p>";
		$message .= "</form>";
		$message .= "<form method='post'  action='/message_delete.php' >";
		$message .= "<table class='p5_message_history' >";
		$message .= "
	<tr>
	<th class='p5_th'>ID</th>
	<th class='p5_th'><a href='$hostname/displayMessageHistory.php?order=prefix&message_from=0&more&asc_desc=$asc_desc&toggle&search=$search'>
	Message</a></th>
	<th class='p5_th'><a href='$hostname/displayMessageHistory.php?order=created&message_from=0&more&asc_desc=$asc_desc&toggle&search=$search'>
	Created</a></th>
	<th class='p5_th'><a href='$hostname/displayMessageHistory.php?order=last_viewed&message_from=0&more&asc_desc=$asc_desc&toggle&search=$search'>
	Last Viewed</a></th>
	<th class='p5_th'><a href='$hostname/displayMessageHistory.php?order=views&message_from=0&more&asc_desc=$asc_desc&toggle&search=$search'>
	Views</a></th>
	<th class='p5_th'><input class='button_del' type='submit' value='Delete' name='submit' onclick='return _delete()' /></th>
	</tr>
	";
		$message_history_row = 'p5_message_history_row_odd';
		
		$message_from_more = $message_from + 20;
		$message_from_less = $message_from - 20;
		if ($message_from_less < 0) {
			$message_from_less = 0;
		}
		
		$more = "<a href='$hostname/displayMessageHistory.php?message_from=$message_from_more&more&order=$order&asc_desc=$asc_desc&search=$search'
	                    >Next ></a>";
		$less = "<a href='$hostname/displayMessageHistory.php?message_from=$message_from_less&less&order=$order&asc_desc=$asc_desc&search=$search'
	                   >&lt; Previous</a>";
		
		foreach ( $message_history as $id=>$message_entry ) {
			
			if (! empty ( $message_entry ['last_viewed'] )) {
				//$message_last_viewed = date ( "Y-m-d", $message_entry ['last_viewed'] );
				$message_last_viewed = last_viewed ( $message_entry ['last_viewed'] );
			} else {
				$message_last_viewed = '';
			}
			$message_id = $message_entry ['message_id'];
			$prefix_size_max = 130;
			$message_prefix = substr ( $message_entry ['prefix'], 0, $prefix_size_max );
			if (strlen ( $message_prefix ) == $prefix_size_max) {
				$message_prefix = $message_prefix . '...';
			}
			$message_id_link = "<a href='$hostname/$message_id' target='_blank' >$message_id</a>";
			$message .= "<tr class='$message_history_row' >
		                 <td class='p5_tc1' >" . $message_id_link . "</td>
		                 <td class='p5_tc2' >" . $message_prefix . "</td>
		                 <td class='p5_tc3' >" . $message_entry ['created_date'] . "</td>
		                 <td class='p5_tc4' >" . $message_last_viewed . "</td>
		                 <td class='p5_tc5' >" . $message_entry ['view_count'] . "</td>
		                 <td class='p5_tc5' ><input type='checkbox' name='delete[$id]' value='".$message_id."' /> </td>
		            </tr>";
			
			if ($message_history_row == 'p5_message_history_row_even') {
				$message_history_row = 'p5_message_history_row_odd';
			} else {
				$message_history_row = 'p5_message_history_row_even';
			}
		}
		
		if ($message_from == 0) {
			$less = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		
		if (count ( $message_history ) != 20) {
			$more = '';
		}
		
		$message .= "</table>";
		
//		$message .= "<div style='float:right;margin-top:10px;' >
//		<input class='button' type='submit' value='Delete' name='submit' onclick='return _delete()' /></div>";
		$message .= "</form>";
	} else {
		if($search==''){
		$message .= "<div style='text-align:center;'>You have no Twitter Messages yet. 
		                   <a href='$hostname/index.php'>Create one now!</a></div>";
		}else{
		$message .= "<div style='text-align:center;'>No messages found with the phrase: $search 
		                   </div>";
		}
	}
	$message .= "<div style='margin-top:20px; font-weight:bold;'>"; //marker a
	if ($msg_cnt > 0) {
		$message .= "<div  class='p5_pofp1' >$less&nbsp;&nbsp;$more</div>";
		$message .= "<div class='p5_pofp2' >$msg_page_of_pages</div>";
	}
	$message .= "<br style='clear:both;' />";
	$message .= "</div>";//marker a
	
	$message .= "</div>\n"; //p5_tweet

	echo $message;
	
	$message = "";
	
	$message .= "<br style='clear:both;'>\n";
	$message .= "</div>\n"; //p5_main_inner

	$message .= "<br style='clear:both;' />";
	$message .= "</div>\n"; //p5_main

	$message .= "<br style='clear:both; width:100%' />";//test
	$message .= $footer;
	$message .= "</div>\n"; //page
	$message .= "</div>\n"; //wrapper

	$message .= $godaddy_analytics;
	$message .= "</body>\n</html>\n";
	
	echo $message;
}
	
?>
<script type='text/javascript'>

function _delete(){

	var test;

	test = confirm("Are you sure you want to delete these selected message(s)?");

	if(test){
		return true;           
	}else{
		return false;
	}
}

function _clear(){

    $('#search').val('');
    return true;
}


</script>