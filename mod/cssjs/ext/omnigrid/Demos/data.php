<?php
	
	$pagination = false;
	if ( isset($_REQUEST["page"]) )
	{
		$pagination = true;
		
		$page = intval($_REQUEST["page"]);
		$perpage = intval($_REQUEST["perpage"]);
	}
	
	// this variables Omnigrid will send only if serverSort option is true
	$sorton = $_REQUEST["sorton"];
	$sortby = $_REQUEST["sortby"];
	
	mysql_connect("localhost", "root", "");
	mysql_select_db("mysql");
	
	$n = ( $page -1 ) * $perpage;
	
	
	
	$result = mysql_query("SELECT COUNT(*) AS count FROM help_category");
	$row = mysql_fetch_object($result);
	$total = $row->count;	

	$limit = "";
	
	if ( $pagination )
		$limit = "LIMIT $n, $perpage";
	
	$result = mysql_query("SELECT * FROM help_category $limit");

	$ret = array();
	while ($row = mysql_fetch_object($result)) {
		array_push($ret, $row);
	}

	$ret = array("page"=>$page, "total"=>$total, "data"=>$ret);

	echo json_encode($ret);

	mysql_free_result($result);
