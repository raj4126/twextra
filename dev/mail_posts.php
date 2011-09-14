<?php 
//this script posts tweets to twitter sent through emails
//we use a Restful web service approach to post the tweet
//this is a long running script, that sleeps for 10 min after processing a list and then continues

while ( 1 ) {
	//open connection to the incoming mail server
	$imap = imap_open ( '{mail.godaddy.com:110/pop3}', 'contact@twextra.com', 'Spearmint1' );
	//get a list of email messages that need to be posted
	if ($imap) {
		//Check no.of.msgs
		$message_count = imap_num_msg ( $imap );
		echo "<br><br>message_count: ", $message_count;
		$message_number_get = 0;
		$tweet = array ();
		$screen_name = array ();
		$message_number_list = array ();
		
		//if there is a message in your inbox, get the screen name and tweet
		while ( $message_number_get <= $message_count ) {
			//read that mail recently arrived
			$tweet [] = imap_fetchbody ( $imap, $message_number_get, 1 );
			$header = imap_headerinfo ( $imap, $message_number_get );
			$screen_name [] = $header->subject;
			$message_number_list [] = $message_number_get;
			$message_number_get ++;
		}
			
		//post the tweet list and send email responses
		foreach ( $message_number_list as $message_number_post ) {
			tw_post ( $screen_name [$message_number_post], $tweet [$message_number_post] );
			imap_mail ( 'raj4126@yahoo.com', 'Your message has been posted to Twitter', 'Your message has been posted to Twitter.' );
		}

		//mark the posted emails for deletion
		foreach ( $message_number_list as $message_number_del ) {
			imap_delete ( $imap, $message_number_del );
		}
		//delete (expunge) all the marked messages
		imap_expunge ( $imap );
		
	}
	//close the stream
	imap_close ( $imap );
	//sleep for 10 min and continue;
	sleep ( 10 * 60 );
}
	
//...............................................................................
function tw_post($screen_name, $tweet) {

$docroot = $_SERVER['DOCUMENT_ROOT'];
require_once $docroot."/config.php";
require_once $docroot."/sql/database.php";

		
/*  //curl command:
curl http://twextra.com/router.php  -X POST -d route="tweet_post" -d editor="hello from curl50" -d twextra_key="12341234" 
-d twitter_access_token="20897173-cPKcnmWm7SWtpnAarm3PQxODQLxpAymHp90DEKADU"  -d 
twitter_access_token_secret="yElKfUEWTZD0y6vlkSL8fm6C3vVSDs4lVv8sRBJaNw" -d social="twitter"
*/
	
	$url = "http://twextra.com/router.php";
	$tweet_urlencoded = urlenclode($tweet);
	
	$twextra_key = '12341234';
	//get user's access_token, and access_token_secret from screen_name
	$model = new TwextraModel ( );
	$access_token_array = $model->get_tw_access_token_secret ( $screen_name );
	
	$access_token = $access_token_array ['oauth_token'];
	$access_token_secret = $access_token_array ['oauth_token_secret'];
	//$post_params = array ('route' => 'tweet_post', 'editor' => $tweet, 'twextra_key' => $twextra_key, 'twitter_access_token' => $access_token, ...
	$post_params = "route=tweet_post&editor={$tweet_urlencoded}&twextra_key={$twextra_key}&twitter_access_token={$access_token}&twitter_access_token_secret={$access_token_secret}&social=twitter";
//.................	
	    $options = array(
	    CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,         // return web page
        CURLOPT_HEADER         => false,        // don't return headers
        CURLOPT_FOLLOWLOCATION => true,         // follow redirects
        CURLOPT_ENCODING       => "",           // handle all encodings
        CURLOPT_USERAGENT      => "twextra_emailer",     // who am i
        CURLOPT_AUTOREFERER    => true,         // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
        CURLOPT_TIMEOUT        => 120,          // timeout on response
        CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
        CURLOPT_POST            => 1,            // i am sending post data
        CURLOPT_POSTFIELDS     => $post_params,    // this are my post vars
        CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
        CURLOPT_SSL_VERIFYPEER => false,        //
        CURLOPT_VERBOSE        => 1                //
    );

    $ch      = curl_init();
    curl_setopt_array($ch,$options);
    $response = curl_exec($ch);
    //$err     = curl_errno($ch);
    //$errmsg  = curl_error($ch) ;
    //$header  = curl_getinfo($ch);
    curl_close($ch); 
	return $response;
	
} 
     
?>