<?php
/*************************************************
 * *Global router script: router.php
 *************************************************/
if (session_id () == "") session_start ();
$docroot = $_SERVER['DOCUMENT_ROOT'];

//configuration parameters:
require_once $docroot."/config.php";
require_once $docroot."/controllers/twextra_controller.php";
require_once $docroot . "/tw.lib.php";
$config_params = Config::getConfigParams();
$hostname = $config_params['hostname'];

$data = $_REQUEST;
$twextra = new TwextraController();

switch($data['route']){
	case 'tweet_post':
		$twextra->postTweet($data);
		break;
		
	case 'tweet_show':
		$twextra->showTweet($data);
		break;

	case 'tweet_translate':
		$twextra->translateTweet($data);
		break;
		
	case 'update_twitter':
		$twextra->updateTwitter($data);
		break;
		
	case 'api_update_json':
		$twextra->apiUpdateJson($data);
		break;
		

    case 'signout':
        header("Location: " . $hostname . "?signout=true");
        exit();
        break;
        
    case 'delete_msg':
    	$twextra->delete_message($data);
    	break;

    case 'signin_twitter':
        $_SESSION['signin_with_twitter'] = 'yes';
        $prefix = '';
        $message = '';
        tw_page1($prefix, $message);
        break;
        
    case 'default':
        //error
        exit(1);		
}

///////////////

  //require_once $docroot . "/models/twextra_model.php";







?>