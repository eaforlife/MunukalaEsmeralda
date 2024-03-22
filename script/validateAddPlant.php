<?php
	require_once("../script/dbConn.php");
	
	// Add Plant validation
	
	$errorCount = array();
	$validatedFields = array();
	$gallery = false;
	
	//json_encode to pass array to javascript. needed since we are validating fields server side.
	//echo json_encode($_POST);
	
	// Validate Form
	if(isset($_POST['plant-uid'])) {
		$x = cleanString($_POST['plant-uid']);
		if($x == "") {
			$errorCount['has-error-message'] = "NULL";
		} else {
			$validatedFields['plant-userID'] = intval($x);
		}
	} else {
		$errorCount['has-error-message'] = "Unauthorized access!";
	}
	if(isset($_POST['new-plant-name'])) {
		$x = cleanString($_POST['new-plant-name']);
		if($x == "") {
			$errorCount['new-plant-name'] = "This field is required!";
		} elseif(strlen($x) <= 3) {
			$errorCount['new-plant-name'] = "This field is too short (minimum of 4 characters required)!";
		} elseif(strlen($x) > 50) {
			$errorCount['new-plant-name'] = "This field is too long (maximum of 50 characters required)!";
		} else {
			$validatedFields['new-plant-name'] = $x;
		}
	} else {
		$errorCount['new-plant-name'] = "This field is required!";
	}
	if(isset($_POST['new-plant-desc'])) {
		$x = cleanString($_POST['new-plant-desc']);
		if($x == "") {
			$errorCount['new-plant-desc'] = "This field is required!";
		} elseif(strlen($x) < 10) {
			$errorCount['new-plant-desc'] = "This field is too short (minimum of 10 characters required)!";
		} elseif(strlen($x) > 1000) {
			$errorCount['new-plant-desc'] = "This field is too long (maximum of 1000 characters required)!";
		} else {
			$validatedFields['new-plant-desc'] = $x;
		}
	} else {
		$errorCount['new-plant-desc'] = "This field is required!";
	}
	if(isset($_POST['new-plant-care'])) {
		$x = cleanString($_POST['new-plant-care']);
		if($x == "") {
			$errorCount['new-plant-care'] = "This field is required!";
		} elseif(strlen($x) < 10) {
			$errorCount['new-plant-care'] = "This field is too short (minimum of 10 characters required)!";
		} elseif(strlen($x) > 1000) {
			$errorCount['new-plant-care'] = "This field is too long (maximum of 1000 characters required)!";
		} else {
			$validatedFields['new-plant-care'] = $x;
		}
	} else {
		$errorCount['new-plant-care'] = "This field is required!";
	}
	if(isset($_POST['new-plant-price-from'])) {
		$x = cleanString($_POST['new-plant-price-from']);
		if($x == "" || checkPrice($x) == 0) {
			$errorCount['new-plant-price-from'] = "This field is required or must be a valid decimal number!";
		}  else {
			if(floatval($x) <= 0 || intval($x) <= 0) {
				$errorCount['new-plant-price-from'] = "Amount must not be equal to 0!";
			} else {
				if(is_int($x)) {
					$x = number_format($x, 2, '.', '');
				}
				$validatedFields['new-plant-price-from'] = floatval($x);
			}
		}
	} else {
		$errorCount['new-plant-price-from'] = "This field is required!";
	}
	if(isset($_POST['new-plant-price-to'])) {
		$x = cleanString($_POST['new-plant-price-to']);
		if($x == "" || checkPrice($x) == 0) {
			$errorCount['new-plant-price-to'] = "This field is required or must be a valid decimal number!";
		}  else {
			if(floatval($x) <= 0 || intval($x) <= 0) {
				$errorCount['new-plant-price-to'] = "Amount must not be equal to 0!";
			} else {
				if(is_int($x)) {
					$x = number_format($x, 2, '.', '');
				}
				$validatedFields['new-plant-price-to'] = floatval($x);
			}
		}
	} else {
		$errorCount['new-plant-price-to'] = "This field is required!";
	}
	if(isset($_POST['new-plant-price-comment'])) {
		$x = cleanString($_POST['new-plant-price-comment']);
		if($x == "") {
			$x = "NULL";
		}
		$validatedFields['new-plant-price-comment'] = $x;
	} else {
		$validatedFields['new-plant-price-comment'] = "NULL";
	}
	
	if(isset($_FILES['new-plant-file'])) {
		if(!empty($_FILES['new-plant-file']['name'])) {
			$uploadedExtention = pathinfo(strtolower($_FILES['new-plant-file']['name']), PATHINFO_EXTENSION);
			$x = $_FILES["new-plant-file"]["tmp_name"];
			$checkifImage = getimagesize($_FILES["new-plant-file"]["tmp_name"]);
			
			if($checkifImage === false) {
				$errorCount['new-plant-file'] = "Unrecognized File!"; // check if it's an actual image
			} elseif($uploadedExtention != 'jpg' && $uploadedExtention != 'jpeg' && $uploadedExtention != 'png') {
				$errorCount['new-plant-file'] = "Unsupported image format!";
			} elseif($_FILES["new-plant-file"]["size"] > 6000000) {
				$errorCount['new-plant-file'] = "File too big! Make sure image is smaller than 5mb!";
			} else {
				$validatedFields['new-plant-file'] = $x;
				$validatedFields['new-plant-file-ext'] = $uploadedExtention;
			}
		} else {
			$errorCount['new-plant-file'] = "Cover photo is required!";
		}
	} else {
		$errorCount['new-plant-file'] = "Cover photo is required!";
	}
	
	if(isset($_FILES['new-plant-gallery'])) {
		foreach($_FILES['new-plant-gallery']['tmp_name'] as $imgFile => $name) {
			if(!empty($_FILES['new-plant-gallery']['tmp_name'][$imgFile])) {
				$gallery = true;
				$uploadedExtention = pathinfo(strtolower($_FILES['new-plant-gallery']['name'][$imgFile]), PATHINFO_EXTENSION);
				$x = $_FILES["new-plant-gallery"]["tmp_name"][$imgFile];
				$checkifImage = getimagesize($_FILES["new-plant-gallery"]["tmp_name"][$imgFile]);
				
				if($checkifImage === false) {
					$errorCount['new-plant-gallery'] = "One or more file is not an image!"; // check if it's an actual image
				} elseif($uploadedExtention != 'jpg' && $uploadedExtention != 'jpeg' && $uploadedExtention != 'png') {
					$errorCount['new-plant-gallery'] = "One or more image file is not supported!";
				} elseif($_FILES["new-plant-gallery"]["size"][$imgFile] > 6000000) {
					$errorCount['new-plant-gallery'] = "One or more file is too big! Make sure image is smaller than 5mb!";
				} else {
					$validatedFields['new-plant-gallery'][$imgFile] = $x;
					$validatedFields['new-plant-gallery-ext'][$imgFile] = $uploadedExtention;
				}
			} else {
				$gallery = false;
				//$errorCount['new-plant-gallery'] = "At least one or more photo is required!";
			}
		}
	} else {
		$gallery = false;
		//$errorCount['new-plant-gallery'] = "At least one or more photo is required!";
	}
	
	if(count($errorCount) > 0) {
		$errorCount['has-error'] = "YES";
		$errorCount['has-error-message'] = "NULL";
		echo json_encode($errorCount);
	} else {
		// Validate one more time for price range
		if(floatval($validatedFields['new-plant-price-from']) > floatval($validatedFields['new-plant-price-to'])) {
			$errorCount['new-plant-price-from'] = "Amount must greater than maximum price range!";
		}
		
		if(count($errorCount) > 0) {
			$errorCount['has-error'] = "YES";
			$errorCount['has-error-message'] = "NULL";
			echo json_encode($errorCount);
		} else {
			// Move image to directory
			$errorCount['has-error'] = "NO";
			$errorCount['has-error-message'] = "NULL";
			
			$currTimeStamp = new DateTime();
			$dtString = $currTimeStamp->format('YmdGis');
			
			$newFileName = str_replace(' ','',$validatedFields['new-plant-name']) . '-001-bg.' . $validatedFields['new-plant-file-ext'];
			move_uploaded_file($validatedFields['new-plant-file'], '../style/img/' . $newFileName);
			
			$newPlantFileName = cleanString($newFileName);
			
			$plantID = 0;
			
			// Add to database 10000
			//$addPlantSQL = "INSERT INTO hexel_plant VALUES (NULL,'".$validatedFields['new-plant-name']."','$newPlantFileName',CURRENT_TIMESTAMP,'".$validatedFields['plant-userID']."');";
			$addPlantSQL = $conn->prepare("INSERT INTO hexel_plant VALUES (NULL,?,?,CURRENT_TIMESTAMP,?);");
			$addPlantSQL->bind_param('ssi',$validatedFields['new-plant-name'],$newPlantFileName,$validatedFields['plant-userID']);
			//$addPlantQuery = $conn->query($addPlantSQL);
			if(!$addPlantSQL->execute()) {
				$errorCount['has-error'] = "YES";
				$errorCount['has-error-message'] = "An error has occurred while adding plant. Try again later. Error: " . $addPlantSQL->error;
			} else {
				$plantID = $conn->insert_id;
				$errorCount['has-error'] = "NO";
				$errorCount['has-error-message'] = "NULL";
			}
			$addPlantSQL->close();
			
			if($errorCount['has-error'] == "NO" && $errorCount['has-error-message'] == "NULL") {
				//$addPlantDescSQL = "INSERT INTO hexel_plant_desc VALUES (NULL,'$plantID','" . $validatedFields['new-plant-desc'] . "','" . $validatedFields['new-plant-care'] . "','" . $validatedFields['new-plant-price-from'] . "','" . $validatedFields['new-plant-price-to'] . "','" . $validatedFields['new-plant-price-comment'] . "',0,CURRENT_TIMESTAMP);";
				$addPlantDescSQL = $conn->prepare("INSERT INTO hexel_plant_desc VALUES (NULL,?,?,?,?,?,?,?,CURRENT_TIMESTAMP);");
				$addPlantDescSQL->bind_param('issddsi',$plantID,$validatedFields['new-plant-desc'],$validatedFields['new-plant-care'],$validatedFields['new-plant-price-from'],$validatedFields['new-plant-price-to'],$validatedFields['new-plant-price-comment'],$updateBool);
				$plantID = intval($plantID); // make sure it's integer.
				$updateBool = 0;
				
				if(!$addPlantDescSQL->execute()) {
					$errorCount['has-error'] = "YES";
					$errorCount['has-error-message'] = "An error has occurred while adding plant. Try again later. Error: " . $addPlantDescSQL->error;
				} else {
					$errorCount['has-error'] = "NO";
					$errorCount['has-error-message'] = "Successfully added plant to database.";
				}
			}
			$addPlantDescSQL->close();
			
			$GalleryFileName = "";
			// Add Gallery
			if($gallery == true) {
				foreach($validatedFields['new-plant-gallery'] as $ix => $val) {
					$newGalleryImgName = $dtString . '-' . $plantID . '-gallery-' . $ix . '.' . $validatedFields['new-plant-gallery-ext'][$ix];
					$cleanGalleryImg = cleanString($newGalleryImgName);
					
					$addPlantGallerySQL = $conn->prepare("INSERT INTO hexel_plant_gallery VALUES (NULL,?,?);");
					$addPlantGallerySQL->bind_param('is',$plantID,$cleanGalleryImg);
					if(!$addPlantGallerySQL->execute()) {
						$errorCount['has-error'] = "YES";
						$errorCount['has-error-message'] = "An error has occurred while adding gallery photos to database. Try again later. Error: " . $addPlantGallerySQL->error;
					} else {
						$errorCount['has-error'] = "NO";
						$errorCount['has-error-message'] = "Successfully added plant gallery photo to database.";
					}
					$addPlantGallerySQL->close();
					move_uploaded_file($validatedFields['new-plant-gallery'][$ix], '../style/img/' . $cleanGalleryImg);
					$GalleryFileName = $cleanGalleryImg . "~" . $GalleryFileName;
				}
				$errorCount['has-gallery'] = $GalleryFileName;
			} else {
				$errorCount['has-gallery'] = "false";
			}
			$conn->close();
			$errorCount['has-uid'] = $plantID;
			$errorCount['has-img'] = $newPlantFileName;
			echo json_encode($errorCount);
		}
	}
	
	
	function checkPrice($x) {
		$pattern = "/^\d*\.?[0-9]{1,2}$/";
		return preg_match($pattern, $x);
	}
	
	function cleanString($x) {
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		$x = trim($x);
		return $x;
	}
	
?>