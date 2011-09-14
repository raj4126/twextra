<?php
if (session_id () == "") session_start ();

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/tw.lib.php";

class WebNotes {

    function create ($data){
    	
    	validate_access_webnotes();

        //configuration parameters:
        $config_params = Config::getConfigParams();

        $hostname = $config_params['hostname'];

        $tweet_size_max = $config_params['tweet_size_max'];

        $prefix_size_max = $config_params['prefix_size_max'];

        $docroot = $config_params['docroot'];

        $debug = $config_params['debug'];

        $cookie_name = $config_params['cookie_name'];

        $script_path = __FUNCTION__;

        //logger ( $script_path."  Topx: ", "", false ); //start with the same log file
        logger($script_path . "  Topx: ", "", true); //start with a new log file
        logger("//..............................................START A NEW TRANSACTION..........................
        ............................................//");

        $note = '';

        $wid = - 1;
        
        header("Location:$hostname/webnotes/create.php");
        exit();
	}
//.........................................................................
	function display($data){
		validate_access_webnotes();
        header("Location:$hostname/webnotes/display.php");
        exit();	
	}
//.........................................................................
	function search($data){
        validate_access_webnotes();
        header("Location:$hostname/webnotes/search.php");
        exit();	
	}
//....................................................................
	function searchdisplay($data){
		validate_access_webnotes();
		$user = 'raj4126';
        $tag = $data['tag'];
        
        header("Location:$hostname/webnotes/display.php?action=searchdisplay&tag=$tag");
        exit();	
	}
//.........................................................................
	function editdisplay($data){
		validate_access_webnotes();
        //$user = 'raj4126';
        //$note = $data['weditor'];
        //$tag = $data['tag'];
        //$action = $data['action'];
        $wid = $data['wid'];
        
        header("Location:$hostname/webnotes/create.php?wid=$wid&action=edit");
        exit();	
	}
//.........................................................................
	function editsave($data){
		validate_access_webnotes();
        $user = 'raj4126';
        $note = $data['weditor'];
        $tag = $data['tag'];
        //$action = $data['action'];
        $wid = $data['wid'];
        
        $model = new TwextraModel ();
		$model-> saveNote($user, $note, $tag, $action = 'edit', $wid);
        
        header("Location:$hostname/webnotes/display.php");
        exit();	
	}
//.........................................................................
	function deletec($data){
		validate_access_webnotes();
		$wid = $data['wid'];
        $model = new TwextraModel();
        $model->deleteNote($wid);
        header("Location:$hostname/webnotes/display.php");
        exit();	
	}
//.........................................................................
	function indexc($data){
		validate_access_webnotes();
	    if (isset($data['weditor'])) {
        $user = 'raj4126';
        $note = $data['weditor'];
        $tag = $data['tag'];
        $action = $data['action'];
        $wid = $data['wid'];
        $model = new TwextraModel();
        $model->saveNote($user, $note, $tag, $action, $wid);
    }
		
        header("Location:$hostname/webnotes/index.php");
        exit();	
	}
//..............................................................................
}
?>