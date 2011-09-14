<?php

$docroot = $_SERVER['DOCUMENT_ROOT'];

require_once $docroot."/config.php";
require_once $docroot."/sql/database.php";
require_once $docroot."/controllers/twextra_controller.php";
if (session_id () == "") session_start ();

require_once $docroot . "/tw.lib.php";

//validate access;
validate_access_twetest();
//................................................................................	
class TwextraModel {
		//............................................................................
	function get_hostname() {
		$config_params = Config::getConfigParams ();
		$hostname = $config_params ['hostname'];
		return $hostname;
	}
	//....................................................
	function mc_pconnect() {
		static $memcache = null;
		
        if($this->get_hostname() == 'http://twetest.com'){
        	return null;
        }
		
		if (! $memcache) {
			$memcache = new Memcache ();
			//open a persistent connection
			if(!($memcache->pconnect ( 'localhost', 11211 ))){
				$memcache = null;
				//.................................
				//send system maintenance message
				$to = 'raj4126@yahoo.com';
				$subject = 'memcached not running';
				$message = 'start memcached daemon';
				$headers = 'From: contact@twextra.com' . "\r\n" .
    						'Reply-To: contact@twextra.com' . "\r\n" .
   							 'X-Mailer: PHP/' . phpversion();
				
				//mail($to, $subject, $message, $headers);
				//....................................
			}
		}
		
		return $memcache;
	}
	
