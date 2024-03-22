<?php

session_start();
$errorCount = array();

if(isset($_GET['logout'])) {
	$x = cleanString($_GET['logout']);
	
	if($x == "" && $x != "1") {
		$errorCount['has-error'] = 'YES';
	}
} else {
	$errorCount['has-error'] = 'YES';
}

if(count($errorCount) > 0) {
	echo json_encode($errorCount);
} else {
	// remove all sessions
	session_unset();
	// destroy active sessions
	session_destroy();
	$errorCount['has-error'] = 'NO';
	
	echo json_encode($errorCount);
}

if(isset($_GET['inactive'])) {
	// remove all sessions
	session_unset();
	// destroy active sessions
	session_destroy();
	echo "<script>window.location.replace('../index.php?ref=nosplash&page=logout&result=2');</script>";
}

function cleanString($x) {
	$x = stripslashes($x);
	$x = htmlspecialchars($x);
	$x = trim($x);
	return $x;
}

?>