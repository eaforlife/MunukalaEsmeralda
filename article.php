<?php
	session_start(); // important since we have user login
	$userID = 0;
	$isAndroid = 0;
	if(isset($_SESSION['uID'])) {
		$userID = $_SESSION['uID'];
	} else {
		$userID = 0;
	}
	if(isset($_SESSION['app'])) {
		if(cleanString($_SESSION['app']) == 'webview') {
			$isAndroid = 1;
		}
	}
	
	$plantID=0;
	if(isset($_GET['pid'])) {
		$plantID = stripChars($_GET['pid']);
	}
	
	require_once("./script/dbConn.php");
	date_default_timezone_set('Asia/Manila'); // We set timezone since mysql defaults is UTC 0. To change this, refer to: https://www.php.net/manual/en/timezones.php
	
	$plantimg = $plantname = $plantPostedDate = $plantUpdatedDate = $plantIsUpdated = $plantPoster = $plantPosterID = $plantDesc = $plantCare = $plantPriceFrom = $plantPriceTo = $plantPriceComment = "";
	$plantgallery = array();
	$plantgalleryid = array();
	$pwaGallery = "";
	
	$plantSQL = $conn->prepare("SELECT * FROM hexel_plant WHERE plant_id = ?;");
	$plantSQL->bind_param('i',$plantID);
	$plantSQL->execute();
	
	$plantQuery = $plantSQL->get_result();
	
	if($plantQuery->num_rows > 0) {
		while($row = $plantQuery->fetch_assoc()) {
			$plantimg = "./style/img/" . $row['plant_image'];
			$plantname = $row['plant_name'];
			$plantPostedDate = date_create($row['plant_published']); // refer to comment below
			// change date format https://www.php.net/manual/en/datetime.format.php to readable date time.
			$plantPostedDate = date_format($plantPostedDate, 'D, jS M Y g:i a'); // Monday, 1st Jan 2020 12:01 am
			$plantPosterID = $row['plant_uid'];
		}
	} else {
		$plantname = "Plant Not Found";
	}
	$plantSQL->close();
	 
	$plantDescSQL = $conn->prepare("SELECT * FROM hexel_plant_desc WHERE desc_plantid = ? ORDER BY desc_published DESC LIMIT 1;");
	$plantDescSQL->bind_param('i',$plantID);
	$plantDescSQL->execute();
	$plantDescQuery = $plantDescSQL->get_result();
	
	if($plantDescQuery->num_rows > 0) {
		while($row = $plantDescQuery->fetch_assoc()) {
			$plantDesc = $row['desc_plant'];
			$plantCare = $row['desc_guide'];
			$plantPriceFrom = $row['desc_price_min'];
			$plantPriceTo = $row['desc_price_max'];
			$plantPriceComment = $row['desc_price_comment'];
			$plantUpdatedDate = date_create($row['desc_published']); // refer to comment above
			// refer to comment above regarding date time format
			$plantUpdatedDate = date_format($plantUpdatedDate, 'jS M Y'); // 1st Jan 2020
			$plantIsUpdated = $row['desc_updated'];
		}
	}
	$plantDescSQL->close();
	
	$plantGallerySQL = $conn->prepare("SELECT * FROM hexel_plant_gallery WHERE plant_id = ?;");
	$plantGallerySQL->bind_param('i',$plantID);
	$plantGallerySQL->execute();
	$plantGalleryQuery = $plantGallerySQL->get_result();
	
	if($plantGalleryQuery->num_rows > 0) {
		while($row = $plantGalleryQuery->fetch_assoc()) {
			$pwaGallery = $row['image_name'] . "~" . $pwaGallery;
			$plantgallery[] = $row['image_name'];
			$plantgalleryid[] = $row['gallery_id'];
		}
	}
	$plantGalleryQuery->close();
	
	
	$getUserSQL = "SELECT * FROM hexel_acct WHERE acct_id='$plantPosterID';";
	$getUserQuery = $conn->query($getUserSQL);
	if($getUserQuery->num_rows > 0) {
		while($row = $getUserQuery->fetch_assoc()) {
			$plantPoster = $row['acct_lname'] . ' ' . $row['acct_fname'];
		}
	}
	$conn->close();
	
	
	function stripChars($x) {
		$x = trim($x);
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		return $x;
	}