	//.............................................................................
	function mc_set($key, $value) {
		
	    if($this->get_hostname() == 'http://twetest.com'){
        	return null;
        }
		
		//save for 1 hour (3600 sec)
		if($memcache = $this->mc_pconnect ()){
			$memcache->set ( $key, $value, false, 1*60*60 );
		}
	}
//..............................................................................	
	function mc_get($key) {
		
		
	    if($this->get_hostname() == 'http://twetest.com'){
        	return null;
        }
		
		if ($memcache = $this->mc_pconnect ()) {
			$value = $memcache->get ( $key );
		}else{
			$value = null;
		}
		
		return $value;
	}
//............................................................................
	function mc_flush() {
		
	    if($this->get_hostname() == 'http://twetest.com'){
        	return null;
        }
		
		if ($memcache = $this->mc_pconnect ()) {
			$memcache->flush ();
			//need to sleep for at least 1 sec after the flush (memcache docs)
			sleep ( 2 );
		}
	}
//..........................................................................
	function apc_get($key){
		$value = apc_fetch($key);
		$value = unserialize($value);
		return $value;
	}
//......................................................................
	function apc_set($key, $value) {
		$value = serialize($value);
		$success = apc_store ( $key, $value );
		return $success;
	}
//...................................................................
	function apc_flush_user() {
		$success = apc_clear_cache ( 'user' );
		return $success;
	}
//...................................................................
	function apc_flush_opcode() {
		$success = apc_clear_cache ();
		return $success;
	}
//...................................................................
	function cache_get($key) {
		$config_params = Config::getConfigParams ();
		$cache_type = $config_params ['cache_type'];
		
		if($cache_type == 'memcache'){
			$value = $this->mc_get($key);
		}else if($cache_type == 'apc'){
			$value = $this->apc_get($key);
		}else{
			$value = null;
		}
		
		$script_path = __FUNCTION__ ;
		logger ( $script_path . " cache_get:", $value );
		
		return $value;
	}
		//...................................................................
	function cache_set($key, $value) {
		$config_params = Config::getConfigParams ();
		$cache_type = $config_params ['cache_type'];
		
		$script_path = __FUNCTION__ ;
		logger ( $script_path . " cache_set:", $value );
		
		if ($cache_type == 'memcache') {
			$value = $this->mc_set ( $key, $value );
		} else if ($cache_type == 'apc') {
			$value = $this->apc_set ( $key, $value );
		} else {
			$value = null;
		}
		
		return $value;
	}
//............................................................................	
	function get_message_table($message_id){
		
		$first_message_table_prefixes = array(1,2,3,4,5);
		
		if(in_array($message_id[0], $first_message_table_prefixes)){
			$message = 'message';
		}else{
			$message = $message_id[0].'message';
		}
		
		return $message;
	}
//...........................................................................
    function is_valid_message_id($message_id){
		//check for valid message ids:
		$pattern = '/^[123456789abcdfghjklmnpqrstvwxyz]{6,6}$/';
		$match = preg_match($pattern, $message_id);
		return $match;
}
//................................................................................	
	function saveTweet($tweet, $message_id, $message_id_reply='', $prefix=''){
				
		$script_path = __FUNCTION__ ;
		//get data base connection;
		$success=true;
		
		//add a non-breaking space at the end of the tweet for detecting tokens while embedding
		//$tweet = $tweet.'&nbsp;';

                $date_time = date("F j, Y  g:i A");
                $remote_ip = $_SERVER['REMOTE_ADDR'];
		
		$link = DbConnect::getInstance();
	
                $command="SET NAMES 'utf8'";
                $stmt=mysqli_prepare($link, $command);
                mysqli_stmt_execute($stmt);
                
                $message = $this->get_message_table($message_id);
                
		//$command="insert into message (id, tweet, url, remote_ip, tid, created_date, message_id_reply, prefix) values('', ?, ?, ?, ?, now(), ?,?)";
		$command="replace into $message (tweet, url, remote_ip, created_date, message_id_reply, prefix) values(?, ?, ?, now(), ?,?)";

		if($stmt=mysqli_prepare($link, $command)){
			mysqli_stmt_bind_param($stmt, 'sssss', $tweet, $message_id, $remote_ip, $message_id_reply, $prefix);
			mysqli_stmt_execute($stmt);
			$count=mysqli_stmt_affected_rows($stmt);
			if($count==0){
				$success=false;
			}
		}else{
			$success=false;
		}

		mysqli_stmt_close($stmt);

		return $success;
	}
		//.............................................................................
	function deleteTweetList($message_list_delete) {
		
		$script_path = __FUNCTION__;
		
		$message_table_list = array('message', 'amessage');//add future message tables to this list
		$first_message_table_prefixes = array(1,2,3,4,5);//for table name: 'message'
			
		//iterate over each message table, and delete from the list
		foreach ( $message_table_list as $message ) {
			
			//check if the message_ids belong to this table, otherwise skip
			$match = false;
			foreach($message_list_delete as $id){
				$condition1 = ($message[0]=='m' && in_array($id[0], $first_message_table_prefixes));
				$condition2 = ($message[0] == $id[0]);
				if($condition1 || $condition2){
					$match = true;
					break;
				}
			}
			
			if($match==false){
				continue;//skip to the next table
			}
			
			//these message_ids belong to this table, so update the message table
			
			$success = true;
			
			//get data base connection;
			$link = DbConnect::getInstance ();
			
			//DELETE t1, t2 FROM t1 INNER JOIN t2 INNER JOIN t3
			//WHERE t1.id=t2.id AND t2.id=t3.id;

			//do not delete if the user is not logged in or does not own this message, or if the list count exceeds 20
			if ((! isset ( $_SESSION ['user'] )) || (empty ( $_SESSION ['user'] )) || (count ( $message_list_delete ) > 20)) {
				return;
			}
			
			$command = "update $message inner join tw_token on ($message.user_id=tw_token.user_id) 
						set deleted=1, tweet='Note: This message has been deleted 
						by the creator', prefix='deleted...' 
		            where screen_name=? and url in ( ";
			
			$message_id_clause = '';
			
			//check for valid message ids:
			$pattern = '/^[123456789abcdfghjklmnpqrstvwxyz]{6,6}$/';
			
			foreach ( $message_list_delete as $message_id ) {
				$message_id_clause .= "'$message_id',";
				$match = preg_match ( $pattern, $message_id );
				if (! $match) {
					return;
				}
			}
			
			$message_id_clause = substr ( $message_id_clause, 0, - 1 );
			$command .= $message_id_clause . ")";
			
			if ($stmt = mysqli_prepare ( $link, $command )) {
				mysqli_stmt_bind_param ( $stmt, "s", $_SESSION ['user'] );
				mysqli_stmt_execute ( $stmt );
				$count = mysqli_stmt_affected_rows ( $stmt );
				if ($count == 0) {
					$success = false;
				}
			} else {
				$success = false;
			}
			
			mysqli_stmt_close ( $stmt );
		}
		
		return $success;
	}

//................................................................................		
	function readTweet($message_id, $inc_view_count=false){
		
	$script_path = __FUNCTION__ ;
		
	logger ( $script_path . " message_id4:", $message_id );
	
	$result = $this->cache_get('read_tweet' . $message_id);
	//placeholder...
	$result = null;
		
		if (! $result) {
			
			logger ( $script_path . " fromdb1", $message_id );
			
			$link = DbConnect::getInstance ();
			
			$message = $this->get_message_table ( $message_id );
			
			$last_viewed_in = time ();
			logger ( $script_path . "  time2: ", $last_viewed_in );
			
			$command = "SET NAMES 'utf8'";
			$stmt = mysqli_prepare ( $link, $command );
			mysqli_stmt_execute ( $stmt );
			
			//		$command="select id, tweet, view_count, date_format(created,'%l:%i %p  %M %D, %Y') as created, last_viewed
			//                              from message where url = ?";
			

			$command = "select user_id, tweet, view_count, date_format(created,'%l:%i %p  %M %D, %Y') as created, last_viewed
                              from $message where url = ?";
			
			if ($stmt = mysqli_prepare ( $link, $command )) {
				mysqli_stmt_bind_param ( $stmt, 's', $message_id );
				mysqli_stmt_execute ( $stmt );
				mysqli_stmt_store_result ( $stmt );
				mysqli_stmt_bind_result ( $stmt, $user_id, $tweet, $view_count, $created, $last_viewed_out );
				mysqli_stmt_fetch ( $stmt );
				if (mysqli_stmt_num_rows ( $stmt ) == 0) {
					$tweet = - 1;
				}
			} else {
				$tweet = - 1;
			}
			
			mysqli_stmt_close ( $stmt );
			
			$result = array ('tweet' => $tweet, 'view_count' => ($view_count + 1), 'created' => $created, 'last_viewed' => $last_viewed_out );
			logger ( $script_path . " result:", $result );
			
			$this->cache_set ( 'read_tweet' . $message_id, $result );
		
		}else{
			logger ( $script_path . " frommc1", $message_id );
		}
		
		if ($inc_view_count) {	
			$this->incViewCount($message_id);
		}
		
		return $result;
	}
	
//............................................................................
	function incViewCount($message_id){
		
	$script_path = __FUNCTION__ ;
		
	$link = DbConnect::getInstance();
	
	$message = $this->get_message_table($message_id);
	
	$last_viewed_in = time();
		
			$command = "update $message 
			              set view_count = view_count+1, 
			                  last_viewed = ?
			              where url=?";
			
			if ($stmt = mysqli_prepare ( $link, $command )) {
				mysqli_stmt_bind_param ( $stmt, 'is', $last_viewed_in, $message_id );
				mysqli_stmt_execute ( $stmt );
			} 
	
		mysqli_stmt_close ( $stmt );
		
		return;
	}
		//................................................................................		
	function get_user_info($message_id) {
		
		$link = DbConnect::getInstance ();
		$script_path = __FUNCTION__;
		
		$user_info = $this->cache_get ( 'user_info' . $message_id );
		
		//just a placeholder
		//$user_info = null;
		
		if (empty ( $user_info )) {
			
			logger ( $script_path . " fromdb2 ", $message_id );
			$message = $this->get_message_table ( $message_id );
			
			$result = $this->is_valid_message_id ( $message_id );
			if (! $result) {
				return; //invalid message_id found
			}
			
			$command = "SET NAMES 'utf8'";
			$stmt = mysqli_prepare ( $link, $command );
			mysqli_stmt_execute ( $stmt );
			
			$command = "select screen_name, name, location, description, user_image_url, message_id_reply, tweet, 
		          date_format(m.created,'%l:%i %p  %M %D, %Y') as created, view_count,
		          last_viewed, m.created_date, prefix
                  from $message m left outer join tw_token t on (m.user_id = t.user_id)
                  where url = '$message_id'
                 ";
			
			if ($stmt = mysqli_prepare ( $link, $command )) {
				//mysqli_stmt_bind_param($stmt, 's', $message_id);
				mysqli_stmt_execute ( $stmt );
				mysqli_stmt_store_result ( $stmt );
				mysqli_stmt_bind_result ( $stmt, $screen_name, $name, $location, $description, $user_image_url, $message_id_reply, $tweet, $created, $view_count, $last_viewed, $created_date, $prefix );
				mysqli_stmt_fetch ( $stmt );
			}
			
			mysqli_stmt_close ( $stmt );
			
			logger ( $script_path . " message id1:", $message_id );
			logger ( $script_path . " screen name:", $screen_name );
			logger ( $script_path . " name:", $name );
			logger ( $script_path . " location:", $location );
			logger ( $script_path . " description:", $description );
			logger ( $script_path . " user_image_url:", $user_image_url );
			
			$user_info = array ('screen_name' => $screen_name, 'name' => $name, 'location' => $location, 
			'description' => $description, 'user_image_url' => $user_image_url, 'message_id_reply' => $message_id_reply, 
			'tweet' => $tweet, 'created' => $created, 'view_count' => $view_count, 'last_viewed' => $last_viewed, 
			'created_date' => $created_date, 'prefix' => $prefix );
			
			$this->cache_set ( 'user_info' . $message_id, $user_info );
		}else{
			logger ( $script_path . " frommc2 ", $message_id );
			$user_info['view_count']= $user_info['view_count'] + 1;
			
			$last_viewed = $user_info['last_viewed'];
			$user_info['last_viewed'] = time();
			$this->cache_set ( 'user_info' . $message_id, $user_info );
			$user_info['last_viewed'] = $last_viewed;
		}
		
		return $user_info;
	}
//...................................................................................
function get_message_history($screen_name='junk', $from = 0, $next = 'more', $order, $asc_desc, $length=20, $search=''){
	
	$link = DbConnect::getInstance();
	
	$script_path = __FUNCTION__ ;
	
	if($order == 'last_viewed'){
		$order_by = " order by top2.last_viewed $asc_desc ";
	}else if($order == 'views'){
		$order_by = " order by top2.view_count $asc_desc ";
	}else if($order == 'prefix'){
		$order_by = " order by top2.prefix $asc_desc ";
	}else{
		$order_by = " order by top2.created $asc_desc ";
	}
		
       $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
       
       if($next == 'less'){
       	  $from = $from - 20;
       }
       
       if($from  < 0){
       	$from = 0;
       }
			//command1 gets total message count for this screen_name from all message tables
		//command2 gets a slice of messages from all message tables specified by $from and $length
		if ($search == '') {
			
			$command0 = "select count(m.url)
       from message m join tw_token on (m.user_id=tw_token.user_id) 
       where tw_token.screen_name=? and m.deleted=0";
			
			$commanda = "select count(am.url)
       from amessage am join tw_token on (am.user_id=tw_token.user_id) 
       where tw_token.screen_name=? and am.deleted=0";
			
			$command1 = " (select (($command0) + ($commanda))) as msg_count_total ";
			
			$command2 = "select url, created_date, created, last_viewed, view_count, prefix, $command1
		           from (
		                 (select m.url, m.created_date, m.created, m.last_viewed, m.view_count, m.prefix
                          from message m inner join tw_token t on (m.user_id = t.user_id)
                          where t.screen_name = ? and m.deleted=0) 
                          union
                         (select am.url, am.created_date, am.created, am.last_viewed, am.view_count, am.prefix
                         from amessage am inner join tw_token ta on (am.user_id = ta.user_id)
                         where ta.screen_name = ? and am.deleted=0)
                   )as top2
				   $order_by  limit $from, $length 
                 ";
		} else {
			
			$searchl= strtolower($search);
			$searchu= strtoupper($search);
			$searchc= ucwords($searchl);
			$searchf= ucfirst($searchl);
			
			$search_cond0 = "( m.tweet like '%$search%' or m.tweet like '%$searchl%' or m.tweet like '%$searchu%' or 
			                   m.tweet like '%$searchc%' or m.tweet like '%$searchf%' )";
			$search_conda = "( am.tweet like '%$search%' or am.tweet like '%$searchl%' or am.tweet like '%$searchu%' or 
			                   am.tweet like '%$searchc%' or am.tweet like '%$searchf%' )";
			
			
			$command0 = "select count(m.url)
       from message m join tw_token on (m.user_id=tw_token.user_id) 
       where tw_token.screen_name=? and m.deleted=0 and $search_cond0 ";
			
			$commanda = "select count(am.url)
       from amessage am join tw_token on (am.user_id=tw_token.user_id) 
       where tw_token.screen_name=? and am.deleted=0 and $search_conda ";
			
			$command1 = " (select (($command0) + ($commanda))) as msg_count_total ";
			
			$command2 = "select url, created_date, created, last_viewed, view_count, prefix, $command1
		           from (
		                 (select m.url, m.created_date, m.created, m.last_viewed, m.view_count, m.prefix
                          from message m inner join tw_token t on (m.user_id = t.user_id)
                          where t.screen_name = ? and m.deleted=0 and $search_cond0 ) 
                          union
                         (select am.url, am.created_date, am.created, am.last_viewed, am.view_count, am.prefix
                         from amessage am inner join tw_token ta on (am.user_id = ta.user_id)
                         where ta.screen_name = ? and am.deleted=0 and $search_conda )
                   )as top2
				   $order_by  limit $from, $length 
                 ";
		}
		//check for sql injection attacks...
		$drop = stripos($command2, 'drop table');
		$insert = stripos($command2, 'insert into');
		$delete = stripos($command2, 'delete from');
		$replace = stripos($command2, 'replace into');
		$alter = stripos($command2, 'alter table');
        
