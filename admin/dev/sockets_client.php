<?php

$docroot = $_SERVER['DOCUMENT_ROOT'];

require_once $docroot . "/tw.lib.php";

//validate access;
validate_access_twetest();
//---------------------------------------------

// check if form was submitted
if($_POST['send']){
    // open client connection to TCP server
    if(!$fp=fsockopen('127.0.0.1',1234,$errstr,$errno,30)){
        trigger_error('Error opening socket',E_USER_ERROR);
    }
    $message=$_POST['message'];
    // write message to socket server
    fputs($fp,$message);
    // get server response
    $ret=fgets($fp,1024);
    // close socket connection
    fclose($fp);
    echo '<h1>You entered the following message in
lowercase :'.$ret.'</h1>';
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>TESTING TCP SOCKET SERVER</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-
8859-1" />
</head>
<body>
<h1>Enter your message here</h1>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<input type="text" name="message" size="30" /><br />
<input type="submit" name="send" value="Send Value" />
</form>
</body>
</html>