?>
<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./style/bootstrap.min.css"><!-- Bootstrap -->
	<link rel="stylesheet" href="./style/hexel.all.min.css"><!-- CSS -->
	<link href='https://fonts.googleapis.com/css?family=Barlow Semi Condensed' rel='stylesheet'><!-- Font Face -->
	<link rel="manifest" href="./manifest.json">
	<meta name="theme-color" content="#EAFAF1"/>
	<!-- iOS Support -->
	<link rel="apple-touch-icon" href="./style/icons/icon-144x144.png">
	<meta name="apple-mobile-web-app-status-bar" content="#EAFAF1">
	<?php if($userID != 0): ?><meta http-equiv="refresh" content="1800; url=./script/logout.php?inactive=1"><?php endif; ?>
	<title><?php echo strtoupper($plantname . ' - plant database'); ?></title>
	<style>
		body {
			position: relative;
		}
		.parallax-article-bg {
			background-attachment: fixed;
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
			position: relative;
		}
		.parallax-article-bg {
			background-image: url('<?php echo $plantimg; ?>');
			background-color: #273746;
			min-height: 100vh;
		}
		.title-text {
			font-family: 'Barlow Semi Condensed' !important;
			background-color: #212F3D;
		}
		#title {
			position: absolute;
			left: 0;
			top: 40%;
			padding: 0;
			margin: 0;
		}
		
		.carousel-control-prev, .carousel-control-next, .carousel-indicators {
			filter: invert(100%);
		}
		
		/* Turn off parallax scrolling for tablets and phones */
		@media only screen and (max-device-width: 1300px) {
			.parallax-article-bg {
				background-attachment: scroll;
				min-height: 300px;
			}
			#title {
				top: 45%;
			}
		}
	</style>
	</head>
	
	<body data-spy="scroll" data-target="#hexel-scrollspy"<?php if($isAndroid == 1): ?> data-webview='yes'<?php endif; ?>>
		
		<div id="top-of-page"></div>
		<!-- Navigation -->
		<div class="container-fluid px-0" id="navbar-top">
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="hexel-scrollspy">
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#hexelnavbar" aria-controls="hexelnavbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="hexelnavbar">
					<a class="navbar-brand" href="#top-of-page">
						<img src="./style/img/hexel-logo.svg" class="d-inline-block align-top" width="30" alt="HEXEL" loading="lazy">
						Munukala Esmeralda
					</a>
					<ul class="navbar-nav mr-auto mt-2 mt-lg-0 text-capitalize" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="./index.php#plant-database">
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-bar-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M12.5 15a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5zM10 8a.5.5 0 0 1-.5.5H3.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L3.707 7.5H9.5a.5.5 0 0 1 .5.5z"/>
								</svg>
								Return
							</a>
						</li>
						<li class="nav-item active" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#top-of-page"><?php echo $plantname; ?> <span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#description">Description</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#care">Care Guide</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#price">Price Watch</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#gallery">Gallery</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show" id="installapp">
							<a class="nav-link" href="./pwa_index.html" id="btnInstallApp">Install App</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<?php if($userID == 0): ?><a class="nav-link" href="#" data-toggle="modal" data-target="#loginhtml">Login</a>
							<?php else: ?><a class="nav-link" href="#" data-toggle="modal" data-target="#logouthtml">Logout</a>
							<?php endif; ?>
						</li>
					</ul>
				</div>
			</nav>
		</div>
		
		<div class="parallax-article-bg">
			<div class="container-fluid" id="title">
				<div class="row">
					<div class="col-sm-auto mx-auto text-center text-white">
						<h1 class="text-capitalize py-2 px-5 title-text rounded shadow mb-3" id="plant-name-title"><?php echo $plantname; ?></h1>
						
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid rounded-top py-5">
			<div class="row">
				<div class="container mb-3">
					<div class="row mb-5">
						<div id="description"><!-- anchor bookmark --></div>
						<div class="col-sm-8 mx-auto">
							<p class="p-0 m-0 text-monospace font-weight-light"><small>Posted: <?php echo $plantPostedDate; ?> <span id="updated-post" class="font-italic"><?php if($plantIsUpdated=='1') echo "(Updated: " . $plantUpdatedDate . ")"; ?></span>. Published by: <span class="text-capitalize"><?php echo $plantPoster; ?></span></small></p>
						</div>
						<?php if($userID != 0): ?>
						<div class="col-sm-auto mx-auto">
							<div class="ml-auto text-sm-right">
								<div class="btn-group p-0 shadow mb-3 rounded" role="group" aria-label="admintools">
									<button type="button" class="btn btn-link btn-sm text-info" id="edit-plant-btn" data-toggle="modal" data-target="#edit-plant-modal">
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
											<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
										</svg>
										Edit Plant
									</button>
									<button type="button" class="btn btn-link btn-sm text-danger" data-toggle="modal" data-target="#delete-plant-modal">
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
										</svg>&nbsp;
										Delete Plant
									</button>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					
					<div class="row mb-3">
						<div class="col-sm-auto mx-auto text-center">
							<h3>
							<span class="text-success">
							<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
							<path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
							<path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
							<circle cx="8" cy="4.5" r="1"/>
							</svg>
							</span>
							Description</h3>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-sm-8 mx-auto" id="plant-description">
							<p class="lead"><?php echo html_entity_decode($plantDesc); ?></p>
						</div>
					</div>
					<div id="care"><!-- anchor bookmark --></div>
					<hr class="w-75 my-5">
					
					<div class="row mb-3">
						<div class="col-sm-auto mx-auto text-center">
							<h3>
							<span class="text-success">
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-droplet-half" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M7.21.8C7.69.295 8 0 8 0c.109.363.234.708.371 1.038.812 1.946 2.073 3.35 3.197 4.6C12.878 7.096 14 8.345 14 10a6 6 0 0 1-12 0C2 6.668 5.58 2.517 7.21.8zm.413 1.021A31.25 31.25 0 0 0 5.794 3.99c-.726.95-1.436 2.008-1.96 3.07C3.304 8.133 3 9.138 3 10c0 0 2.5 1.5 5 .5s5-.5 5-.5c0-1.201-.796-2.157-2.181-3.7l-.03-.032C9.75 5.11 8.5 3.72 7.623 1.82z"/>
								<path fill-rule="evenodd" d="M4.553 7.776c.82-1.641 1.717-2.753 2.093-3.13l.708.708c-.29.29-1.128 1.311-1.907 2.87l-.894-.448z"/>
								</svg>
							</span>
							Care Guide</h3>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-sm-8 mx-auto" id="plant-care">
							<span><?php echo html_entity_decode($plantCare); ?></span>
						</div>
					</div>
					
					<hr class="w-75 my-5">
					<div id="price"><!-- anchor bookmark --></div>
					<div class="row mb-3">
						<div class="col-sm-auto mx-auto text-center">
							<h3>
							<span class="text-success">
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-shop-window" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zm2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5z"/>
								</svg>
							</span>
							Price Watch</h3>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-sm-8 mx-auto">
							<p>Price: PHP <span id="plant-price-from"><?php echo $plantPriceFrom; ?></span> to <span id="plant-price-to"><?php echo $plantPriceTo; ?></span></p>
							<small class="text-muted" id="plant-comment"><?php if($plantPriceComment != 'NULL') echo $plantPriceComment; ?></small>
						</div>
					</div>
					
					<hr class="w-75 my-5">
					<div id="gallery"><!-- anchor bookmark --></div>
					<div class="row mb-3">
						<div class="col-sm-auto mx-auto text-center">
							<h3>
							<span class="text-success">
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-images" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M12.002 4h-10a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1zm-10-1a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-10zm4 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
								<path fill-rule="evenodd" d="M4 2h10a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1v1a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2h1a1 1 0 0 1 1-1z"/>
								</svg>
							</span>
							Gallery</h3>
						</div>
					</div>
					<?php if($userID != 0): ?>
					<div class="row mb-3">
						<div class="col-sm-6">&nbsp;</div>
						<div class="col-sm-4 mx-auto">
							<span class="text-dark mr-2">
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>
								<path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>
								</svg>
							</span>
							<button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#add-gallery-modal">
							<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
							</svg>
							Add
							</button>
							<?php if(!empty($plantgallery)): ?>
							<button type="reset" class="btn btn-outline-info btn-sm" id="gallery-delete-reset">
							<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-counterclockwise" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
							<path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
							</svg>
							Reset</button>
							<button type="button" class="btn btn-outline-danger btn-sm" id="gallery-delete-button">
							<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
							<path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
							</svg>
							Delete</button>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
					
					<div class="row mb-3">
						<div class="col-sm-8 mx-auto">
							<?php if(!empty($plantgallery)): ?>
								<?php if($userID != 0): ?>
									<form method="post" id="gallery-delete-form">
										<div class="form-row">
									<?php foreach($plantgallery as $ix => $val): ?>
											<div class="form-group col-sm-4">
												<div class="form-check form-check-inline">
												<input class="form-check-input" id="chk-<?php echo $ix; ?>" type="checkbox" data-pwa="<?php echo $plantgallery[$ix]; ?>" name="del-<?php echo $plantgalleryid[$ix]; ?>" value="<?php echo $plantgalleryid[$ix]; ?>">
												<label class="form-check-label" for="chk-<?php echo $ix; ?>">
												<a href="#" onclick="showSlide(<?php echo $ix; ?>)">
													<img src="<?php echo './style/img/' . $plantgallery[$ix]; ?>" alt="img-1" height="200" class="img-thumbnail mb-1" loading="lazy">
												</a>
												</label>
												</div>
											</div>
									<?php endforeach; ?>
										</div>
										<div class="form-group col-sm-4">
										<input type="hidden" name="edit-plant-id" value="<?php echo $plantID; ?>">
										<input type="hidden" name="edit-pwa-id" id="edit-pwa-id" value="<?php 
											if(isset($_GET['id'])) {
												echo stripChars($_GET['id']);
											}
										?>">
										<input type="hidden" name="edit-pwa-tag" id="edit-pwa-tag" value="<?php echo $pwaGallery; ?>">
										<input type="hidden" name="edit-method" value="delete">
										</div>
									</form>
								<?php else: ?>
									<div class="row">
									<?php foreach($plantgallery as $ix => $val): ?>
										<div class="col-sm-4">
											<a href="#" onclick="showSlide(<?php echo $ix; ?>)">
												<img src="<?php echo './style/img/' . $plantgallery[$ix]; ?>" alt="img-1" height="200" class="img-thumbnail mb-1" loading="lazy">
											</a>
										</div>
									<?php endforeach; ?>
									</div>
								<?php endif; ?>
							<?php else: ?>
							<p class="text-muted">Gallery seems to be empty for this plant.</p>
							<?php endif; ?>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<div class="container-fluid" id="footer">
			<div class="row">
				<div class="col-sm-6 mx-auto mt-5 d-flex justify-content-center align-content-center">
					<div class="row my-3">
						<div class="container">
							<a href="#top-of-page" class="btn btn-outline-light" role="button">
							<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-bar-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M8 10a.5.5 0 0 0 .5-.5V3.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 3.707V9.5a.5.5 0 0 0 .5.5zm-7 2.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13a.5.5 0 0 1-.5-.5z"/>
							</svg>
							&nbsp;Back To Top
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 text-center mx-auto text-light">
					<p><small>All Rights Reserved &copy; 2020 MUNUKALA ESMERALDA</small></p>
				</div>
			</div>
		</div>
		<!-- End of Body -->
		
		<?php if($userID != 0): ?>
		<!-- Logout Page -->
		<div class="modal fade" id="logouthtml" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="logouthtmllabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="logouthtmllabel">NOTICE</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<span id="logout-modal-msg">Are you sure you want to log out?</span>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-outline-danger" id="btn-logouthtml">Logout</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Delete Page -->
		<div class="modal fade" id="delete-plant-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteplantmodal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="deleteplantmodal">Confirm Delete</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="delete-modal-body">
						Are you sure you want to delete <strong><span id="plant-name-for-modal"></span></strong>?
						<form method="post" class="invisible" id="delete-plant-form">
							<input type="hidden" name="edit-mode" value="delete">
							<?php
								if(isset($_GET['id'])) {
									// if PWA data ID is set we add it as well
									echo "<input type='hidden' name='plant-pwa' id='plant-pwa' value='" . stripChars($_GET['id']) . "'>\n";
								}
							?>
							<input type="hidden" name="plant-id" value="<?php echo $plantID; ?>">
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" id="btn-delete-close" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger" id="btn-delete-plant">Delete</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Edit Page -->
		<div class="modal fade" id="edit-plant-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editplantmodal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editplantmodal">Edit Plant</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="modal-edit-body">
						
						<form id="form-edit-plant" method="post">
							<fieldset>
								<div class="form-group">
									<label for="current-plant-name">Plant Name: </label>
									<input type="text" class="form-control" id="current-plant-name" placeholder="Plant Name" disabled>
								</div>
								<div class="form-group">
									<label for="edit-plant-desc" aria-describedby="edit-plant-desc-help">Plant Description: </label>
									<small id="edit-plant-desc-help" class="form-text text-muted m-0 p-0">&nbsp;
									<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
										<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
									</svg>
									Tip: You may use html tag to format text. Example for bullets you use &nbsp;<samp>&lt;ul&gt;&lt;li&gt;Text 1 Here&lt;/li&gt;&lt;li&gt;Text 2 Here&lt;/li&gt;&lt;/ul&gt;</samp>
									</small>
									<textarea class="form-control" id="edit-plant-desc" name="edit-plant-desc" maxlength="1000" required></textarea>
									<div id="edit-plant-desc-err"></div>
								</div>
								<div class="form-group">
									<label for="edit-plant-care" aria-describedby="edit-plant-care-help">Plant Care Guide: </label>
									<small id="edit-plant-care-help" class="form-text text-muted m-0 p-0">&nbsp;
									<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
										<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
									</svg>
									Tip: You may use html tag to format text. Example for bullets you use &nbsp;<samp>&lt;ul&gt;&lt;li&gt;Text 1 Here&lt;/li&gt;&lt;li&gt;Text 2 Here&lt;/li&gt;&lt;/ul&gt;</samp>
									</small>
									<textarea class="form-control" id="edit-plant-care" name="edit-plant-care" maxlength="1000" required></textarea>
									<div id="edit-plant-care-err"></div>
								</div>
								<div class="form-row">
									<div class="col">
										<label for="edit-plant-price-from">Starting Price: </label>
										<input type="text" pattern="^\d*\.?[0-9]{1,2}$" class="form-control" id="edit-plant-price-from" name="edit-plant-price-from" placeholder="00.00" required>
										<div id="edit-plant-price-from-err"></div>
									</div>
									<div class="col">
										<label for="edit-plant-price-to">Maximum Price: </label>
										<input type="text" pattern="^\d*\.?[0-9]{1,2}$" class="form-control" id="edit-plant-price-to" name="edit-plant-price-to" placeholder="00.00" required>
										<div id="edit-plant-price-to-err"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="edit-plant-price-comment">Additional Comment For Pricing: </label>
									<input type="text" class="form-control" id="edit-plant-price-comment" name="edit-plant-price-comment" placeholder="Comment for price">
								</div>
								<input type="hidden" id="edit-plant-id" name="edit-plant-id" value="<?php echo $plantID; ?>">
								<?php
									if(isset($_GET['id'])) {
										// if PWA data ID is set we add it as well
										echo "<input type='hidden' name='edit-pwa-id' id='edit-pwa-id' value='" . stripChars($_GET['id']) . "'>\n";
									}
								?>
								<input type="hidden" id="edit-mode" name="edit-mode" value="edit">
							</fieldset>
							<button type="button" class="btn btn-outline-secondary" id="btn-cancel-edit" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-outline-primary" id="btn-reset-edit">Reset</button>
							<button type="button" class="btn btn-outline-info" id="btn-submit-edit">Edit Plant</button>
						</form>
						
					</div>
				</div>
			</div>
		</div>
		
		<!-- Add more photo in slides modal -->
		<div class="modal fade" id="add-gallery-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addgallerymodal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="addgallerymodal">Add To Gallery</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="add-gallery-modal-body">
						<form id="add-gallery-form" method="post">
							<fieldset>
								<input type="hidden" name="edit-plant-id" value="<?php echo $plantID; ?>">
								<input type="hidden" name="edit-method" value="add">
								<div class="form-group mb-3">
									<label for="edit-plant-gallery" aria-describedby="edit-plant-gallery-help">Plant Gallery:</label>
									<small id="edit-plant-gallery-help" class="form-text text-muted m-0 p-0">&nbsp;
									<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
										<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
									</svg>
									Supported format: jpeg and png. Maximum image size is 5MB!
									</small>
									<input type="file" class="form-control-file" id="edit-plant-gallery" name="edit-plant-gallery[]" multiple="multiple" accept=".jpg,.jpeg,.png" required>
									<?php
										if(isset($_GET['id'])) {
											// if PWA data ID is set we add it as well
											echo "<input type='hidden' name='edit-plant-pwa-id' id='edit-plant-pwa-id' value='" . stripChars($_GET['id']) . "'>\n";
										}
									?>
									<input type="hidden" name="edit-plant-pwa-id" id="edit-plant-pwa-id" value="<?php ?>">
									<div id="edit-plant-gallery-err"></div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" id="btn-add-gallery-close" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-success" id="btn-add-gallery-plant"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1h-2z"/>
  <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 1.707V10.5a.5.5 0 0 1-1 0V1.707L5.354 3.854a.5.5 0 1 1-.708-.708l3-3z"/>
