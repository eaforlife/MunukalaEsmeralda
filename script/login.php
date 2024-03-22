<?php
	session_start(); // important since we have user login
	require_once("../script/dbConn.php");
	
	$errorCount = array();
	$cleanUsn = $cleanPwd = "";
	
	if(isset($_POST['loginUsername'])) {
		$x = cleanString($_POST['loginUsername']);
		if($x == "") {
			$errorCount['loginUsername'] = 'This field is required!';
		} else {
			$cleanUsn = $x;
		}
	} else {
		$errorCount['loginUsername'] = 'This field is required!';
	}
	if(isset($_POST['loginPassword'])) {
		$x = cleanString($_POST['loginPassword']);
		if($x == "") {
			$errorCount['loginPassword'] = 'This field is required!';
		} else {
			$cleanPwd = $x;
		}
	} else {
		$errorCount['loginPassword'] = 'This field is required!';
	}
	
	if(count($errorCount) > 0) {
		$errorCount['has-error'] = 'YES';
		echo json_encode($errorCount);
	} else {
		$loginSQL = $conn->prepare("SELECT * FROM hexel_acct WHERE LOWER(acct_usn) = ? AND acct_pwd = ? LIMIT 1;");
		$loginSQL->bind_param('ss', $cleanUsn, $cleanPwd);
		$cleanUsn = strtolower($cleanUsn);
		$cleanPwd = md5($cleanPwd);
		$loginSQL->execute();
		$loginQuery = $loginSQL->get_result();
		/*if(!$loginSQL->execute()) {
			$_SESSION['uID'] = $row['acct_id'];
			$errorCount['has-error'] = 'NO';
		} else {
			$errorCount['loginUsername'] = 'Username/Password does not match or exists!';
			$errorCount['loginPassword'] = 'Username/Password does not match or exists!';
			$errorCount['has-error'] = 'YES';
		}*/
	
		//$loginQuery = $conn->query($loginSQL);
		if($loginQuery->num_rows > 0) {
			while($row = $loginQuery->fetch_assoc()) {
				$_SESSION['uID'] = $row['acct_id'];
				$errorCount['has-error'] = 'NO';
			}
		} else {
			$errorCount['loginUsername'] = 'Username/Password does not match or exists!';
			$errorCount['loginPassword'] = 'Username/Password does not match or exists!';
			$errorCount['has-error'] = 'YES';
		}
		
		//$loginSQL->close();
		$conn->close();
		echo json_encode($errorCount);
	}
	
	
	function cleanString($x) {
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		$x = trim($x);
		return $x;
	}
	
?>