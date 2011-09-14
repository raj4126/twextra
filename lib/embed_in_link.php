<?php
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once  $docroot."/config.php";
////$subject = "@one <a href='http://example.com'>@two</a> hello world @three   @one.";
////$subject = "http://www.example.com one two three";
//$subject = 'test  @test  test@yahoo.com';
//echo "\noriginal string: ", $subject;
//$pattern_to_embed =  'at_reply';
////$pattern_to_embed =  'link';
//$embed  = new EmbedInLink($subject, $pattern_to_embed);
//$subject = $embed->embedPattern();
//echo "\nembedded string: ", $subject;
//..........................................................................

class EmbedInLink {
	//define member variables
	
    //test url:  http://www.amazon.com/Kindle-Wireless-Reading-Display-Generation/dp/B0015T963C/ref=amb_link_353392262_2?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=center-1&pf_rd_r=0X728X8G4D9DAS0M8AFA&pf_rd_t=101&pf_rd_p=1267052482&pf_rd_i=507846
    //<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/8MgPlvzeUVU?fs=1&amp;hl=en_US"></param>
    //<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
    //<embed src="http://www.youtube.com/v/8MgPlvzeUVU?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" 
    //allowfullscreen="true" width="480" height="385"></embed></object>
	private $subject;
	private $pattern;
	private $pattern_to_embed;//'at_reply' or 'link'
	private $pattern_a_start = '<a';
	private $pattern_a_end = '</a>';
	private $twitter_embedded_token_max;
	//.....................................................
	function __construct($subject, $pattern_to_embed) {
		//convert all html entities to their applicable characters??
		$this->subject = $subject;
		$this->pattern_to_embed = $pattern_to_embed;
		$config_params = Config::getConfigParams();
		$this->twitter_embedded_token_max = $config_params['twitter_embedded_token_max'];
		if ($pattern_to_embed == 'at_reply') {
			//$this->pattern = "/(\s|>|)@[_a-zA-Z0-9]{1,15}/";
			  $this->pattern = "/(\s|>|\.|&nbsp;|^)@[_a-zA-Z0-9]{1,15}/";
		} else if ($pattern_to_embed == 'hash_tag') {
			  $this->pattern = "/#[a-zA-Z][_a-zA-Z0-9]{1,139}(\s|<|&nbsp;|$)/";
		} else if ($pattern_to_embed == 'link') {
		    //$this->pattern = '/https?:\/\/[A-Za-z0-9.?&=+;:~#_%\-\/]+(<|\s|&nbsp;|$)/'; //k-original 
		    $this->pattern = '/(h|H)(t|T)(t|T)(p|P)(s|S)?:\/\/[A-Za-z0-9.?&=+;:~#!_%,\-\/]+(<|\s|&nbsp;|\n|$)/'; //k-original 
		}else if($pattern_to_embed == 'email'){
			//$this->pattern = '/(\s|>)[_a-zA-Z0-9-_.]+[_a-zA-Z0-9-_]+@[_a-zA-Z0-9]+.[_a-zA-Z0-9]+(\s|<)/';
			$this->pattern = '/(\s|>)[_a-zA-Z0-9-_.]+[_a-zA-Z0-9-_]+@[_a-zA-Z0-9]+.[_a-zA-Z0-9]+(\s|<|&nbsp;)/';
		}
	}
	//...............................................
	function embedPattern() {
		//get list of @replies, and position of anchor tag starts, ends, and @replies
		$script_path = __FUNCTION__;

		$tokens_in = array();
		preg_match_all ( $this->pattern, $this->subject, &$tokens_in );
		$tokens = $tokens_in [0];

			

		//remove urls which are within object tags because we do not want to embed urls within object tags in videos
		if ($this->pattern_to_embed == 'link') {

			$tokens = $this->remove_object_tokens ( $tokens ); //
		}
		
		logger($script_path.'  subject: ', "--".$this->subject."--");
		logger($script_path.'  strlen(subject): ', strlen($this->subject));
		logger($script_path.'  tokens 1: ', $tokens);
		logger($script_path.'  tokens_in 1: ', $tokens_in);
		
		//do not embed tokens if the token count is above a max limit
		if(count($tokens) > $this->twitter_embedded_token_max){
			return $this->subject;
		}
		
		// for all cases, take out leading ">" and trailing "<" characters, if any
			$j = 0;

        foreach ($tokens as $token) {

            if (substr($token, 0, 1) == '>' || substr($token, 0, 1) == '.') {

                $tokens[$j] = substr($token, 1);

            }

            if (substr($token, - 1) == '<') {

                $tokens[$j] = substr($token, 0, - 1);

            }

            //trim any spaces
            $tokens[$j] = trim($tokens[$j]);
            
            //strip &nbsp; at the end; only for links..

            if ($this->pattern_to_embed == 'link') {

                $nbsp_pos = strpos($tokens[$j], '&nbsp;');

                if ($nbsp_pos !== false) {

                    $tokens[$j] = substr($token, 0, $nbsp_pos);

                }
                $newline_pos1 = strpos($tokens[$j], '\r\n');

                if ($newline_pos1 !== false) {

                    $tokens[$j] = substr($token, 0, $newline_pos1);

                }
                $newline_pos2 = strpos($tokens[$j], '\n');

                if ($newline_pos2 !== false) {

                    $tokens[$j] = substr($token, 0, $newline_pos2);
                }
            }

            //strip &nbsp; at the beginning; only for at_reply..

            if ($this->pattern_to_embed == 'at_reply') {

                $nbsp_pos = strpos($tokens[$j], '&nbsp;');

                if ($nbsp_pos !== false) {
                    $tokens[$j] = substr($token, $nbsp_pos + 6);
                }
            }

            //strip &nbsp; at the end; only for hash_tag..
            if ($this->pattern_to_embed == 'hash_tag') {

                $nbsp_pos = strpos($tokens[$j], '&nbsp;');

                if ($nbsp_pos !== false) {
                    $tokens[$j] = substr($token, 0, $nbsp_pos);
                }
            }

            $j ++;

        }
			
		logger($script_path.'  tokens 2: ', $tokens);
		if ($this->pattern_to_embed == 'link') {
			$tokens_pos = $this->get_token_position_link ( $this->subject, $tokens );
		} else if($this->pattern_to_embed == 'at_reply') {
			$tokens_pos = $this->get_token_position_at_replies ( $this->subject, $tokens );
		}else if($this->pattern_to_embed == 'hash_tag') {
			$tokens_pos = $this->get_token_position_hash_tag ( $this->subject, $tokens );
		}
		logger($script_path.'  tokens pos array: ', $tokens_pos);
		
		
//		foreach($tokens_pos as $token_pos){
//			logger($script_path.'  start token pos in subject: ', $this->subject[$token_pos]);
//		}
		logger($script_path.'  tokens pos: ', $tokens_pos);
		$k = 0;
		$subject_len = strlen ( $this->subject );
//		while ( $k < $subject_len ) {
//			logger ( "subject[$k]=", $this->subject [$k] );
//			$k ++;
//		}
		
		$anchor_tags_start_pos = $this->get_anchor_tags_position ( $this->subject, $this->pattern_a_start );
		$anchor_tags_end_pos   = $this->get_anchor_tags_position ( $this->subject, $this->pattern_a_end );
		
		logger($script_path.'  anchor start pos: ', $anchor_tags_start_pos);
		logger($script_path.'  anchor end pos: ', $anchor_tags_end_pos);

		//embed tokens which are not embedded in anchor tags

		$subject_embedded = $this->embed_tokens ( $this->subject, $tokens, $tokens_pos, 
	                    $anchor_tags_start_pos, $anchor_tags_end_pos);
		
		return $subject_embedded;
	}
	//..........................................................................
	//decode character entities inside object tags
	public function entityDecodeObject($tweet){
		
		$offset = 0;
		$diff = 0;
		
		$i = 0;
		
		while($i < 100){
		
			$obj_starts = strpos ( $tweet, '&lt;object', $offset );
			$obj_ends = strpos ( $tweet, '&lt;/object&gt;', $offset ) + 14; //we want the "end" of </object> tag
			
			if(($obj_starts === false) or ($obj_ends === false)){
				break;
			}
			
			$tweet_len_pre = strlen($tweet);
			
			$tweet_prefix = substr ( $tweet, 0, $obj_starts );
			$tweet_postfix = substr ( $tweet, $obj_ends + 1 );
			$tweet_object = substr ( $tweet, $obj_starts, $obj_ends - $obj_starts + 1 );
					
			$tweet_object = preg_replace ( '#&lt;#', '<', $tweet_object );
			$tweet_object = preg_replace ( '#&gt;#', '>', $tweet_object );
			$tweet_object = preg_replace ( '#&quot;#', '"', $tweet_object );
			$tweet_object = preg_replace ( '#&amp;#', '&', $tweet_object );
			
			$tweet = $tweet_prefix . $tweet_object . $tweet_postfix;
			
			$tweet_len_post = strlen($tweet);
			$diff = $tweet_len_pre - $tweet_len_post;
			
			$offset = $obj_ends - $diff;	
			
			$i++;
		}		
		return $tweet;
	}
	//..................................................................
	//remove urls within object tags from the tokens list
	public function remove_object_tokens($tokens){
		
		$obj_starts = array();
		$obj_ends = array();
		$obj_starts = $this->get_anchor_tags_position($this->subject, '&lt;object');
		
		$obj_ends = $this->get_anchor_tags_position($this->subject, '&lt;/object&gt;');
		
		foreach ( $tokens as $id => $token ) {

			foreach ( $obj_starts as $j => $start ) {

				$objstr = substr ( $this->subject, $obj_starts [$j], ($obj_ends [$j] - $obj_starts [$j] + 1) );

				if (strpos ( $objstr, $token )) {

					unset ( $tokens [$id] );
					break;

				}

			}

		}
		return $tokens;
	}
	//............................................................