        if (($drop === false) && ($insert === false) && ($delete === false) && ($replace === false) && ($alter === false)) {
            if ($stmt = mysqli_prepare ( $link, $command2 )) {
                mysqli_stmt_bind_param ( $stmt, 'ssss', $screen_name, $screen_name, $screen_name, $screen_name );
                mysqli_stmt_execute ( $stmt );
                mysqli_stmt_store_result ( $stmt );
                
                mysqli_stmt_bind_result ( $stmt, $message_id, $created_date, $created, $last_viewed, $view_count, $prefix, $msg_count_total );
                
                while ( mysqli_stmt_fetch ( $stmt ) ) {
                    $message_history [] = array ('message_id' => $message_id, 'created_date' => $created_date, 'last_viewed' => $last_viewed, 'view_count' => $view_count, 'prefix' => $prefix, 'msg_cnt' => $msg_count_total );
                }
                mysqli_stmt_close ( $stmt );
            }
        } else {
            $message_history = array ();
        }
		
		return $message_history;
	}
//...................................................................................
function get_message_totals(){
	
	$link = DbConnect::getInstance();
	
	$script_path = __FUNCTION__ ;
	

			//command1 gets total message count for this screen_name from all message tables
		//command2 gets a slice of messages from all message tables specified by $from and $length
			
			$command0 = "select count(m.url)   from message m";
			
			$commanda = "select count(am.url)  from amessage am";
			
			$command1 = " select (($command0) + ($commanda)) as message_totals ";
        
            if ($stmt = mysqli_prepare ( $link, $command1 )) {
                //mysqli_stmt_bind_param ( $stmt, 'ssss', $screen_name, $screen_name, $screen_name, $screen_name );
                mysqli_stmt_execute ( $stmt );
                //mysqli_stmt_store_result ( $stmt );
                
                mysqli_stmt_bind_result ( $stmt, $message_totals );
                
                 mysqli_stmt_fetch ( $stmt ) ;
            }else{
            	$message_totals = 0;
            }
		
        mysqli_stmt_close ( $stmt );
        
		return $message_totals;
	}
