<?php 

  if (session_id () == "") session_start ();

  $docroot=$_SERVER['DOCUMENT_ROOT'];

  require_once $docroot."/config.php";

  require_once $docroot . "/models/twextra_model.php";

  require_once $docroot . "/tw.lib.php";

  require_once $docroot . "/banner.php";

  require_once $docroot . "/header_html.php";

 notes_display();

//.......................................................................................................//

function notes_display() {

  if (session_id () == "") session_start ();

  	logger($script_path." index start:");

  	validate_access_webnotes();

	//configuration parameters:

	$config_params = Config::getConfigParams();

	$hostname = $config_params['hostname'];

	$watch_demo = $config_params['watch_demo'];

	$docroot = $config_params['docroot'];

	$debug = $config_params['debug']; 

	$header = header_html();//

	$footer = $config_params['footer']; 

	$doctype = $config_params['doctype']; 

	$html_attribute = $config_params['html_attribute']; 

	$css = $config_params['css']; 

	$google_analytics = $config_params['google_analytics']; 

	$godaddy_analytics = $config_params['godaddy_analytics']; 

	$cookie_name = $config_params['cookie_name'];

	$ep4 = $config_params['ep4'];

  

   $docroot = $_SERVER['DOCUMENT_ROOT'];

   

   $user = $_SESSION['user'];//??

   $user = 'raj4126';

    if ($_REQUEST['action'] == 'searchdisplay') {

        $tag = $_REQUEST['tag'];

        $model = new TwextraModel();

        $notes_all_array = $model->searchNote($user, $tag);

    } else {

        $model = new TwextraModel();

        $notes_all_array = $model->getNotesAll($user);

    }

	

	header("Pragma: no-cache");

    header("cache-Control: no-cache, must-revalidate"); // HTTP/1.1

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

    

   print $doctype;

   print "<html $html_attribute >";



$message = "

<head>

<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\" />

<title>Twextra- When you NEED more than 140 characters</title>";



echo $message;



$scripts = '';

$scripts .= "<script type='text/javascript' src='/scripts/jquery/jquery-1.2.6.js'></script>\n";



echo $scripts;



$message = "

<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"/favicon.ico\" />

</head>

<body>

<div class='wrapper'>

<div class='page'>";



echo $message; 



$banner = banner($screen_name, 'banner_index');//(user, banner_class)



echo $banner;



$message = "<div style='clear:both;margin-left:auto;margin-right:auto;width:800px;'>

Your webnotes are displayed below:";



$message .= "<table class='w_display_table' >";

$message .= "<tr class='w_display_tr' >

<th class='w_display_th'>Note</th>

<th class='w_display_th'>Tags</th>

<th class='w_display_th'>Updated</th>

<th class='w_display_th'>Actions</th></tr>";



foreach($notes_all_array as $entry){

    

    $message .= "<tr class='w_display_tr' >

    <td class='w_display_td'>{$entry['note']}</td>

    <td class='w_display_td' >{$entry['tag']}</td>

    <td class='w_display_td'>{$entry['created']}</td>

    <td class='w_display_td'><a href=$hostname/webnotes/wrouter.php?wid={$entry['wid']}&wroute=edit >Edit</a>

        <span id='delete' style='color:blue;' onclick=_edit('$hostname',{$entry['wid']})>Delete</span>

        <a href=$hostname/webnotes/wrouter.php?wid={$entry['wid']}&wroute=share>Share</a>

    </td></tr>";

}

$message .= "</table>";

$message .= "</div>";



echo $message;



echo $footer;



$message = "

</div>

</div>

</body>

</html>";



echo $message;

}

?>



<script type='text/javascript'>



function _edit(hostname, wid){

	var test;



	test = confirm("Are you sure, you want to delete this webnote?");

	if(test == 1){

		window.location = hostname+"/webnotes/wrouter.php?wid="+wid+"&wroute=delete";

	}

}



</script>