	public function get_token_position_link($subject, $tokens) {
		
				$script_path = __FUNCTION__;
				
		$offset = 0;
		$i = 0;
		foreach($tokens as $token) {
			
			$token_position1 = strpos ( $subject, $tokens [$i].'<', $offset );
			$token_position2 = strpos ( $subject, $tokens [$i].' ', $offset );
			$token_position3 = strpos ( $subject, $tokens [$i]."&nbsp;", $offset );
			$token_position4 = strpos ( $subject, $tokens [$i], 0 );
			$token_position5 = strpos ( $subject, $tokens [$i]."\r\n", $offset );
			$token_position6 = strpos ( $subject, $tokens [$i]."\n",   $offset );
			
			logger($script_path.'  token: ', $token);
			logger($script_path.'  token pos <: ', $token_position1);
			logger($script_path.'  token pos s: ', $token_position2);
			logger($script_path.'  token pos &: ', $token_position3);
			logger($script_path.'  token pos 0: ', $token_position4);
			logger($script_path.'  token pos \r\n: ', $token_position5);
			logger($script_path.'  token pos \n: ', $token_position6);
			//logger($script_path.'  token pos 4: ', $token_position4);
			
			if($token_position1 === false){
				$token_position1 = 1000000;
			}
			if($token_position2 === false){
				$token_position2 = 1000000;
			}
			if($token_position3 === false){
				$token_position3 = 1000000;
			}
			if($token_position5 === false){
				$token_position5 = 1000000;
			}
			if($token_position6 === false){
				$token_position6 = 1000000;
			}
			
			logger($script_path.'  token pos 1: ', $token_position1);
			logger($script_path.'  token pos 2: ', $token_position2);
			logger($script_path.'  token pos 3: ', $token_position3);
			logger($script_path.'  token pos 4: ', $token_position4);
			logger($script_path.'  token pos 5: ', $token_position5);
			logger($script_path.'  token pos 6: ', $token_position6);
			
			$token_position = min($token_position1, $token_position2, $token_position3, $token_position5, $token_position6);
			
			if(($offset == 0) && ($token_position4 === 0 )){
				$token_position = 0;
			}
			
			logger($script_path.'  token pos final: ', $token_position);
			
			if ($token_position === false) {
				break;
			}else{
				$tokens_pos[$i] = $token_position;
			}
			$offset = $tokens_pos [$i] + 1;
			$i ++;
		}
		return $tokens_pos;
	}
	//..............................................................
	public function get_token_position_at_replies($subject, $tokens) {
		
				$script_path = __FUNCTION__;
				
		$offset = 0;
		$i = 0;
		foreach($tokens as $token) {
			
			$token_position = strpos ( $subject, $tokens [$i], $offset );
			
			logger($script_path.'  token pos final: ', $token_position);
			
			if ($token_position === false) {
				$tokens_pos[$i] = 1000000;
			}else{
				$tokens_pos[$i] = $token_position;
			}
			$offset = $tokens_pos [$i] + 1;
			$i ++;
		}
		return $tokens_pos;
	}	
	//..............................................................
	public function get_token_position_hash_tag($subject, $tokens) {
		
				$script_path = __FUNCTION__;
				
		$offset = 0;
		$i = 0;
		foreach($tokens as $token) {
			
			$token_position = strpos ( $subject, $tokens [$i], $offset );
			
			logger($script_path.'  token pos final: ', $token_position);
			
			if ($token_position === false) {
				$tokens_pos[$i] = 1000000;
			}else{
				$tokens_pos[$i] = $token_position;
			}
			$offset = $tokens_pos [$i] + 1;
			$i ++;
		}
		return $tokens_pos;
	}	
	//.............................................................