//---------------------------------------------------------------------------------
function get_twextra_search($screen_name='junk', $from = 0, $next = 'more', $order, $asc_desc, $length=20, $search=''){
    
    //if search string is empty, return an empty array
    if(empty($search)){
        $message_history [] = array ('message_id' => '', 'created_date' => '', 
        'last_viewed' => '', 'view_count' => '', 'prefix' => '', 
        'msg_cnt' => 0 );
        return $message_history;
    }
    
    
    $link = DbConnect::getInstance(); 
    
    $script_path = __FUNCTION__ ;
    
    if($order == 'last_viewed'){
        $order_by = " order by top2.last_viewed $asc_desc ";
    }else if($order == 'views'){
        $order_by = " order by top2.view_count $asc_desc ";
    }else if($order == 'prefix'){
        $order_by = " order by top2.prefix $asc_desc ";
    }else{
        $order_by = " order by top2.created $asc_desc ";
    }
        
       $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
       
       if($next == 'less'){
          $from = $from - 20;
       }
       
       if($from  < 0){
        $from = 0;
       }
            //command1 gets total message count for this screen_name from all message tables
        //command2 gets a slice of messages from all message tables specified by $from and $length
        if ($search == '') {
            
            $command0 = "select count(m.url)
       from message m join tw_token on (m.user_id=tw_token.user_id) 
       where m.deleted=0";
            
            $commanda = "select count(am.url)
       from amessage am join tw_token on (am.user_id=tw_token.user_id) 
       where am.deleted=0";
            
            $command1 = " (select (($command0) + ($commanda))) as msg_count_total ";
            
            $command2 = "select url, created_date, created, last_viewed, view_count, prefix, $command1
                   from (
                         (select m.url, m.created_date, m.created, m.last_viewed, m.view_count, m.prefix
                          from message m inner join tw_token t on (m.user_id = t.user_id)
                          where m.deleted=0) 
                          union
                         (select am.url, am.created_date, am.created, am.last_viewed, am.view_count, am.prefix
                         from amessage am inner join tw_token ta on (am.user_id = ta.user_id)
                         where am.deleted=0)
                   )as top2
                   $order_by  limit $from, $length 
                 ";
        } else {
        
            $search = preg_replace("/'/", "\'", $search);
            
            $searchl= strtolower($search);
            $searchu= strtoupper($search);
            $searchc= ucwords($searchl);
            $searchf= ucfirst($searchl);
            
//            $search_cond0 = "( m.tweet like '%$search%' or m.tweet like '%$searchl%' or m.tweet like '%$searchu%' or 
//                               m.tweet like '%$searchc%' or m.tweet like '%$searchf%' )";
//            $search_conda = "( am.tweet like '%$search%' or am.tweet like '%$searchl%' or am.tweet like '%$searchu%' or 
//                               am.tweet like '%$searchc%' or am.tweet like '%$searchf%' )";
            
            $search_cond0 = "( match(m.tweet) against('$search $searchl $searchu $searchc $searchf' in boolean mode)  )";
            $search_conda = "( match(am.tweet) against('$search $searchl $searchu $searchc $searchf' in boolean mode) )";
            
            
            $command0 = "select count(m.url)
       from message m join tw_token on (m.user_id=tw_token.user_id) 
       where $search_cond0 and m.deleted=0";
            
            $commanda = "select count(am.url)
       from amessage am join tw_token on (am.user_id=tw_token.user_id) 
       where $search_conda and am.deleted=0";
            
            $command1 = " (select (($command0) + ($commanda))) as msg_count_total ";
            
            $command2 = "select url, created_date, created, last_viewed, view_count, prefix, $command1
                   from (
                         (select m.url, m.created_date, m.created, m.last_viewed, m.view_count, m.prefix
                          from message m inner join tw_token t on (m.user_id = t.user_id)
                          where $search_cond0 and m.deleted=0) 
                          union
                         (select am.url, am.created_date, am.created, am.last_viewed, am.view_count, am.prefix
                         from amessage am inner join tw_token ta on (am.user_id = ta.user_id)
                         where $search_conda and am.deleted=0)
                   )as top2
                   $order_by  limit $from, $length 
                 ";
        }
        //check for sql injection attacks...
        $drop = stripos($command2, 'drop table');
        $insert = stripos($command2, 'insert into');
        $delete = stripos($command2, 'delete from');
        $replace = stripos($command2, 'replace into');
        $alter = stripos($command2, 'alter table');
        
        if (($drop === false) && ($insert === false) && ($delete === false) && ($replace === false) && ($alter === false)) {
            if ($stmt = mysqli_prepare ( $link, $command2 )) {
                //mysqli_stmt_bind_param ( $stmt, 'ssss', $screen_name, $screen_name, $screen_name, $screen_name );
                mysqli_stmt_execute ( $stmt );
                mysqli_stmt_store_result ( $stmt );
                
                mysqli_stmt_bind_result ( $stmt, $message_id, $created_date, $created, $last_viewed, $view_count, $prefix, $msg_count_total );
                
                while ( mysqli_stmt_fetch ( $stmt ) ) {
                    $message_history [] = array ('message_id' => $message_id, 'created_date' => $created_date, 'last_viewed' => $last_viewed, 'view_count' => $view_count, 'prefix' => $prefix, 'msg_cnt' => $msg_count_total );
                }
                mysqli_stmt_close ( $stmt );
            }
        } else {
            $message_history = array ();
        }
        
        return $message_history;
    }
//...................................................................................
//this function was used once to populate prefixes, now it is not needed and is deprecated.
function set_prefixes() {//deprecated
		
		$link = DbConnect::getInstance ();
		
		$script_path = __FUNCTION__;
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );
		
		//select a subset for performance reasons and invoke multiple times
		//$command = "select url, tweet from message where prefix is null";
		  $command = "select url, tweet from message where prefix is null limit 0,500";
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_execute ( $stmt );
			mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result ( $stmt, $message_id, $tweet );
			while ( mysqli_stmt_fetch ( $stmt ) ) {
				$tweet_message_id [] = array ('tweet'=>$tweet, 'message_id'=>$message_id );
			}
		}
		
		foreach ( $tweet_message_id as $entry ) {
			$tweet = $entry['tweet'];
			$message_id = $entry['message_id'];
			$controller = new TwextraController ();
			$prefix = $controller->getPrefix ( $tweet, $message_id ); //prefix
			//now write back the prefixes..
			$command = "update message set prefix=? where url=?";
			if ($stmt = mysqli_prepare ( $link, $command )) {
				mysqli_stmt_bind_param ( $stmt, 'ss', $prefix, $message_id );
				mysqli_stmt_execute ( $stmt );
				mysqli_stmt_store_result ( $stmt );
				//mysqli_stmt_bind_result ( $stmt, $message_id, $tweet );
			}
		}
		
		mysqli_stmt_close ( $stmt );
		
		return;
	
	}
//................................................................................		
	function urlHit($message_id){
		
		$script_path = __FUNCTION__ ;
		
		$link = DbConnect::getInstance();
		
		$message = $this->get_message_table($message_id);
		
       $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
       
		//$command="select id, tweet, view_count from message where url = ?";
		$command="select tweet, view_count from $message where url = ?";

		if($stmt=mysqli_prepare($link, $command)){
			mysqli_stmt_bind_param($stmt, 's', $message_id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $tweet, $view_count);
			mysqli_stmt_fetch($stmt);
			if(mysqli_stmt_num_rows($stmt) == 0){
				$result = 'miss';
			}else{
				$result = 'hit';
			}
		}else{
			$result='error';
		}

		mysqli_stmt_close($stmt);
		return $result;
	}
