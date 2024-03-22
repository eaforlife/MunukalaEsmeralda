<?php

	require_once('../script/dbConn.php');
	
	$errorCount = array();
	$validatedFields = array();
	
	if(isset($_POST['edit-method'])) {
		
		if(cleanString($_POST['edit-method']) == 'add') {
			$plantID = 0;
			// add photo
			
			if(isset($_POST['edit-plant-id'])) {
				if($_POST['edit-plant-id'] != '') {
					$plantID = cleanString($_POST['edit-plant-id']);
				} else {
					$errorCount['has-error'] = 'YES';
				}
			}
			
			if(isset($_FILES['edit-plant-gallery'])) {
				foreach($_FILES['edit-plant-gallery']['tmp_name'] as $imgFile => $name) {
			
					if(!empty($_FILES['edit-plant-gallery']['tmp_name'][$imgFile])) {
					
						$uploadedExtention = pathinfo(strtolower($_FILES['edit-plant-gallery']['name'][$imgFile]), PATHINFO_EXTENSION);
						$x = $_FILES["edit-plant-gallery"]["tmp_name"][$imgFile];
						$checkifImage = getimagesize($_FILES["edit-plant-gallery"]["tmp_name"][$imgFile]);
						
						if($checkifImage === false) {
							$errorCount['edit-plant-gallery'] = "One or more files is not an image!"; // check if it's an actual image
						} elseif($uploadedExtention != 'jpg' && $uploadedExtention != 'jpeg' && $uploadedExtention != 'png') {
							$errorCount['edit-plant-gallery'] = "Unsupported image format!";
						} elseif($_FILES["edit-plant-gallery"]["size"][$imgFile] > 6000000) {
							$errorCount['edit-plant-gallery'] = "One or more files is too big! Make sure image is smaller than 5mb!";
						} else {
							$validatedFields['edit-plant-gallery'][$imgFile] = $x;
							$validatedFields['edit-plant-gallery-ext'][$imgFile] = $uploadedExtention;
						}
					} else {
						$errorCount['edit-plant-gallery'] = "Nothing was uploaded";
					}
					
				}
			}
			
			if(count($errorCount) > 0) {
				$errorCount['has-error'] = 'YES';
				echo json_encode($errorCount);
			} else {
				// Add Gallery
				$currTimeStamp = new DateTime();
				$dtString = $currTimeStamp->format('YmdGis');
				foreach($validatedFields['edit-plant-gallery'] as $ix => $val) {
					
					$newGalleryImgName = $dtString . '-' . $plantID . '-gallery-' . $ix . '.' . $validatedFields['edit-plant-gallery-ext'][$ix];
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
					move_uploaded_file($validatedFields['edit-plant-gallery'][$ix], '../style/img/' . $cleanGalleryImg);
				}
				
				// For PWA entry
				$pwaGalleryTag = "";
				$pwaGalleryFetch = $conn->prepare("SELECT * FROM hexel_plant_gallery WHERE plant_id = ?;");
				$pwaGalleryFetch->bind_param('i', $plantID);
				$pwaGalleryFetch->execute();
				$pwaGalleryQuery = $pwaGalleryFetch->get_result();
				
				if($pwaGalleryQuery->num_rows > 0) {
					while($row = $pwaGalleryQuery->fetch_assoc()) {
						$pwaGalleryTag = $row['image_name'] . "~" . $pwaGalleryTag;
					}
				} else {
					$pwaGalleryTag = "false";
				}
				$pwaGalleryQuery->close();
				$conn->close();
				$errorCount['has-gallery'] = $pwaGalleryTag;
				echo json_encode($errorCount);
				
			}
			
		}
		
		if(cleanString($_POST['edit-method']) == 'delete') {
			
			$plantID = 0;
			
			if(isset($_POST['edit-plant-id'])) {
				if(cleanString($_POST['edit-plant-id']) == '') {
					$errorCount['has-error-message'] = "Unrecognized Plant.";
				} else {
					$plantID = cleanString($_POST['edit-plant-id']);
				}
			} else {
				$errorCount['has-error-message'] = "Unrecognized Plant.";
			}
			
			if(count($errorCount) > 0) {
				$errorCount['has-error'] = 'YES';
				echo json_encode($errorCount);
			} else {
				$validatedFields = $_POST;
				$deleteArr = array();
				foreach ($validatedFields as $key => $val) {
					if(is_numeric($val) && $validatedFields[$key] !== $plantID) {
						// Since we don't know which images are being selected for deletion we do seperate POST values.
						$deleteArr[] = $val;
					}
				}
				
				if(count($deleteArr) > 0) {
					$errCtr = 0;
					foreach($deleteArr as $key => $val) {
						
						$fileName = '';
						
						$getFileSQL = $conn->prepare("SELECT * FROM hexel_plant_gallery WHERE gallery_id = ?;");
						$getFileSQL->bind_param('i', $gID);
						$gID = $deleteArr[$key];
						$getFileSQL->execute();
						$getFileQuery = $getFileSQL->get_result();
						
						if($getFileQuery->num_rows > 0) {
							while($row = $getFileQuery->fetch_assoc()) {
								$fileName = $row['image_name'];
							}
						}
						$getFileSQL->close();
						
						$deleteFileSQL = $conn->prepare("DELETE FROM hexel_plant_gallery WHERE gallery_id = ?;");
						$deleteFileSQL->bind_param('i', $gID);
						$gID = $deleteArr[$key];
						if($deleteFileSQL->execute()) {
							
							$filePath = '../style/img/' . $fileName;
							if(file_exists($filePath))
								unlink($filePath);
							
						} else {
							$errCtr += 1;
						}
						$deleteFileSQL->close();
					}
					$conn->close();
					if($errCtr > 0) {
						$errorCount['has-error'] = 'YES';
						$errorCount['has-error-message'] = 'Unable to delete photo(s) from database. Try again later.';
					} else {
						$errorCount['has-error'] = 'NO';
					}
					echo json_encode($errorCount);
					
				} else {
					// Nothing was really deleted.
					$errorCount['has-error'] = 'YES';
					$errorCount['has-error-message'] = 'Nothing was deleted.';
					echo json_encode($errorCount);
				}
				
			}
		}
		
	}
	
	
	function cleanString($x) {
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		$x = trim($x);
		return $x;
	}

?>