	public function get_anchor_tags_position($subject, $pattern) {
		
		$offset = 0;
		$i = 0;
		while ( 1 ) {
			$anchor_tags_position = strpos ( $subject, $pattern, $offset );
			if ($anchor_tags_position === false) {
				break;
			}else{
				$anchor_tags_pos[$i] = $anchor_tags_position;
			}
			$offset = $anchor_tags_pos [$i] + 1;
			$i ++;
		}
		return $anchor_tags_pos;
	}
	//.............................................................
public function check_embedded_token($subject, $token, $token_pos, 
	                     $anchor_tags_start_pos, $anchor_tags_end_pos) {
		
		$script_path = __FUNCTION__;
		
		logger ( $script_path . '  check embedded token: ', $token );
		$is_token_embedded = 'no';
		$j = 0;
		foreach ( $anchor_tags_start_pos as $anchor_start_pos ) {
			logger ( $script_path . '  anchor tags start pos-loop: ', $anchor_tags_start_pos [$j] );
			logger ( $script_path . '  anchor tags end pos-loop: ', $anchor_tags_end_pos [$j] );
			logger ( $script_path . '  token pos-loop: ', $token_pos );
			
			if (($token_pos > $anchor_tags_start_pos [$j]) && ($token_pos < $anchor_tags_end_pos [$j])) {
				$is_token_embedded = 'yes';
				logger ( $script_path . '  token between start and end tags: ', $token );
				logger ( $script_path . '  is token embedded1: ', $is_token_embedded );
				break;
			}
			$j ++;
		}
		
		return $is_token_embedded;
	}
//.............................................................