//................................................................................		
	function apiKeyMatchUpdate($key){
		$script_path = __FUNCTION__ ;
		$link = DbConnect::getInstance();
		$result = 'hit';
		
		$command = 'select count(*) from api_auth where api_key = ?';
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 's', $key );
			mysqli_stmt_execute ( $stmt );
			//mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result($stmt, $count );
			mysqli_stmt_fetch($stmt);
			logger ( $script_path . " count:", $count );
			if($count != 1){
				$result =  'error';
			}
	
		} else {
			$result = 'error';
		}
		
		logger ( $script_path . " result2:", $result );
		
		if($result == 'error'){
			return $result;
		}
		
		mysqli_stmt_close($stmt);
		//......................................
		$link = DbConnect::getInstance();
		
		$todays_date = date("Y-m-d");
		$command = 'select updated, daily_count, daily_max from api_auth_daily_count where api_key = ? and updated=?';
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'ss', $key, $todays_date );
			mysqli_stmt_execute ( $stmt );
			mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result($stmt, $updated, $daily_count, $daily_max );
			mysqli_stmt_fetch($stmt);
			logger ( $script_path . " todays-date:", $todays_date );
			logger ( $script_path . " updated:", $updated );
			if($updated == null){
				$command="insert into api_auth_daily_count (api_key, daily_count, daily_max, updated)values(?, 1, 1000, now())";
			}else if($daily_count < $daily_max){
				$command="update api_auth_daily_count set daily_count = daily_count + 1 where api_key = ? and updated='$todays_date'";
			}else{
				$result = 'error';//daily limit exceeded
			}
			logger ( $script_path . " result3:", $result );
	
		} else {
			$result = 'error';
			logger ( $script_path . " result4:", $result );
		}
		if ($result != 'error') {
			if ($stmt = mysqli_prepare ( $link, $command )) {
				mysqli_stmt_bind_param ( $stmt, 's', $key );
				mysqli_stmt_execute ( $stmt );
				//mysqli_stmt_store_result ( $stmt );
				//mysqli_stmt_bind_result($stmt, $api_key, $daily_count, );
				//mysqli_stmt_fetch($stmt);
				if (mysqli_stmt_affected_rows ( $stmt ) == 0) {
					$result = 'error';
			logger ( $script_path . " result5:", $result );
				} else {
					$result = 'hit';
			logger ( $script_path . " result6:", $result );
				}
			} else {
				$result = 'error';
			logger ( $script_path . " result7:", $result );
			}
		}

		mysqli_stmt_close($stmt);
		return $result;
	}
//................................................................................		
	function get_screen_name($user_id_hash){

		$link = DbConnect::getInstance();
		
	   $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
 
		$command = "SELECT screen_name 
		            FROM tw_token 
		            WHERE user_id_hash=?";

		if($stmt=mysqli_prepare($link, $command)){
			mysqli_stmt_bind_param($stmt, 's', $user_id_hash);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $screen_name);
			mysqli_stmt_fetch($stmt);
		}else{
			$screen_name='';
		}

		mysqli_stmt_close($stmt);

                return $screen_name;	
	}
//................................................................................		
	function get_tw_access_token($tw_user_id_hash){

		$link = DbConnect::getInstance();
                $error = '';
		
//		$access_token = Array ( 'oauth_token' => '20897173-zbS904r4c2FZZvqKFpk2EuIzlcghC1SgGc0ZJfLL7', 
//                        'oauth_token_secret' => 'RPLf7rSCr2JLv31NUw2DND3NFCcmGOTKqK3pi38Ew',
//                        'user_id' => '20897173',
//                        'screen_name' => 'raj4126' );

       $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
 
		$command = "SELECT oauth_token, oauth_token_secret, user_id, screen_name
		            FROM tw_token 
		            WHERE user_id_hash=?";

		if($stmt=mysqli_prepare($link, $command)){
			mysqli_stmt_bind_param($stmt, 's', $tw_user_id_hash);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $oauth_token, $oauth_token_secret, $user_id, $screen_name);
			mysqli_stmt_fetch($stmt);
			if(mysqli_stmt_affected_rows($stmt) == 0){
				$error = 'error';
			}
		}else{
			$error='error';
		}

		mysqli_stmt_close($stmt);
                
                $access_token = array('oauth_token'=>$oauth_token, 'oauth_token_secret'=>$oauth_token_secret,
                                     'user_id'=>$user_id, 'screen_name'=>$screen_name);

                $result = ($error == 'error') ?  'error' : $access_token;

                return $result;	
	}
//................................................................................		
	function set_tw_access_token($access_token) {
		
		$script_path = __FUNCTION__ ;
		
		//		$access_token = Array ( 'oauth_token' => '20897173-zbS904r4c2FZZvqKFpk2EuIzlcghC1SgGc0ZJfLL7', 
		//                        'oauth_token_secret' => 'RPLf7rSCr2JLv31NUw2DND3NFCcmGOTKqK3pi38Ew',
		//                        'user_id' => '20897173',
		//                        'screen_name' => 'raj4126' ); 
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$cookie_name = $config_params['cookie_name'];

		$link = DbConnect::getInstance ();
		
	    $command="SET NAMES 'utf8'";
        $stmt=mysqli_prepare($link, $command);
        mysqli_stmt_execute($stmt);
		
		$error = 'no error';
		$user_id = $access_token ['user_id'];
		$oauth_token = $access_token ['oauth_token'];
		$oauth_token_secret = $access_token ['oauth_token_secret'];
		$screen_name = $access_token ['screen_name'];
		$oauth_verifier = "-";
		
		$user_id_hash = sha1 ( $user_id, false );
		
		 logger($script_path."  user_id:user_id_hash:oauth_token:oauth_token_secret:screen_name:: ", 
		     $user_id.":".$user_id_hash.":".$oauth_token.":".$oauth_token_secret.":".$screen_name);

		//use REPLACE INSTEAD OF INSERT TO DELETE OLD MATCHING ENTRIES(ON UNIQUE KEYS) BEFORE AN INSERT
		//correct logic: update if screen name already exists, insert otherwise..
//original code...............................................	
		$command = "REPLACE INTO tw_token
                 ( user_id_hash, oauth_token, oauth_token_secret, oauth_verifier, user_id, screen_name, 
                  created )
                   VALUES (?, ?, ?, ?, ?, ?, now())";
		
		 logger($script_path."  sql: ", $command);
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'ssssss', $user_id_hash, $oauth_token, $oauth_token_secret, 
			$oauth_verifier, $user_id, $screen_name );
			
			    $result =   mysqli_stmt_execute ( $stmt );
			if ($result == true) {
				logger ( $script_path . "  success "  );
			} else {
				logger ( $script_path . "  failure: " );
			}
		} else {
			$error = 'error';
		}
//........................................................................
		logger($script_path."  error: ", $error);
		
		mysqli_stmt_close ( $stmt );
		
		setcookie ( $cookie_name, $user_id_hash, time () + 3600 * 24 * 14, "/");//set expiry time of 2 weeks
		
		return $error;
	}
//........................................................................
	function update_message_user_id($message_id, $user_id) {
		$link = DbConnect::getInstance ();
		$error = '';
		$script_path = __FUNCTION__ ;
		
		$message = $this->get_message_table($message_id);
		
		$command = "UPDATE $message SET user_id = ? WHERE url = ? ";
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'is', $user_id, $message_id );
			mysqli_stmt_execute ( $stmt );
		} else {
			$error = 'error';
		}
		mysqli_stmt_close ( $stmt );
		
		logger($script_path."  error: ", $error);
		
		return $error;
	
	}
//................................under development................................................	
	function update_user_info($user_id, $status) {
		
		$script_path = __FUNCTION__ ;
		
		$link = DbConnect::getInstance ();
		$error = '';
		
	    $command="SET NAMES 'utf8'";
        $stmt=mysqli_prepare($link, $command);
        mysqli_stmt_execute($stmt);
		
		$name = $status->user->name;//max allowed 20 chars (25 in twextra)
		$name = substr($name, 0, 25);
		$screen_name = $status->user->screen_name;//max allowed 15 chars (50 in twextra)
		$screen_name = substr($screen_name, 0, 50);
		$location = $status->user->location;//max allowed 30 chars (40 in twextra)
		$location = substr($location, 0, 40);
		$description = $status->user->description;//max allowed 160 chars (200 in twextra)
		$description = substr($description, 0, 200);
		$user_image_url = $status->user->profile_image_url;//max allowed ?? (255 in twextra)
		$user_image_url = substr($user_image_url, 0, 255);
		
		$user_info = array($name, $screen_name, $location, $description, $user_image_url, $user_id);
		
		logger($script_path."  user_info4: ", $user_info);
		
		$command = "UPDATE tw_token SET name = ?, screen_name=?, location=?, description=?, user_image_url=? WHERE user_id = ? ";
		
		logger($script_path."  sql: ", $command);
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'sssssi', $name, $screen_name, $location, $description, $user_image_url, $user_id );
			mysqli_stmt_execute ( $stmt );
		} else {
			$error = 'error';
		}
		mysqli_stmt_close ( $stmt );
		
		return $error;
	
	}
