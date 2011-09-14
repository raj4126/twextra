<?php

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";

		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$linkedin_access = $config_params['linkedin_access'];
		$linkedin_secret = $config_params['linkedin_secret'];
            
            //require_once $docroot."/sessions.php";  
            //$sess = new SessionManager();
            session_start();

            require_once $docroot."/tw.lib.php";
	 
	    $config['base_url']             =   "$hostname/linkedin/page1.php";
	    $config['callback_url']         =   "$hostname/linkedin/page2.php";
	   // $config['linkedin_access']      =   'YMKaHlPF6xv8YTMs_FftnoC1tq_0Fgoz9Y8me0PvcR1Sm9WxzuPI18hZr2yP3fFq';//twetest
	   // $config['linkedin_secret']      =   'PuKdmBOQFdR1vibAe0LX3yRkKhu-NWlZaqC3EwnsiiMw1OL0EZ_J_rmh5PjzHXfg';//twetest
	    $config['linkedin_access']      =   $linkedin_access;//twetest
	    $config['linkedin_secret']      =   $linkedin_secret;//twetest

	    include_once $docroot."/linkedin/linkedin.php";
	    logger("Page1/Ln/Top: "); 
           
            
	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
	    //$linkedin->debug = true;
            
	 
	    # Now we retrieve a request token. It will be set as $linkedin->request_token
	    $linkedin->getRequestToken();

            logger("Page1/Ln/request_token: ", $linkedin->request_token);
             
	    $_SESSION['requestToken'] = serialize($linkedin->request_token);

            $_SESSION['message'] = $_REQUEST['message'];//
            $_SESSION['prefix'] = $_REQUEST['prefix'];

	 
	    # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
	    
	    header("Location: " . $linkedin->generateAuthorizeUrl());
            exit();
?>