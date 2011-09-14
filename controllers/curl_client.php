<?php 

  $docroot=$_SERVER['DOCUMENT_ROOT'];
  require_once $docroot."/tw.lib.php";
//---------------------------------------------------------------------
validate_access_restricted();

//curl http://twextra.com/router.php  -X POST -d route="tweet_post" -d editor="hello from curl15" -d twextra_key="12341234" -d twitter_access_token="20897173-cPKcnmWm7SWtpnAarm3PQxODQLxpAymHp90DEKADU"  -d social="twitter"
$cmd = '
curl http://twextra.com/router.php  
-X POST 
-d route="tweet_post" 
-d editor1="hello from curl50" 
-d twextra_key="12341234" 
-d twitter_user_id="20897173"
-d twitter_access_token="20897173-cPKcnmWm7SWtpnAarm3PQxODQLxpAymHp90DEKADU"  //twextra
-d twitter_access_token_secret="yElKfUEWTZD0y6vlkSL8fm6C3vVSDs4lVv8sRBJaNw"   //twextra
-d twitter_access_token="176706752-YuS6MbyL0Id6kUuGkRTwMSE2ZdRjM7rft2vR29ba"  //twetest
-d twitter_access_token_secret="1BCi6YLsDIl6Xpyw7knfVVp3y3JFHD78u6TUhWBZqU"   //twetest
-d social="twitter"
';

$cmd =' 
curl http://twextra.com/router.php  -X POST -d route="tweet_post" -d editor1="hello from curl50" -d twextra_key="12341234" -d twitter_user_id="20897173" -d twitter_access_token="20897173-cPKcnmWm7SWtpnAarm3PQxODQLxpAymHp90DEKADU"  -d twitter_access_token_secret="yElKfUEWTZD0y6vlkSL8fm6C3vVSDs4lVv8sRBJaNw" -d social="twitter"
';
$cmd =' 
curl http://twetest.com/router.php  -X POST -d route="tweet_post" -d editor1="hello from curl50" -d twextra_key="12341234" -d twitter_user_id="20897173" -d twitter_access_token="176706752-YuS6MbyL0Id6kUuGkRTwMSE2ZdRjM7rft2vR29ba"  -d twitter_access_token_secret="1BCi6YLsDIl6Xpyw7knfVVp3y3JFHD78u6TUhWBZqU" -d social="twitter"
';



/*Posterous API
 * method upload:
 * http://posterous.com/api2/upload.format
 * formats:
 * json or xml
 * sample json response:
 * sample xml response:
 * sample curl command:
curl -v -H 'X-Auth-Service-Provider: https://api.twitter.com/1/account/verify_credentials.json'
-H 'X-Verify-Credentials-Authorization: 
OAuth oauth_nonce="gMfO3YKSNBt5w8s1gQ9TnUQ1Ji219gYFq5VovyC1Y0", 
oauth_callback="http%3A%2F%2Fposterous.com%2Foauth%2Fcallback", 
oauth_signature_method="HMAC-SHA1", 
oauth_timestamp="1273511181", 
oauth_consumer_key="STWUDG4OpJhxrEnLZ1uQ4g", 
oauth_token="21465735-lwKdG1iDzWhTFjiCdlddrmcslMNllHZUd5SPY1xMo", 
oauth_signature="1sYBtfLl%2EBLXkg655gYNLalXOP0%3D", 
oauth_version="1.0"' 
-F "key=2b38a676d9b58d1d44da0db832512a1f"
-F "media=@/path/to/some/image.jpg"
-F "message=message" http://posterous.com/api2/upload.xml
*/
?>