//................................................................................		
	function get_tw_access_token_secret($screen_name) {
		
		$link = DbConnect::getInstance ();
		$error = '';
		
	   $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
		
		$command = "select oauth_token, oauth_token_secret, user_id, screen_name
			from tw_token 
			where screen_name = ?";
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 's', $screen_name );
			mysqli_stmt_execute ( $stmt );
			mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result ( $stmt, $oauth_token, $oauth_token_secret, $user_id, $screen_name );
			mysqli_stmt_fetch ( $stmt );
			if (mysqli_stmt_affected_rows ( $stmt ) == 0) {
				$error = 'error';
			}
		} else {
			$error = 'error';
		}
		
		mysqli_stmt_close ( $stmt );
		
		$access_token_array = array ('oauth_token' => $oauth_token, 'oauth_token_secret' => $oauth_token_secret, 'user_id' => $user_id, 'screen_name' => $screen_name );
		
		$result = ($error == 'error') ? 'error' : $access_token_array;
		
		return $result;
	}
		//................................................................................
	function get_twextra_key($email, $firstname, $lastname, $application, $company, $web_site, $screen_name, $length = 9) {
		//check if already given a key, otherwise store user info, and grant a new key
		$link = DbConnect::getInstance ();
		$command = "select api_key from api_auth where email=? and application=? and company=? and web_site=? ";
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'ssss', $email, $application, $company, $web_site );
			mysqli_stmt_execute ( $stmt );
			mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result ( $stmt, $api_key );
			mysqli_stmt_fetch ( $stmt );
		} else {
			$result = 'error';
		}
		
		mysqli_stmt_close ( $stmt );
		
		
		if(!empty($api_key)){
			return $api_key;
		}
		
		// start with a blank string..
		// define possible characters
		//"0" is not an allowed digit, as a leading "0" could be misleading
		//total address space is approx 730M (30**6)
		//initially using only a subset of the address space (~120M) to allow for better database management
		$possible = "1234567890";
		
		$possible0 = "123456789";//table "message"
		//$possible0 = "a";//table "amessage"; allow "a" as a prefix, but not in the remaining 5 characters
		
		// set up a counter
		$i = 0;
		$j = 0;
		// add random characters to $password until $length is reached
		while ( $j < 100 ) {
			$rand_twextra_key  = "";
			while ( $i < $length ) {
				// pick a random character from the possible ones
				if ($i == 0) {
					$char = substr ( $possible0, mt_rand ( 0, strlen ( $possible0 ) - 1 ), 1 );
				} else {
					$char = substr ( $possible, mt_rand ( 0, strlen ( $possible ) - 1 ), 1 );
				}
				$rand_twextra_key  .= $char;
				$i ++;
			}
			$result = $this->api_auth_hit ( $rand_twextra_key );
			if ($result == 'miss') {
				break;
			}
			$j ++;
			$i = 0;
		}
		
		return $rand_twextra_key;
	}
//.............................................................................
function update_api_tables($email, $firstname, $lastname, $application, $company, $web_site, $screen_name, $rand_twextra_key){
	
        //start a transaction..
		$link = DbConnect::getInstance ();
		$command = "begin";
		$stmt=mysqli_prepare($link, $command);
		mysqli_stmt_execute($stmt);
		
		$api_key_hash = sha1($rand_twextra_key);
        $phone='unknown';
        
       $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
		
		$command = "select user_id from tw_token where screen_name='$screen_name' limit 1";
		if ($stmt = mysqli_prepare ( $link, $command )) {
			//mysqli_stmt_bind_param ( $stmt, 'i', $rand_twextra_key );
			mysqli_stmt_execute ( $stmt );
			mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result ( $stmt, $user_id );
			mysqli_stmt_fetch ( $stmt );
		} else {
			$result = 'error';
		}
		//mysqli_stmt_close ( $stmt );
		
		$success= true;
		$rand_twextra_key = intval($rand_twextra_key);
		$user_id = intval($user_id);
		
		$command = "insert into api_auth
		(email, first_name, last_name, user_id, phone, application, company, web_site, api_key, api_key_hash, created) 
		values(?,?,?,?,?,?,?,?,?,?,now()) ";
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'sssissssis', $email, $firstname, $lastname, $user_id, $phone, $application, $company, $web_site, $rand_twextra_key, $api_key_hash );
			mysqli_stmt_execute ( $stmt );
		} else {
			$success = false;
		}
		
		//mysqli_stmt_close ( $stmt );
		
		//now update api_auth_daily_count table
		$daily_count = 0;
		$daily_max = 5000;
		//$link = DbConnect::getInstance ();
		$command = "insert into api_auth_daily_count
		(api_key, daily_count, daily_max, updated)
		values(?,?,?,now()) ";
		if ($stmt = mysqli_prepare ( $link, $command )) {
			mysqli_stmt_bind_param ( $stmt, 'iii', $rand_twextra_key, $daily_count, $daily_max);
			mysqli_stmt_execute ( $stmt );
		} else {
			$success = false;
		}
		
		//$link = DbConnect::getInstance ();
		$command = "commit";
		$stmt=mysqli_prepare($link, $command);
		mysqli_stmt_execute($stmt);
		
		mysqli_stmt_close ( $stmt );
}
//...........................................................................
	function api_auth_hit($rand_twextra_key ){
		
		$script_path = __FUNCTION__ ;
		
		$link = DbConnect::getInstance();
		
       $command="SET NAMES 'utf8'";
       $stmt=mysqli_prepare($link, $command);
       mysqli_stmt_execute($stmt);
       
		$command="select email from api_auth where api_key = ?";

		if($stmt=mysqli_prepare($link, $command)){
			mysqli_stmt_bind_param($stmt, 'i', $rand_twextra_key );
			mysqli_stmt_execute($stmt);
			//mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $email);
			mysqli_stmt_fetch($stmt);
			if(mysqli_stmt_num_rows($stmt) == 0){
				$result = 'miss';
			}else{
				$result = 'hit';
			}
		}else{
			$result='error';
		}

		mysqli_stmt_close($stmt);
		return $result;
	}
