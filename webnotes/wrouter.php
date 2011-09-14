<?php
/*************************************************
 * *Global router script: router.php
 *************************************************/
if (session_id () == "") session_start ();
$docroot = $_SERVER['DOCUMENT_ROOT'];

//configuration parameters:
require_once $docroot."/config.php";
require_once $docroot . "/webnotes/wcontroller.php";
$config_params = Config::getConfigParams();
$hostname = $config_params['hostname'];

$data = $_REQUEST;
$webnotes = new WebNotes();

switch($data['wroute']){
	case 'create':
		$webnotes->create($data);
		break;
		
	case 'display':
		$webnotes->display($data);
		break;
		
    case 'search':

		$webnotes->search($data);

        break;
        
    case 'searchdisplay':

		$webnotes->searchdisplay($data);

        break;
        
    case 'index':
		$webnotes->indexc($data);

        break;
        
    case 'edit':
		$webnotes->editdisplay($data);

        break;
        
    case 'editsave':
		$webnotes->editsave($data);

        break;
        
    case 'delete':
		$webnotes->deletec($data);

        break;
        

    case 'default':

        //error
        exit(1);		
}

?>