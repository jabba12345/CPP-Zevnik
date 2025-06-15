<?php

	$host = 'localhost';
	$user = 'root';
	$password = '';
	$database = 'cppdb';
	
	$link = mysqli_connect($host, $user, $password, $database) 
		or die("Povezovanje ni mogoče.");
	
	mysqli_set_charset($link, "utf8");
	
?>