	public function embed_tokens($subject, $tokens, $tokens_pos, 
	                    $anchor_tags_start_pos, $anchor_tags_end_pos) {
	                    	
		$script_path = __FUNCTION__;
		$i = 0;
		foreach ( $tokens as $token ) {
			logger($script_path."  token: ", $token);
			
			$token = trim($token);
			
			$is_token_embedded = $this->check_embedded_token ( $subject, $token, $tokens_pos[$i], 
			                       $anchor_tags_start_pos, $anchor_tags_end_pos );
			
			logger($script_path.' is_token_embedded2: ', $is_token_embedded);
			
			if ($is_token_embedded == 'no') {
				//embed
					logger($script_path.'  pattern to embed: ', $this->pattern_to_embed);
					logger($script_path.'  inside embed: ', $token);
				
				if ($this->pattern_to_embed == 'link') {
					$token_embedded = "<a href='$token' target='_blank'  class='twextra' >$token</a>";
				} else if ($this->pattern_to_embed == 'at_reply') {
					$token_no_at = substr($token, 1);
					$token_embedded = "<a href='http://twitter.com/$token_no_at ' target='_blank' class='twextra' >$token</a>";
				} else if ($this->pattern_to_embed == 'hash_tag') {
					$token_hash = substr($token, 1);
					$token_embedded = "<a href='http://twitter.com/#search?q=%23$token_hash' target='_blank' class='twextra' >$token</a>";
				}else if ($this->pattern_to_embed == 'email') {
					$token_embedded = "<a href='mailto:$token' target='_blank' class='twextra' >$token</a>";  
				}else{
					exit("embed_tokens: Error");
				}
					
					$subject_pre = substr ( $subject, 0, $tokens_pos [$i] ); 
					$subject_post = substr ( $subject, $tokens_pos [$i] + strlen ( $token ) ); 
					
					if($tokens_pos[$i] == 0){
						$subject_pre = '';
					}
					
					logger($script_path.'  tokens_pos: ', $tokens_pos[$i]);
					logger($script_path.'  subject_pre: ', $subject_pre);
					logger($script_path.'  subject_post: ', $subject_post);
					
					
					$subject = $subject_pre . $token_embedded . $subject_post;
					
					$token_embedded_len = strlen($token_embedded);
					$token_len = strlen($token);
					
					$offset = $token_embedded_len - $token_len;
					
//					logger($script_path.' strlen: token_embedded: ', "-".$token_embedded_len."-");
//					logger($script_path.' strlen: token: ', "-".$token_len."-");
//					logger($script_path.' offset: ', "-".$offset."-");
//					logger($script_path.' subject_pre: ', "-".$subject_pre."-");
//					logger($script_path.' token: ', "-".$token."-");
//					logger($script_path.' token_embedded: ', "-".$token_embedded."-");
//					logger($script_path.' subject_post: ', "-".$subject_post."-");
//					logger($script_path.' subject: ', "-".$subject."-");
					$k=0; $subject_len = strlen($subject);
//					while($k < $subject_len){
//						logger("subject[$k]=", $subject[$k]);
//						$k++;
//					}
//					logger($script_path.' token length: ', strlen($token));
//					logger($script_path.' tokens pos: ', $tokens_pos);
//					logger($script_path.' tokens pos i: ', $i);
//					
//					logger($script_path.' subject token pos char: ', $subject[$tokens_pos[$i]]);
//					logger($script_path.' offset: ', $offset);
					
					//update token_pos, anchor_tags_start_pos and anchor_tags_end_tags
					$j = 0;
					foreach ( $anchor_tags_start_pos as $anchor_tag_start_pos ) {
						if ($anchor_tags_start_pos [$j] > $token_pos [$i]) {
							$anchor_tags_start_pos [$j] = $anchor_tags_start_pos [$j] + $offset;
						}
						$j ++;
					}
					
					$j = 0;
					foreach ( $anchor_tags_end_pos as $anchor_tag_end_pos ) {
						if ($anchor_tags_end_pos [$j] > $token_pos [$i]) {
							$anchor_tags_end_pos [$j] = $anchor_tags_end_pos [$j] + $offset;
						}
						$j ++;
					}
					
					$j = 0;
					foreach ( $tokens_pos as $token_pos ) {
						if ($tokens_pos [$j] > $tokens_pos [$i]) {
							$tokens_pos [$j] = $tokens_pos [$j] + $offset;
						}
						$j ++;
					}
			}else{
				//flush token from tokens and pos arrays..
			}
				$i ++;
		}			

		return $subject;
	}
	//...........................................................................

}
?>