</svg> Upload</button>
					</div>
				</div>
			</div>
		</div>
		
		<?php else: ?>
		
		<!-- Login Page -->
		<div class="modal fade" id="loginhtml" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="login-modal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title" id="login-modal">Login</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					</div>
					<div class="modal-body" id="login-form-body">						
						<form method="post" id="login-form">
							<fieldset>
								<div class="form-group">
									<label for="loginUsername">Username</label>
									<input type="text" class="form-control shadow-sm mb-2" id="loginUsername" name="loginUsername" placeholder="Username" required>
									<div id="loginUsername-err"></div>
								</div>
								<div class="form-group">
									<label for="loginPassword">Password</label>
									<input type="password" class="form-control shadow-sm mb-3" id="loginPassword" name="loginPassword" required>
									<div id="loginPassword-err"></div>
								</div>
								<button type="submit" class="btn btn-outline-success" id="login-submit-btn">Login</button>
								<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="modal fade" id="plantslide" tabindex="-1" aria-labelledby="plantslideLabel" aria-hidden="true">
			<div class="modal-dialog modal-xl">
				<div class="modal-content">
					<div class="modal-body">
						<div id="plantslidebookmark" class="carousel slide carousel-fade" data-ride="carousel">
							<ol class="carousel-indicators">
								<?php foreach($plantgallery as $ix => $val): ?>
									<?php if($ix == 0): ?>
										<li data-target="#plantslidebookmark" data-slide-to="<?php echo $ix; ?>" class="active"></li>
									<?php else: ?>
										<li data-target="#plantslidebookmark" data-slide-to="<?php echo $ix; ?>"></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ol>
							<div class="carousel-inner">
								<?php foreach($plantgallery as $ix => $val): ?>
									<?php if($ix == 0): ?>
										<div class="carousel-item active">
											<img src="<?php echo './style/img/' . $plantgallery[$ix]; ?>" class="d-block w-100" alt="<?php echo 'slide ' . $ix; ?>">
										</div>
									<?php else: ?>
										<div class="carousel-item">
											<img src="<?php echo './style/img/' . $plantgallery[$ix]; ?>" class="d-block w-100" alt="<?php echo 'slide ' . $ix; ?>">
										</div>
									<?php endif ?>
								<?php endforeach; ?>
							</div>
							<a class="carousel-control-prev" href="#plantslidebookmark" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
							</a>
							<a class="carousel-control-next" href="#plantslidebookmark" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- The core Firebase JS SDK is always required and must be listed first -->
		<script src="https://www.gstatic.com/firebasejs/8.1.1/firebase-app.js"></script>
		
		<!-- TODO: Add SDKs for Firebase products that you want to use
			 https://firebase.google.com/docs/web/setup#available-libraries -->
		<script src="https://www.gstatic.com/firebasejs/8.1.1/firebase-firestore.js"></script>

		<script>
		  // Your web app's Firebase configuration
		  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
		  var firebaseConfig = {
			apiKey: "AIzaSyAOoZFGHJq8CncRpK-WCr67MXujuqnAHNY",
			authDomain: "hexel-pwa.firebaseapp.com",
			databaseURL: "https://hexel-pwa.firebaseio.com",
			projectId: "hexel-pwa",
			storageBucket: "hexel-pwa.appspot.com",
			messagingSenderId: "502763909830",
			appId: "1:502763909830:web:95f46a21516dc530669dc7",
			measurementId: "G-MQ1MGXF9KH"
		  };
		  // Initialize Firebase
		  firebase.initializeApp(firebaseConfig);
		  const dbFirebase = firebase.firestore();
		</script>
		<script src="./script/hexel-app.js"><!-- PWA --></script>
		<script src="./script/jquery-3.5.1.min.js"><!-- JQuery --></script>
		<script src="./script/bootstrap.bundle.min.js"><!-- Bootstrap --></script>
		<script src="./script/hexel.all.script.js"><!-- Javascript Files --></script>
		<script src="./script/hexel.article.script.js"></script>
		<script>
			var appInstall = document.querySelector('#installapp');
			var btnInstall = document.querySelector('#btnInstallApp');
			
			let deferredPrompt;
			appInstall.style.display = "none";

			window.addEventListener('beforeinstallprompt', (e) => {
			  // Prevent the mini-infobar from appearing on mobile
			  e.preventDefault();
			  // Stash the event so it can be triggered later.
			  deferredPrompt = e;
			  // Update UI notify the user they can install the PWA
			  appInstall.style.display = "block";
			  //appInstall.classList.toggle('hidden', false);
			  
			  btnInstall.addEventListener('click', (evt) => {
					console.log('👍', 'butInstall-clicked');
					const promptEvent = window.deferredPrompt;
					if (!promptEvent) {
						// The deferred prompt isn't available.
						return;
					}
					// Show the install prompt.
					promptEvent.prompt();
					// Log the result
					promptEvent.userChoice.then((result) => {
						console.log('👍', 'userChoice', result);
						// Reset the deferred prompt variable, since
						// prompt() can only be called once.
						window.deferredPrompt = null;
						// Hide the install button.
						//appInstall.classList.toggle('hidden', true);
						appInstall.style.display = "none";
					});
				});
			  
			});
		
			$('.carousel').carousel({
				interval: false,
				touch: true
			});
			
			$(window).scroll(function() {
				if($('body').is('[data-webview]')) {
					$('#navbar-top').addClass('fixed-top');
				} else {
					//$('#navbar-top').toggleClass('container-fluid container', $(window).scrollTop() > 1);
					$('#navbar-top').toggleClass('container fixed-top mt-2', $(window).scrollTop() > 100);
					$('#navbar-top nav').toggleClass('navbar-light bg-light rounded shadow py-1', $(window).scrollTop() > 100);
					$('#navbar-top nav').toggleClass('navbar-dark bg-dark', $(window).scrollTop() <= 100);
					$('#navbar-top').toggleClass('container-fluid', $(window).scrollTop() <= 100);
					//$('nav').toggleClass('navbar-light', $(window).scrollTop() > 1);
					/*$('#navbar-top nav').removeClass('navbar-dark', $(window).scrollTop() > 1);
					$('#navbar-top nav').addClass('navbar-light', $(window).scrollTop() > 1);
					$('#navbar-top nav').removeClass('navbar-light', $(window).scrollTop() < 1);
					$('#navbar-top nav').addClass('navbar-dark', $(window).scrollTop() < 1);
					$('#navbar-top nav').toggleClass('bg-light', $(window).scrollTop() > 1);*/
				}
				
			});
			
			function showSlide(ix) {
				console.log('show me');
				$('.carousel').carousel(ix);
				$('#plantslide').modal('toggle');
			}
		</script>
	</body>
</html>