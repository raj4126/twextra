<?php

if (session_id () == "") session_start ();

$docroot = $_SERVER ['DOCUMENT_ROOT'];

require_once $docroot . "/graphs/phpgraphlib/phpgraphlib.php";

require_once $docroot . "/models/twextra_model.php";



if (isset ( $_REQUEST ['graph_type'] )) {
	$graph_type = $_REQUEST ['graph_type'];
}



create_graph($graph_type);



function create_graph ($graph_type = '')

{
    
	$graph = new PHPGraphLib(1400, 600);

    $model = new TwextraModel(); 
    //$data = array("Alex"=>99, "Mary"=>98, "Joan"=>70, "Ed"=>90);

	if ($graph_type == 'daily_stats') {
		$data = $model->get_stats_daily ();
    	$graph->setTitle("Daily Unique Traffic");
	} else if ($graph_type == 'message_history') {
	    //this graph is currently not plotted!
		$screen_name = 'rajen4126';
		$message_from = 0;

		$next = 20;
		$order = 'created';
		$asc_desc = 'desc';
        $length = 20;
        
		$data = $model->get_message_history ( $screen_name, $message_from, $next, $order, $asc_desc, $length );
		foreach ( $data as $entry ) {
			$data [$entry [message_id]] = $entry [view_count];
		}

    	$graph->setTitle("Message History");
	} else if($graph_type == 'monthly_stats'){
   		   $data = $model->get_monthly_uniques (  );
   		   $graph->setTitle("Monthly Unique Traffic");
		
	}else if($graph_type == 'messages_stats'){
           $data = $model->get_messages_stats (  );
           $graph->setTitle("Daily Messages Created");
        
    }else{

		$data = array ();

	}

    $graph->addData($data);

    $graph->setTextColor("blue");

    //additions for a line graph..........  

    $graph->setBars(false);

    $graph->setLine(true);

    //$graph->setDataPoints(true);

    $graph->setDataPointColor('maroon');

    $graph->setDataValues(true);

    $graph->setDataValueColor('maroon');

    $graph->setGoalLine(.0025);

    $graph->setGoalLineColor('red');

    //........

    return $graph->createGraph();

}

?>



