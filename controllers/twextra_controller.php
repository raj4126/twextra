<?php
if (session_id () == "") session_start ();

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/tw.lib.php";
require_once $docroot."/twitter/twitteroauth/twitteroauth.php";
require_once $docroot."/twitter/config.php";
require_once $docroot."/lib/embed_in_link.php";

//validate access;
validate_access_twetest();

class TwextraController {
	
	public $language_list = array ('af' => 'Afrikaans', 'sq' => 'Albanian', 'ar' => 'Arabic', 'be' => 'Belarusian', 'bg' => 'Bulgarian', 'ca' => 'Catalan', 'zh' => 'Chinese', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'en' => 'English', 'et' => 'Estonian', 'fil' => 'Filipino', 'fi' => 'Finnish', 'fr' => 'French', 'gl' => 'Galician', 'de' => 'German', 'el' => 'Greek', 'he' => 'Hebrew', 'hi' => 'Hindi', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'id' => 'Indonesian', 'ga' => 'Irish', 'it' => 'Italian', 'ja' => 'Japanese', 'ko' => 'Korean', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'mk' => 'Macedonian', 'ms' => 'Malay', 'mt' => 'Maltese', 'no' => 'Norwegian', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ro' => 'Romanian', 'ru' => 'Russian', 'sr' => 'Serbian', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'es' => 'Spanish', 'sw' => 'Swahili', 'sv' => 'Swedish', 'th' => 'Thai', 'tr' => 'Turkish', 'uk' => 'Ukrainian', 'vi' => 'Vietnamese', 'cy' => 'Welsh', 'yi' => 'Yiddish' );
	
	function postTweet($data) {
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$tweet_size_max = $config_params['tweet_size_max'];
		$prefix_size_max = $config_params['prefix_size_max'];
		$docroot = $config_params['docroot'];
		$debug = $config_params['debug']; 
		$enable_stats = $config_params ['enable_stats'];
		$cookie_name = $config_params['cookie_name']; 
		
		$script_path = __FUNCTION__;
		
		//save logs
		if($enable_stats){
			$model = new TwextraModel();
			$model->saveStat();
		}
		
		//save the tweet if the submitted form is not empty
		
		//logger ( $script_path."  Topx: ", "", false ); //start with the same log file
		logger ( $script_path."  Topx: ", "", true ); //start with a new log file
        logger("//..............................................START A NEW TRANSACTION..........................
        ............................................//");
		logger($script_path."  IP Address: ", $_SERVER['REMOTE_ADDR']);
		logger($script_path."  userAgent: ", $_SERVER ['HTTP_USER_AGENT']);
		logger($script_path."  request array: ", $data);
        logger($script_path."  get memory usage: ", memory_get_usage(true));
		
		$_SESSION['controller_remote_ip'] = $_SERVER['REMOTE_ADDR'];
		
		$twitter_access_token = '';
		$twitter_access_token_secret = '';
		$twextra_key = '';
		$twitter_user_id = '';
		$twitter_user_id_hash = '';
		$result_t = '';//
		
		if (isset ( $data )) {
			
			if (isset ( $data ['editor1'] )) {
				$tweet_raw = $data ['editor1'];
			} elseif (isset ( $data ['editorI'] )) {
				$tweet_raw = $data ['editorI'];
			} elseif(isset($data['editor'])){
				$tweet_raw = $data['editor'];
			}
			
			
			//for api access
			if(isset($data['twextra_key'])){
				$twextra_key = $data['twextra_key'];
			}
			//for api access
			if(isset($data['twitter_user_id'])){
				$twitter_user_id = $data['twitter_user_id'];
			}
			//for api access
			if(isset($data['twitter_access_token'])){
				$twitter_access_token = $data['twitter_access_token'];
			}
			//for api access
			if(isset($data['twitter_access_token_secret'])){
				$twitter_access_token_secret = $data['twitter_access_token_secret'];
			}
			
			$tweet = str_replace ( "<br>", "<br />", $tweet_raw );
			
			$tweet_size = strlen ( $tweet );
			$tweet = stripslashes ( $tweet );
		}
		
		if(isset($data['message_id_reply'])){
			$message_id_reply = $data['message_id_reply'];
		}else{
			$message_id_reply = '';
		}
		
		if(isset($data['message_id'])){
			$message_id = $data['message_id'];
		}else{
			$message_id = '';
		}
		
//print_r($data);
		
		/*
		//embed @replies for all cases
			$pattern_to_embed = 'at_reply';
			$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
			$tweet = $embed->embedPattern ();
			
		//embed links for all cases (Twitter, LinkedIn, Twextra)
			$pattern_to_embed = 'link';
			$embed = new EmbedInLink ( $tweet, $pattern_to_embed );
			$tweet = $embed->embedPattern ();
		*/
		
		//validate and update twextra key
		if($twextra_key != ''){
			$model = new TwextraModel();
			$success = $model->apiKeyMatchUpdate($twextra_key);
			if($success == 'error'){
				$err_message = array ('error' => 'Error: Your Twextra key did not match or the daily limit is exceeded.' );
				print_r ( json_encode ( $err_message ) );
				exit ();
			}
		}
		
		if ($tweet_size < $tweet_size_max) { 
            
            if ($message_id == '412641') {
            	//have one fixed message_id for admin work...
                $message_id_reply = $message_id;
            } else {
                //generate a random url
                $message_id = $this->random_message_id ();
            }
			
			//echo 'message_id:', $message_id; exit;
			$url = $hostname . "tweet_display.php?message_id={$message_id}&mthd=displayTweet";
			$url_rewrite = "$hostname/$message_id";
				//save in database
			logger ( $script_path."  before model save:" );
			
			if (isset ( $data ['social'] ) && ($data['social']=='twitter')) {
				if(isset($_COOKIE[$cookie_name])){
					$twitter_user_id_hash = $_COOKIE[$cookie_name];
				}else if($twitter_user_id != ''){
					logger($script_path."  twitter_user_id2: ", $twitter_user_id);
					$twitter_user_id_hash = sha1($twitter_user_id, false);
					logger($script_path." twitter_user_id_hash2: ", $twitter_user_id_hash);
				}
				logger($script_path."  twitter_user_id_hash", $twitter_user_id_hash);
				
				if ($twitter_user_id_hash != '' ) {
					$result_t = $this->getTwCredentials($twitter_user_id_hash);//
				logger($script_path."  result_t/tw credentials: ", $result_t);
					
					//$tid = $result_t ['tid'];
				} else {
					//$tid = null;
					$result_t = 'error';
				}
			} else {
				//$tid = null;
			}
			    $url_ln2 = $this->getPrefix($tweet, $message_id);//prefix
				$model = new TwextraModel ( );
				$success = $model->saveTweet ( $tweet, $message_id, $message_id_reply, $url_ln2 );
			
			logger ( $script_path."  after model save:" );
			//...........................................................................  
			//if twitter request then generate prefix and message and redirect to page 1 or 2
			if (isset ( $data ['social'] )) {

		    	logger ( $script_path."  final prefix:", $url_ln2 );
		    	
		    	$url_ln1 = "$hostname/linkedin/page1.php?prefix=";
				$url_tw1 = "$hostname/twitter/page1.php?prefix=";
				$url_ln3 = "&message=";
				$url_ln4 = rawurlencode ( $url_rewrite );
				
				$url_ln = $url_ln1 . $url_ln2 . $url_ln3 . $url_ln4;
				$url_tw = $url_tw1 . $url_ln2 . $url_ln3 . $url_ln4;
				
				$_SESSION ['prefix'] = $url_ln2;
				$_SESSION ['message'] = $url_ln4;
				$_SESSION ['message_id'] = $message_id;
					
				//logic: if cookie is set and a hit in the table, go to page 2; else go to page 1.
				//$_COOKIE['tw_tok']
				if ($twextra_key != '' && $twitter_access_token != '' && $twitter_access_token_secret != '') {
					//validate and update twextra key;
					$api_result = $this->apiUpdateJson ( $data );
					logger($script_path."  api_result: ", $api_result);
					
					if ($api_result != false) {
						//echo $api_result;
					}else{
						echo "Error: Please check your data and try again.";
					}
				}
				if (isset ( $data ['social'] ) && ($data['social']=='twitter')) {
					logger($script_path."  postTweet/test1:");
					if (isset ($twitter_user_id_hash)) {
						logger($script_path."  test2");
						if ($result_t != 'error') {
							//go to page 2
							tw_page2_cookie($url_ln2, $url_ln4, $twitter_access_token, $twitter_access_token_secret);
						} else {
							//go to page 1
							tw_page1($url_ln2, $url_ln4);
						}
					} else {
						//go to page 1
						tw_page1($url_ln2, $url_ln4);
					}
				}
				if (isset ( $data ['social'] ) && ($data['social']=='linkedin')) {
					//go to page 1
					logger ( $script_path."  Linkedin/Cookie not set2: " );
					logger ( $script_path."  Linkedin/url_ln2", $url_ln2 );
					logger ( $script_path."  Linkedin/url_ln4", $url_ln4 );
					ln_page1 ( $url_ln2, $url_ln4 );
				}
			
			}
			//............................................................................
			//finally if neither twitter or linkedin request redirect to view page
			logger ( $script_path."  RD url: ", $url_rewrite );
			header ( "Location:$url_rewrite" ); //
			exit ();
		} else {
			//require_once $docroot . "/tw.lib.php";
			$error_size = "Your message size exceeds the max size limit of $tweet_size_max (incl. formatting).<br> Your message size: $tweet_size. Please try again.";
			$_SESSION['error_size'] = $error_size;
			$_SESSION['tweet'] = $tweet;
			//index ( $tweet, $error_size );
			header ( "Location:$hostname" ); //
			exit();
			
		}
	}
//.........................................................................................................	
	function showTweet($data) {
		
		$script_path = __FUNCTION__;
		    	
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$debug = $config_params['debug']; 
		$enable_stats = $config_params ['enable_stats'];
		
	    //save logs
		if ($enable_stats) {
			$model = new TwextraModel ( );
			$model->saveStat ();
		}
		
		if (! empty ( $data )) {
			//get the url string
			$message_id = $data ['url'];
			
			logger($script_path . "  message_id: ", $message_id);
			        
			//get data from the database
			$model = new TwextraModel ( );
			$result = $model->readTweet ( $message_id, false );
			$tweet = $result ['tweet'];
			
			$url_ln2 = $this->getPrefix($tweet, $message_id);
			$_SESSION ['prefix'] = $url_ln2;
			$view_count = $result ['view_count'];
			$url = $hostname . "router.php?url={$message_id}&route=tweet_show";
			//display tweet and success message
			$this->updateTwitter ( $data );
			$view = new TwextraView ( );
			$view->displayTweet ( $tweet, $url, $view_count, $this->language_list );
		}
	}
//........................................................................................
    function getPrefix ($tweet, $message_id)

    {
    	$script_path = __FUNCTION__;
        
        $config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		//$tweet_size_max = $config_params['tweet_size_max'];
		$prefix_size_max = $config_params['prefix_size_max'];
		//$docroot = $config_params['docroot'];
		//$debug = $config_params['debug']; 
		//$cookie_name = $config_params['cookie_name']; 
		$url_rewrite = "$hostname/$message_id";
		

        //$tweet_plain = strip_tags ( htmlspecialchars_decode ( $tweet ) );
        $tweet_plain = strip_tags($tweet);

        logger($script_path . "  before entity decode: ", $tweet_plain);

        $tweet_plain = preg_replace('/&nbsp;/', ' ', $tweet_plain);

        $tweet_plain = html_entity_decode($tweet_plain, ENT_NOQUOTES, 'UTF-8'); //for fixing spanish characters..
        logger($script_path . "  after entity decode: ", $tweet_plain);

        $tweet_plain = trim($tweet_plain);

        $tweet_size_plain = strlen($tweet_plain);

        $url_ln1 = "$hostname/linkedin/page1.php?prefix=";

        $url_tw1 = "$hostname/twitter/page1.php?prefix=";

        $url_ln3 = "&message=";

        $url_ln4 = rawurlencode($url_rewrite);

        $prefix_size = $prefix_size_max - strlen($url_ln4); //this is the url part that is sent to linkedin/twitter
        if ($prefix_size < strlen($tweet_plain)) {

            $tweet_prefix = substr($tweet_plain, 0, $prefix_size - 1);

            //the prefix should not have a partial word at the end
            $tweet_array = explode(' ', $tweet_prefix);

            $tweet_count = count($tweet_array);

            if ($tweet_count > 1) {

                //drop the last word in the array to avoid partial words, if the word count is more than 1
                $tweet_array[$tweet_count - 1] = '';

            }

            $tweet_prefix = implode(' ', $tweet_array);

            $tweet_prefix = trim($tweet_prefix);

        } else {

            $tweet_prefix = $tweet_plain;

        }

        //strip out multiple white spaces from the prefix
        $tweet_prefix_cleaned = preg_replace("/&nbsp;/", " ", $tweet_prefix);

        $tweet_prefix_cleaned = trim($tweet_prefix_cleaned);

        $url_ln2_raw = $tweet_prefix_cleaned . "... ";

        //$url_ln2 = rawurlencode ( $url_ln2_raw );
        $url_ln2 = $url_ln2_raw;
        
        return $url_ln2;
    }
//.....................................................................................	
	function getTwCredentials($twitter_user_id_hash = '') {
		if (session_id () == "") session_start ();
		
		$script_path = __FUNCTION__;
		
		$_SESSION ['tw_tok'] = $twitter_user_id_hash;
			$model = new TwextraModel ( );
			$result_t = $model->get_tw_access_token ( $twitter_user_id_hash );
			logger ( $script_path . "  result_t: ", $result_t );
			logger ( $script_path . "  twitter_user_id_hash: ", $twitter_user_id_hash );
			logger ( $script_path . "  result_t array: ", $result_t );
			
			$_SESSION ['tw_access_token'] = $result_t;
			$_SESSION ['user'] = $result_t ['screen_name']; //
			$_SESSION ['tid'] = $result_t ['tid'];
			
			//check result_t, if access token is null or wrong, set result_t to error.			
			//if((trim($result_t['oauth_token'])=='') || ($result_t['oauth_token']==null)){
			if(empty($result_t) || empty($result_t['oauth_token'])){
				$result_t = 'error';
			}
		
		return $result_t;
	}
//.............................................................................................	
	function apiUpdateJson($data) {
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$tweet_size_max = $config_params['tweet_size_max'];
		
		$script_path = __FUNCTION__;
		
		$json_array = array ();
		$error = false;
		$twextra_key = $data ['twextra_key'];
		$twitter_access_token = $data ['twitter_access_token'];
		$json_array ['twextra_key'] = $twextra_key; //optional
		

		$tweet = $data ['editor'];
		$tweet_len = trim ( strlen ( $tweet ) );
		
		//generate an error if the message length exceeds max size, or is empty
		if ($tweet_len > $tweet_size_max) {
			//generate error message;
			$json_array ['error'] ['message_len'] = 'Message exceeds max size';
			$error = true;
		} else if ($tweet_len == 0) {
			//generate error message;
			$json_array ['error'] ['message_len'] = 'Message is empty';
			$error = true;
		} else {
			$json_array ['message_len'] = $tweet_len;
		}
		
		//authenticate based on the key and check daily limit
		if (! $error) {
			logger($script_path."  twextra_key: ", $twextra_key);
			$model = new TwextraModel ( );
			$key_match = $model->apiKeyMatchUpdate ( $twextra_key ); //
			

			if ($key_match == 'miss') {
				//
				$json_array ['error'] ['key_match_daily_limit'] = 'Error: Key did not match or the daily limit exceeded.';
				$error = true;
			}
		}
		
		//generate a random url
		if (! $error) {
			$message_id = $this->random_message_id ();
			$url = $hostname . "/tweet_display.php?message_id={$message_id}&mthd=displayTweet";
			
			$tweet = stripslashes ( $tweet );
			//save in database, and increment daily usage count
			$model = new TwextraModel ( );
			$saveTweet = $model->saveTweet ( $tweet, $message_id );
			//$incCount = $model->apiIncCount ( $key ); //
			if (! $saveTweet) {
				$json_array ['system_error'] = true;
				$json_array ['url'] = '';
				$json_array ['created'] = '';
			} else {
				$json_array ['url'] = $url;
				$json_array ['created'] = date ( "Y-m-d H:i:s" );
			}
		}
		
		$json_string = json_encode ( $json_array );
		
		$json_string_stripped = stripslashes ( $json_string );
		
		//echo stripslashes ( $json_string );
		
		if ($error == false) {
			return $json_string_stripped;
		} else {
			return $error;
		}
	}
//..........................................................................................	
	function translateTweet($data) {
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		$tweet_size_max = $config_params['tweet_size_max'];
		$docroot = $config_params['docroot'];
		$debug = $config_params['debug']; 
		
		$script_path = __FUNCTION__;
		
		//flow: get src, tgt languages and tweet; call google service, post results..
		
		//save logs
		if ($debug) {
			$model = new TwextraModel ( );
			$model->saveStat ();
		}
		
		logger ( $script_path."  translateTweet:" );
		$src_lang = $data ['src_lang'];
		$tgt_lang = $data ['tgt_lang'];
		//$src_tweet = $data['tweet'];
		$message_id = $data ['message_id'];
		$src_lang_value = $this->language_list [$src_lang];
		$tgt_lang_value = $this->language_list [$tgt_lang];
		$url = $hostname . "tweet_display.php?message_id={$message_id}&mthd=displayTranslatedTweet&src_lang={$src_lang}&tgt_lang={$tgt_lang}";
		$url .= "&src_lang_value={$src_lang_value}&tgt_lang_value={$tgt_lang_value}";
		$url_rewrite = "$hostname/$message_id/$src_lang-$tgt_lang";
		header ( "Location:$url_rewrite" );
		exit ();
		//header("Location:$url");

		//TODO: CHECK MAX SIZE, AND FREQUENCY OF TRANSLATIONS..
		if (trim ( $src_tweet ) != '') {
			$tgt_tweet = $this->translate ( $src_lang, $tgt_lang, $src_tweet );
			$tweet_object = json_decode ( $tgt_tweet );
			if (isset ( $tweet_object->responseData->translatedText )) {
				$tgt_tweet = $tweet_object->responseData->translatedText;
			} else {
				$tgt_tweet = '';
			}
			if (isset ( $data ['url'] )) {
				$url = $data ['url'];
			} else {
				$url = '';
			}
		} else {
			$tgt_tweet = '';
			$url = '';
		}
		
	//		$view = new TwextraView();
	//		$view->displayTranslatedTweet($src_lang_value, $tgt_lang_value, $src_tweet, $tgt_tweet, $url, $this->language_list);
	}
//...........................................................................	
	function updateTwitter($data) {
		
		$this->updateStatus ( 'raj4126', 'abc123', 'This is a test.' );
	}
//............................................................................	
	function random_message_id($length = 6) {
		// start with a blank string..
		// define possible characters
		//"0" is not an allowed digit, as a leading "0" could be misleading
		//total address space is approx 730M (30**6)
		//initially using only a subset of the address space (~120M) to allow for better database management
		$possible = "123456789bcdfghjklmnpqrstvwxyz";
		
		//$possible0 = "12345";//table "message"
		$possible0 = "a";//table "amessage"; allow "a" as a prefix, but not in the remaining 5 characters
		
		// set up a counter
		$i = 0;
		$j = 0;
		// add random characters to $password until $length is reached
		while ( $j < 100 ) {
			$rand_message_id = "";
			while ( $i < $length ) {
				// pick a random character from the possible ones
				if ($i == 0) {
					$char = substr ( $possible0, mt_rand ( 0, strlen ( $possible0 ) - 1 ), 1 );
				} else {
					$char = substr ( $possible, mt_rand ( 0, strlen ( $possible ) - 1 ), 1 );
				}
				$rand_message_id .= $char;
				$i ++;
			}
			$model = new TwextraModel ( );
			$result = $model->urlHit ( $rand_message_id );
			if ($result == 'miss') {
				break;
			}
			$j ++;
			$i = 0;
		}
		// done!
		
		return $rand_message_id;
	}
	//////////////////////////////////////////////////////////////////////////	
	function translate($src_lang, $tgt_lang, $tweet) {
		
		$prefix = 'q=';
		$query = urlencode ( $tweet );
		$query_string = $prefix . $query;
		$request = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&" . $query_string . "&langpair=" . $src_lang . "%7C" . $tgt_lang;
		
		//$tweet_translated = file_get_contents($request);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $request );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HTTPGET, true );
		$tweet_translated = curl_exec ( $ch );
		curl_close ( $ch );
		
		return $tweet_translated;
	}
	//////////////////////////////////////////////////////////////////////////
	function apiClient() {
		
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostname = $config_params['hostname'];
		
		//command line curl client:
		//curl http://192.168.1.103/router.php  -X POST -d route="api_update_json" -d editor="hello" -d key="1234" 
		//this function is not yet configured..use command line client above
		$data ['route'] = 'api_update_json';
		$data ['editor'] = "my tweet";
		
		$prefix = 'q=';
		$query = urlencode ( $tweet );
		$query_string = $prefix . $query;
		$request = "$hostname/twextra?v=1.0&" . $query_string . "&langpair=" . $src_lang . "%7C" . $tgt_lang;
		
		//$tweet_translated = file_get_contents($request);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $request );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HTTPGET, true );
		$tweet_translated = curl_exec ( $ch );
		curl_close ( $ch );
		
		return $tweet_translated;
	}
	///////////////////////////////////////////////////////////////////////////////  
	
	function updateStatus($user, $pswd, $status) {
		$postData = "status=" . urlencode ( $status ) . "&";
		$toSend = "POST /statuses/update.json HTTP/1.1\r\n";
		$toSend .= "Host: twitter.com\r\n";
		$toSend .= "Authorization: Basic " . base64_encode ( $user . ':' . $pswd ) . "\r\n";
		$toSend .= "Connection: close\r\n";
		$toSend .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$toSend .= "Content-Length: " . strlen ( $postData ) . "\r\n";
		$toSend .= "X-Twitter-Client: ElfTwitterLib\r\n";
		$toSend .= "X-Twitter-Client-Version: 0.1\r\n";
		$toSend .= "X-Twitter-Client-URL: http://www.viewista.com\r\n";
		$toSend .= "\r\n{$postData}\r\n\r\n";
		
		$hSocket = @socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
		if (! $hSocket) {
			$this->errm = "Twitter error: failed creating socket (" . socket_strerror ( socket_last_error () ) . ")\n";
			return 0;
		}
		if (! @socket_connect ( $hSocket, "twitter.com", 80 )) {
			$this->errm = "Twitter error: failed connecting to twitter.com (" . socket_strerror ( socket_last_error () ) . ")\n";
			return 0;
		}
		if (@socket_send ( $hSocket, $toSend, strlen ( $toSend ), 0 ) === false) {
			$this->errm = "Twitter error: sending data (" . socket_strerror ( socket_last_error () ) . ").\n";
			return 0;
		}
		$sReceived = '';
		while ( $buf = @socket_read ( $hSocket, 4096 ) ) {
			$sReceived .= $buf;
		}
		socket_close ( $hSocket );
		$this->rply = $sReceived;
		if (strpos ( $sReceived, 'HTTP/1.1 200 OK' ) !== FALSE)
			return 1;
		$this->errm = "Twitter error: failed to login to twitter.com.\n";
		return 0;
	}
//..........................................................	
	function getError() {
		return $this->errm;
	}
//................................................................
	function getReply() {
		return $this->rply;
	}
//.......................................................
function delete_message($data){
	//delete this message if the user_name is same as the message creator name, and redirect to message history page or page B
	$username = $_SESSION['user'];
	$message_id = $data['message_id'];
	$message_poster = $data['message_poster'];
	
	if($username == $message_poster){
		//delete message, and redirect to message history page
		$twextra_model = new TwextraModel();
		$success = $twextra_model->deleteTweetList(array($message_id));
		header("Location: " . $hostname . "/displayMessageHistory.php");
		exit;	
	}else{
			//error message, redirect to page B
		$error = "This message '$message_id' could not be deleted.";
		 $twextra_view = new TwextraView();
		 $twextra_view->displayTweet($message_id, $error);
	}
}
///////////////////////////////////////////////////////////////////////////	
}
?>