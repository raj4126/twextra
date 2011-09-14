<?php 

  if (session_id () == "") session_start ();

  $docroot=$_SERVER['DOCUMENT_ROOT'];

  require_once $docroot."/config.php";

  require_once $docroot . "/models/twextra_model.php";

  require_once $docroot . "/tw.lib.php";

  //require_once $docroot . "/controllers/twextra_controller.php";

  require_once $docroot . "/banner.php";

  require_once $docroot . "/header_html.php";

 search_display();

//.......................................................................................................//

function search_display() {

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









$message = "<div style='float:left;clear:both;margin-left:auto;margin-right:auto;width:800px;'><form method='post' 

action='/webnotes/wrouter.php' accept-charset=\"utf-8\" >

	<input type='hidden' name='wroute' id='wroute' value='searchdisplay'> </input>



	<input id='tag' name='tag' style='width: 768px; border:solid black 1px;'></input> 

<div><input type='submit' name='save' id='save' value='Search' class='button'></input></div>



</form></div>";





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

