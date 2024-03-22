<?php

	require_once('../script/dbConn.php');
	
	$errCount = array();
	
	if(isset($_POST['edit-mode'])) {
		
		if(cleanString($_POST['edit-mode']) == 'delete') {
			$plantID = "";
			if(isset($_POST['plant-id'])) {
				$x = cleanString($_POST['plant-id']);
				
				if($x == "") {
					$errCount['msg'] = "Invalid Plant. No changes has been made.";
					$errCount['has-error'] = "YES";
				} else {
					$plantID = $x;
				}
			} else {
				$errCount['msg'] = "Invalid Plant. No changes has been made.";
				$errCount['has-error'] = "YES";
			}
			
			if(count($errCount) > 0) {
				echo json_encode($errCount);
			} else {
				
				$sqlError = 0;
				$imageName = "";
				
				$getImage = $conn->prepare("SELECT plant_image FROM hexel_plant WHERE plant_id = ?;");
				$getImage->bind_param('i', $plantID);
				$getImage->execute();
				$imageRslt = $getImage->get_result();
				if($imageRslt->num_rows > 0) {
					while($row = $imageRslt->fetch_assoc()) {
						$imageName = '../style/img/' . $row['plant_image'];
					}
				}
				$getImage->close();
				
				// Delete image
				if(file_exists($imageName))
					unlink($imageName);
				
				
				$deleteQuery = $conn->prepare("DELETE FROM hexel_plant WHERE plant_id = ?;");
				$deleteQuery->bind_param('i', $plantID);
				if(!$deleteQuery->execute()) {
					$sqlError += 1;
				}
				$deleteQuery->close();
				
				$deleteDesc = $conn->prepare("DELETE FROM hexel_plant_desc WHERE desc_plantid = ?;");
				$deleteDesc->bind_param('i', $plantID);
				if(!$deleteDesc->execute()) {
					$sqlError += 1;
				}
				$deleteDesc->close();
				
				// Delete Gallery
				$delGalleryFile = $conn->prepare("SELECT * FROM hexel_plant_gallery WHERE plant_id = ?;");
				$delGalleryFile->bind_param('i', $plantID);
				$delGalleryFile->execute();
				$delGalleryFileRslt = $delGalleryFile->get_result();
				if($delGalleryFileRslt->num_rows > 0) {
					while($row = $delGalleryFileRslt->fetch_assoc()) {
						$galleryFile = '../style/img/' . $row['image_name'];
						if(file_exists($galleryFile))
							unlink($galleryFile);
					}
				}
				$delGalleryFile->close();
				
				$delGallery = $conn->prepare("DELETE FROM hexel_plant_gallery WHERE plant_id = ?;");
				$delGallery->bind_param('i', $plantID);
				if(!$delGallery->execute())
					$sqlError += 1;
				$delGallery->close();
				
				$conn->close();
				
				if($sqlError > 0)
					$errCount['has-error'] = "YES";
				else
					$errCount['has-error'] = "NO";
				
				echo json_encode($errCount);
			}
			
		}
		
		
		if(cleanString($_POST['edit-mode']) == 'edit') {
			
			$desc = $care = $from = $to = $comment = $plantID = "";
			
			if(isset($_POST['edit-plant-desc'])) {
				$x = cleanString($_POST['edit-plant-desc']);
				if($x == "") {
					$errCount['edit-plant-desc'] = "Field is required!";
				}	elseif(strlen($x) < 10) {
					$errCount['edit-plant-desc'] = "This field is too short (minimum of 10 characters required)!";
				} elseif(strlen($x) > 1000) {
					$errCount['edit-plant-desc'] = "This field is too long (maximum of 1000 characters required)!";
				} else {
					$desc = $x;
				}
			}
			if(isset($_POST['edit-plant-care'])) {
				$x = cleanString($_POST['edit-plant-care']);
				if($x == "") {
					$errCount['edit-plant-care'] = "Field is required!";
				}	elseif(strlen($x) < 10) {
					$errCount['edit-plant-care'] = "This field is too short (minimum of 10 characters required)!";
				} elseif(strlen($x) > 1000) {
					$errCount['edit-plant-care'] = "This field is too long (maximum of 1000 characters required)!";
				} else {
					$care = $x;
				}
			}
			if(isset($_POST['edit-plant-price-from'])) {
				$x = cleanString($_POST['edit-plant-price-from']);
				if($x == "" || checkPrice($x) == 0) {
					$errCount['edit-plant-price-from'] = "This field is required or must be a valid decimal number!";
				}  else {
					if(floatval($x) <= 0 || intval($x) <= 0) {
						$errCount['edit-plant-price-from'] = "Amount must not be equal to 0!";
					} else {
						if(is_int($x)) {
							$x = number_format($x, 2, '.', '');
						}
						$from = floatval($x);
					}
				}
			}
			if(isset($_POST['edit-plant-price-to'])) {
				$x = cleanString($_POST['edit-plant-price-to']);
				if($x == "" || checkPrice($x) == 0) {
					$errCount['edit-plant-price-to'] = "This field is required or must be a valid decimal number!";
				}  else {
					if(floatval($x) <= 0 || intval($x) <= 0) {
						$errCount['edit-plant-price-to'] = "Amount must not be equal to 0!";
					} else {
						if(is_int($x)) {
							$x = number_format($x, 2, '.', '');
						}
						$to = floatval($x);
					}
				}
			}
			if(isset($_POST['edit-plant-price-comment'])) {
				$x = cleanString($_POST['edit-plant-price-comment']);
				if($x == "") {
					$x = "NULL";
				}
				$comment = $x;
			}
			if(isset($_POST['edit-plant-id'])) {
				$x = cleanString($_POST['edit-plant-id']);
				if($x == "") {
					$errCount['edit-plant-id'] = "Unexpected error. Unable to find plant";
				} else {
					$plantID = $x;
				}
			}
			
			
			if(count($errCount) > 0) {
				$errCount['has-error'] = "YES";
				echo json_encode($errCount);
			} else {
				if($from > $to) {
					$errCount['edit-plant-price-from'] = "Amount must greater than or equal to the maximum price range!";
					$errCount['has-error'] = "YES";
					echo json_encode($errCount);
				} else {
					
					//$editPlantSQL = "INSERT INTO hexel_plant_desc VALUES (NULL, '$plantID', '$desc', '$care', '$from', '$to', '$comment', '1', CURRENT_TIMESTAMP);";
					$editPlantSQL = $conn->prepare("INSERT INTO hexel_plant_desc VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);");
					$editPlantSQL->bind_param('issddsi', $plantID, $desc, $care, $from, $to, $comment, $bool);
					$plantID = intval($plantID);
					$bool = 1;
					if(!$editPlantSQL->execute()) {
						$errCount['has-error'] = "YES";
						$errCount['msg'] = "Unable to edit plant. Error: " . $editPlantSQL->error;
					} else {
						$errCount['has-error'] = "NO";
						$errCount['msg'] = "Successfully made changes.";
					}
					
					$editPlantSQL->close();
					$conn->close();
					echo json_encode($errCount);
				}
			}
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