//................................................................................	
	function saveStat(){
				
		//get data base connection;
		$success=true;

        $remote_ip = $_SERVER['REMOTE_ADDR'];
		
		$link = DbConnect::getInstance();
	
		$command="insert into stat (id, remote_ip, access_date) values('', ?, now())";

		if($stmt=mysqli_prepare($link, $command)){
			mysqli_stmt_bind_param($stmt, 's', $remote_ip);
			mysqli_stmt_execute($stmt);
			$count=mysqli_stmt_affected_rows($stmt);
			if($count==0){
				$success=false;
			}
		}else{
			$success=false;
		}

		mysqli_stmt_close($stmt);
        
        return $success;
    }
    //..............................................................................
    function get_messages_stats() { 
        $link = DbConnect::getInstance ();
        
        $script_path = __FUNCTION__;
        
        $command1 = "(SELECT m.created_date, count( DISTINCT m.url ) AS uniq1
                    FROM message m
                    GROUP BY m.created_date
                    ORDER BY m.created_date ASC
                    LIMIT 0 , 365)";
        
        $command2 = "(SELECT a.created_date, count( DISTINCT a.url ) AS uniq2
                    FROM amessage a
                    GROUP BY a.created_date
                    ORDER BY a.created_date ASC
                    LIMIT 0 , 365)";
        
        $command = "select *
                    from (
                            $command1
                            union
                            $command2
                    )as top";
        
        if ($stmt = mysqli_prepare ($link, $command)) {
            
            //mysqli_stmt_bind_param($stmt, 's', $screen_name);
            mysqli_stmt_execute ( $stmt );
            mysqli_stmt_store_result ( $stmt );
            mysqli_stmt_bind_result ( $stmt, $access_date, $uniq );
            
            $data = array();
            
            while ( mysqli_stmt_fetch ( $stmt ) ) {
                $data [$access_date] = isset($data[$access_date]) ? ($data[$access_date] + $uniq) : $uniq;
            }
        
        }
        
        mysqli_stmt_close ( $stmt );
        
        ksort($data);
        
        return $data;
    }	
	
//....................................................................................
function get_stats_daily (){

        $link = DbConnect::getInstance();

        $script_path = __FUNCTION__;

        $command = "SELECT access_date, count( DISTINCT remote_ip ) AS uniq
                    FROM stat
                    GROUP BY access_date
                    ORDER BY access_date ASC
                    LIMIT 0 , 730";

        if ($stmt = mysqli_prepare($link, $command)) {

            //mysqli_stmt_bind_param($stmt, 's', $screen_name);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $access_date, $uniq);

            while (mysqli_stmt_fetch($stmt)) {

                $data[$access_date] = $uniq;

            }

        }

        mysqli_stmt_close($stmt);

        return $data;

    }
//.........................................................................
function get_monthly_uniques() {
		
		$link = DbConnect::getInstance ();
		$script_path = __FUNCTION__;
		
		$command = 'select j1 as Jan, j2 as Feb, j3 as Mar, j4 as Apr, j5 as May, j6 as Jun,
j7 as Jul, j8 as Aug, j9 as Sep, j10 as Oct, j11 as Nov, j12 as Dece from (';
		$i = 1;
		while ( $i <= 12 ) {
			if ($i < 10) {
				$idx = "0$i";
			} else {
				$idx = $i;
			}
			$command .= "(select count(distinct remote_ip)j$i
							from stat
							where year(access_date) = '2011' and month(access_date) = '$idx' 
						)jj$i,";
			$i ++;
		}
		$command = substr ( $command, 0, - 1 ).")";
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			
			//mysqli_stmt_bind_param($stmt, 's', $screen_name);
			mysqli_stmt_execute ( $stmt );
			mysqli_stmt_store_result ( $stmt );
			mysqli_stmt_bind_result ( $stmt, $jan, $feb, $mar, $apr, $may, $jun, $jul, 
			$aug, $sep, $oct, $nov, $dec );
			mysqli_stmt_fetch($stmt);
			
			$data = array ('jan' => $jan, 'feb' => $feb, 'mar' => $mar, 'apr' => $apr, 
			'may' => $may, 'jun' => $jun, 'jul' => $jul, 'aug' => $aug, 'sep' => $sep, 
			'oct' => $oct, 'nov' => $nov, 'dec' => $dec );
		}
		
		mysqli_stmt_close ( $stmt );
		
		return $data;
	}
//..........................................................................
	function saveNote($user = '', $note = '', $tag = '', $action = 'insert', $wid = -1){

		//get data base connection;
		$success=true;
		
		//echo "user:note:tag:action:wid::<br>$user:$note:$tag:$action:$wid"; exit;

		$link = DbConnect::getInstance();
		
		if($action == 'edit'){
		    $command="update w_note set note  = ?,
		                                tag   = ?,
		                                created = now()
		                            where wid = ?";
		}else{
		    $command="insert into w_note (wid, note, user, tag, created) values('', ?, ?, ?, now())";
		}
		
		if ($stmt = mysqli_prepare ( $link, $command )) {
			if ($action == 'edit') {
				mysqli_stmt_bind_param ( $stmt, 'ssi', $note, $tag, $wid );
			} else {
				mysqli_stmt_bind_param ( $stmt, 'sss', $note, $user, $tag );
			}
			mysqli_stmt_execute($stmt);
			$count=mysqli_stmt_affected_rows($stmt);
			if($count==0){
				$success=false;
			}
		}else{
			$success=false;
		}

		mysqli_stmt_close($stmt);

		return $success;
	}
//.....................................................................
	function getNotesAll($user){
				
		//get data base connection;
		$success=true;

		$link = DbConnect::getInstance();
		
		$command="select wid, note, tag, created from w_note where user = ? order by created desc";
		
		if ($stmt = mysqli_prepare($link, $command)) {
            mysqli_stmt_bind_param($stmt, 's', $user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $wid, $note, $tag, $created);
            while (mysqli_stmt_fetch($stmt)) {
                $data[] = array('wid'=>$wid, 'note'=>$note, 'tag'=>$tag, 'created'=>$created);
            }
        }
        mysqli_stmt_close($stmt);
        return $data;
	}
//.....................................................................
	function searchNote($user, $tag){
				
		//get data base connection;
		$success=true;

		$link = DbConnect::getInstance();
		//validate input tag data:
		$match = preg_match("/;/", $tag);
		if($match > 0){
		    exit("Wrong input tag data");
		}
		
		$tagstring = '';
		//parse tag on spaces or comma:
		$tag_array = preg_split('/[\s,]+/', $tag);
		foreach($tag_array as $tagitem){
		    $tagstring .= " tag like '%".$tagitem."%' or note like '%".$tagitem."%' or ";
		}
		$tagstring = substr($tagstring, 0, -3);
		
		$command="select wid, note, tag, created from w_note where user = ? and $tagstring order by created desc";
		
		if ($stmt = mysqli_prepare($link, $command)) {
            mysqli_stmt_bind_param($stmt, 's', $user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $wid, $note, $tag, $created);
            while (mysqli_stmt_fetch($stmt)) {
                $data[] = array('wid'=>$wid, 'note'=>$note, 'tag'=>$tag, 'created'=>$created);
            }
        }
        mysqli_stmt_close($stmt);
        return $data;
	}
//.....................................................................
	function getNote($wid){
				
		//get data base connection;
		$success=true;

		$link = DbConnect::getInstance();
		
		$command="select wid, note, tag, created from w_note where wid = ?";
		
		if ($stmt = mysqli_prepare($link, $command)) {
            mysqli_stmt_bind_param($stmt, 'i', $wid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $wid, $note, $tag, $created);
            mysqli_stmt_fetch($stmt);
            $data = array('wid'=>$wid, 'note'=>$note, 'tag'=>$tag, 'created'=>$created);
        }
        mysqli_stmt_close($stmt);
        return $data;
	}
