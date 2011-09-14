<?php

exit;//for testing only..

//$docroot = $_SERVER['DOCUMENT_ROOT'];
//require_once  "../../tw.lib.php";

//validate access;
validate_access_twetest();

$socket=new SocketServer(); 

class SocketServer{
    var $host;
    var $port;
    //-------------------------------------------
    function  SocketServer($host='127.0.0.1',$port=1234){
    	
        if(!preg_match("/^d{1,3}.d{1,3}.d{1,3}.d{1,3}$/", $host)){
            //trigger_error('Invalid IP address format.',E_USER_ERROR);
        }
        
        if(!is_int($port)||$port<1||$port>65535){
            trigger_error('Invalid TCP port number.',E_USER_ERROR);
        }
        
        $this->host=$host;
        $this->port=$port;
        $this->connect();
    }
    //---------------------------------------------
    function connect(){
        //set_time_limit(0);
        // create low level socket
        if(!$socket=socket_create(AF_INET,SOCK_STREAM,0)){
            trigger_error('Error creating new socket.',E_USER_ERROR);
        }
        // bind socket to TCP port
        if(!socket_bind($socket,$this->host,$this->port)){
            trigger_error('Error binding socket to TCP port.',E_USER_ERROR);
        }
        // begin listening connections
        if(!socket_listen($socket)){
            trigger_error('Error listening socket connections.',E_USER_ERROR);
        }
        // create communication socket
        if(!$comSocket=socket_accept($socket)){
            trigger_error('Error creating communication socket.',E_USER_ERROR);
        }
			// read socket input
		//while ( 1 ) {
			$socketInput = socket_read ( $comSocket, 1024 );
			// convert to uppercase socket input 
			$socketOutput = strtoupper ( trim ( $socketInput ) ) . "n";
			// write data back to socket server
			if (! socket_write ( $comSocket, $socketOutput, strlen ( $socketOutput ) )) {
				trigger_error ( 'Error writing socket output', E_USER_ERROR );
			}
		//}
        // close sockets
        socket_close($comSocket);
        socket_close($socket);
    }
}

?>