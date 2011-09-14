<?php
	    $docroot = $_SERVER['DOCUMENT_ROOT'];
            require_once $docroot."/tw.lib.php";
            require_once $docroot . "/config.php";
            //require_once $docroot."/sessions.php"; 
            
        $config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$linkedin_access = $config_params['linkedin_access'];
		$linkedin_secret = $config_params['linkedin_secret'];

            //$sess = new SessionManager();
            session_start();

            $config['base_url']         =   "$hostname/linkedin/page1.php";
	    $config['callback_url']         =   "$hostname/linkedin/page2.php";
	   // $config['linkedin_access']      =   'YMKaHlPF6xv8YTMs_FftnoC1tq_0Fgoz9Y8me0PvcR1Sm9WxzuPI18hZr2yP3fFq';//twetest
	   // $config['linkedin_secret']      =   'PuKdmBOQFdR1vibAe0LX3yRkKhu-NWlZaqC3EwnsiiMw1OL0EZ_J_rmh5PjzHXfg';//twetest
	    $config['linkedin_access']      =   $linkedin_access;//twetest
	    $config['linkedin_secret']      =   $linkedin_secret;//twetest

	 
	    include_once $docroot."/linkedin/linkedin.php";

           logger("Ln/Page2: Top");
	 
	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
	    //$linkedin->debug = true;
	 
	   if (isset($_REQUEST['oauth_verifier'])){
	        $_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];
	 
	        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
	        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
	        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);
                  logger("Ln/Page2: access token1: ", $linkedin->access_token);
	 
	        $_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
	        header("Location: " . $config['callback_url']);
	        exit;
	   }
	   else{
	        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
	        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
	        $linkedin->access_token     =   unserialize($_SESSION['oauth_access_token']);
                  logger("Ln/Page2: access token2: ", $linkedin->access_token);
	   }
	 
	    # You now have a $linkedin->access_token and can make calls on behalf of the current member

            //$status = "This is a test".date("Y:m:d  H-i-s");
             $ln_access_token = $linkedin->access_token;
            logger("Ln/Page2: access token3: ", $ln_access_token);

            $status = $_SESSION['message'];
            $prefix = $_SESSION['prefix'];
            $message_id = $_SESSION['message_id'];
            
            $status = "$hostname/$message_id";
            
	    $response = $linkedin->setStatus($prefix.$status);
            $_SESSION['update_li']=1;
            logger("Ln/Page2 status: ", $status);
            header("Location: $status");
            exit;
	 
	?>