//.....................................................................
	function deleteNote($wid){
				
		//get data base connection;
		$success=true;

		$link = DbConnect::getInstance();
		
		$command="delete from w_note where wid = ?";
		
		if ($stmt = mysqli_prepare($link, $command)) {
            mysqli_stmt_bind_param($stmt, 'i', $wid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

			//mysqli_stmt_bind_result($stmt, $wid, $note, $tag, $created);
		// mysqli_stmt_fetch($stmt);
		//$data = array('wid'=>$wid, 'note'=>$note, 'tag'=>$tag, 'created'=>$created);
		}

		mysqli_stmt_close ( $stmt );

		return;

	}

	//.......................................................................
	function backupDbMessage($database_info, $create_msg_table_stmt) {//deprecated
      //deprecated, backing up through the UI timesout, and through the commandline will require setting up include paths etc..
      //current plan is to create another message table once its size is just under 1GB, and then use godaddy backup tool from their website
		
		global $docroot;

		//get data base connection;
		$link = DbConnect::getInstance ();
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );

		

		$command = "select count(*) from message";

		if ($stmt = mysqli_prepare ( $link, $command )) {

			//mysqli_stmt_bind_param($stmt, 'i', $wid);

			mysqli_stmt_execute ( $stmt );

			//mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result ( $stmt, $total_row_count );

			mysqli_stmt_fetch ( $stmt );
		}

		mysqli_stmt_close ( $stmt );
		
		$link = DbConnect::getInstance ();
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );
		
		echo "total row count- message: ", $total_row_count;
		

		$chunk_size = 100;

		$offset = 0;
		$data = '';
		$data .= $database_info;
		$data .= $create_msg_table_stmt;
		$timestamp = date("Y_m_d_H_i_s");

		

		$link = DbConnect::getInstance ();

		

		while ( $offset < $total_row_count ) {

			$command = "select id, tweet, url, created, view_count, last_viewed, remote_ip, blocked, tid, user_id, created_date, 
								message_id_reply, prefix
	 					from message order by created asc limit $offset, $chunk_size";


			if ($stmt = mysqli_prepare ( $link, $command )) {

				//mysqli_stmt_bind_param($stmt, 'i', $wid);

				mysqli_stmt_execute ( $stmt );

				//mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result ( $stmt, $id, $tweet, $url, $created, $view_count, $last_viewed, $remote_ip, $blocked, $tid, 
				                          $user_id, $created_date, $message_id_reply, $prefix );

				
				 $tweet = mysqli_real_escape_string($link, $tweet);
				 $prefix = mysqli_real_escape_string($link, $prefix);

				

				$data .= " insert ignore into message(id, tweet, url, created, view_count, last_viewed, remote_ip, blocked, tid, user_id, created_date, 
			message_id_reply, prefix ) values ";

				while ( mysqli_stmt_fetch ( $stmt ) ) {

					$id = empty ( $id ) ? 0 : $id;

					$tid = empty ( $tid ) ? 0 : $tid;

					$data .= "($id,'$tweet','$url','$created',$view_count,$last_viewed,'$remote_ip',$blocked,$tid,'$user_id','$created_date',
                           '$message_id_reply','$prefix'),";

				}

				$data = substr ( $data, 0, - 1 );
				$data .= ";\n";

				

			} else {

				echo "<br>error";

			}
			$offset = $offset + $chunk_size;
		}

		//file_put_contents ( "db_backup_$timestamp.txt", $data, FILE_APPEND );

		file_put_contents ( $docroot."/backup_db/backup_db_message_$timestamp.sql", $data);

		echo "--end";

		
		mysqli_stmt_close ( $stmt );

		return;

	}

//.......................................................................................	

	function backupDbTwToken($database_info, $create_tw_token_table_stmt) {//deprecated

		global $docroot;

		//get data base connection;
		$link = DbConnect::getInstance ();

		$command = "SET NAMES 'utf8'";

		$stmt = mysqli_prepare ( $link, $command );

		mysqli_stmt_execute ( $stmt );
		
		$command = "select count(*) from tw_token";
		if ($stmt = mysqli_prepare ( $link, $command )) {
			//mysqli_stmt_bind_param($stmt, 'i', $wid);
			mysqli_stmt_execute ( $stmt );
			//mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result ( $stmt, $total_row_count );
			mysqli_stmt_fetch ( $stmt );
		}
		mysqli_stmt_close ( $stmt );
		
		$link = DbConnect::getInstance ();
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );
		
		echo "total row count- tw_token: ", $total_row_count;
		
		//$total_row_count = 20;
		
		$chunk_size = 100;
		$offset = 0;
		$data = '';
		$data .= $database_info;
		$data .= $create_tw_token_table_stmt;
		$timestamp = date("Y_m_d_H_i_s");
		
		$link = DbConnect::getInstance ();
		
		while ( $offset < $total_row_count ) {
			$command = "select tid, user_id_hash, oauth_token, oauth_token_secret, oauth_verifier, user_id, 
			                   screen_name, name, location, description, user_image_url, created
	 					from tw_token order by created asc limit $offset, $chunk_size";

			if ($stmt = mysqli_prepare ( $link, $command )) {
				//mysqli_stmt_bind_param($stmt, 'i', $wid);
				mysqli_stmt_execute ( $stmt );
				//mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result ( $stmt, $tid, $user_id_hash, $oauth_token, $oauth_token_secret, $oauth_verifier, $user_id, 
				                          $screen_name, $name, $location, $description, $user_image_url, $created);
				
				 $screen_name = mysqli_real_escape_string($link, $screen_name);
				 $name = mysqli_real_escape_string($link, $name);
				 $location = mysqli_real_escape_string($link, $location);
				 $description = mysqli_real_escape_string($link, $description);
				
				$data .= " insert ignore into tw_token(tid, user_id_hash, oauth_token, oauth_token_secret, oauth_verifier, user_id, 
			                   screen_name, name, location, description, user_image_url, created ) values ";
				while ( mysqli_stmt_fetch ( $stmt ) ) {
					$id = empty ( $id ) ? 0 : $id;
					$tid = empty ( $tid ) ? 0 : $tid;
					$data .= "($tid, '$user_id_hash', '$oauth_token', '$oauth_token_secret', '$oauth_verifier', $user_id, 
				                          '$screen_name', '$name', '$location', '$description', '$user_image_url', '$created'),";
				}
				$data = substr ( $data, 0, - 1 );
				$data .= ";\n";
				
			} else {
				echo "<br>error";
			}
			$offset = $offset + $chunk_size;
		}
		
		
		//file_put_contents ( "db_backup_$timestamp.txt", $data, FILE_APPEND );
		file_put_contents ( $docroot."/backup_db/backup_db_tw_token_$timestamp.sql", $data);
		//file_put_contents('junk.sql','test');
		//file_put_contents ( "backup_db_tw_token.sql", $data);
		echo "--end";
		
		mysqli_stmt_close ( $stmt );
		return;
	}	
//...........................class ending curly brace.....................................................	
}
?>
