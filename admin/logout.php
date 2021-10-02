<?php 
	require_once("../includes/config.php");
	require_once("../includes/function.php");
	require_once("../class/classDb.php");
	require_once("../class/agentClass.php");

	$db = new Database();
	$objAgent = new agent();
	
	
	session_destroy();
	
	echo "<script>window.location.href='index.php';</script>";

?>