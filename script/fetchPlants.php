<?php
	require_once('../script/dbConn.php');
	
	$ctr = 1;
	
	if(isset($_GET['q'])) {
		if(cleanString($_GET['q']) == '') {
			$ajaxSQL = 'SELECT * FROM hexel_plant;';
			$plantSQLCtr = 'SELECT * FROM hexel_plant;';
			$plantSQLCtrQuery = $conn->query($plantSQLCtr);
			$ctr = $plantSQLCtrQuery->num_rows;
			$ajaxQuery = $conn->query($ajaxSQL);
		} else {
			$ajaxSQL = $conn->prepare("SELECT * FROM hexel_plant WHERE plant_name LIKE ?;");
			$ajaxSQL->bind_param('s',$search);
			$search = '%' . cleanString($_GET['q']) . '%';
			$ajaxSQL->execute();
			$ajaxQuery = $ajaxSQL->get_result();
		}
	}
	
	if($ctr < 1) {
		// database is empty.
		echo "\n<a href='#' target='_self' class='list-group-item list-group-item-action list-group-item-success disabled'>";
		echo "\n<div class='d-flex justify-content-between'>";
		echo "\n<span class='text-sm-left ml-1 mr-1 no-wrap' id='l-shoulder' aria-hidden='true'></span>";
		echo "\n<span class='text-sm-center text-dark text-capitalize'>Oops! Plant database seems to be empty! Try again later!</span>";
		echo "\n<span class='text-sm-right mr-1 ml-1 no-wrap' id='r-shoulder' aria-hidden='true'></span>";
		echo "\n</div>\n</a>";
	} else {
		if($ajaxQuery->num_rows > 0) {
			// database not empty and has search results
			while($row = $ajaxQuery->fetch_assoc()) {
				echo "\n<a href='./article.php?pid=" . $row['plant_id'] . "' target='_self' class='list-group-item list-group-item-action list-group-item-success shadow my-2 rounded' id='plant-list-group'>";
				echo "\n<div class='d-flex justify-content-between'>";
				echo "\n<span class='text-sm-left ml-1 mr-1 no-wrap' id='l-shoulder' aria-hidden='true'></span>";
				echo "\n<span class='text-sm-center text-dark text-capitalize'>" . $row['plant_name'] . "</span>";
				echo "\n<span class='text-sm-right mr-1 ml-1 no-wrap' id='r-shoulder' aria-hidden='true'></span>";
				echo "\n</div>\n</a>";
			}
		} else {
			// database not empty but can't find any data
			echo "\n<a href='#' target='_self' class='list-group-item list-group-item-action list-group-item-success disabled'>";
			echo "\n<div class='d-flex justify-content-between'>";
			echo "\n<span class='text-sm-left ml-1 mr-1 no-wrap' id='l-shoulder' aria-hidden='true'></span>";
			echo "\n<span class='text-sm-center text-dark text-capitalize'>Oops! Plant not found. Please refine your search or try again later!</span>";
			echo "\n<span class='text-sm-right mr-1 ml-1 no-wrap' id='r-shoulder' aria-hidden='true'></span>";
			echo "\n</div>\n</a>";
		}
	}
	$ajaxQuery->close();
	$conn->close();
	
	function cleanString($x) {
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		$x = trim($x);
		return $x;
	}
?>