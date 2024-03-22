<?php

/*
* # MySQLi connection 
* #
* # Define parameters
* # 
* # $dbHost - IP/Hostname of phpMyAdmin/mySQL
* # $dbUser - phpMyAdmin/mySQL username
* # $dbPass - phpMyAdmin/mySQL password
* # $dbName - Database name/schema
*/

$dbHost = 'localhost';
$dbUser = 'id15323553_studiohexel';
$dbPass = '!MunukalaEsmeralda2020';
$dbName = 'id15323553_hexel_database';

/*
* Start MySQLi connection
*/
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if($conn->connect_error) {
	// Connection failed. Show this error.
	die('Unable to connect to database. Error received: ' . $conn->connect_error);
}

?>