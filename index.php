<?php
	session_start(); // important since we have user login
	$showsplash = 1;
	
	$userID = 0;
	$isAndroid = 0;
	$isOffline = 0;
	if(isset($_SESSION['uID'])) {
		$userID = $_SESSION['uID'];
	} else {
		$userID = 0;
	}
	
	if(isset($_GET['ref'])) {
		if(cleanString($_GET['ref']) == 'nosplash') {
			$showsplash = 0;
		}
	}
	if(isset($_GET['offline'])) {
		if(cleanString($_GET['offline']) == 'offline') {
			$isOffline = 1;
		} else {
			$isOffline = 0;
		}
	}
	/*
	if(isset($_SESSION['app'])) {
		if(cleanString($_SESSION['app']) == 'webview') {
			$isAndroid = 1;
		}
	}
	
	if(isset($_GET['app'])) {
		if(cleanString($_GET['app']) == 'webview') {
			$_SESSION['app'] = 'webview';
			$isAndroid = 1;
		}
	}*/
	
	function cleanString($x) {
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		$x = trim($x);
		return $x;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="./style/bootstrap.min.css"><!-- Bootstrap -->
		<link rel="stylesheet" href="./style/hexel.all.min.css"><!-- CSS -->
		<link rel="stylesheet" href="./style/hexel.index.css"><!-- CSS -->
		<link href='https://fonts.googleapis.com/css?family=Barlow Semi Condensed' rel='stylesheet'><!-- Font Face -->
		<link rel="manifest" href="./manifest.json">
		<meta name="theme-color" content="#EAFAF1"/>
		<!-- iOS Support -->
		<link rel="apple-touch-icon" href="./style/icons/icon-144x144.png">
		<meta name="apple-mobile-web-app-status-bar" content="#EAFAF1">
		<?php if($userID != 0): ?><meta http-equiv="refresh" content="1800; url=./script/logout.php?inactive=1"><?php endif; ?>
		<title>HOME</title>
	</head>
	<body data-spy="scroll" data-target="#hexel-scrollspy"<?php if($showsplash == 0): ?> data-splash='no'<?php endif; ?><?php if($isAndroid == 1): ?> data-webview='yes'<?php endif; ?>>
			<!-- Navigation -->
			<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark" id="hexel-scrollspy">
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#hexelnavbar" aria-controls="hexelnavbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="hexelnavbar">
					<a class="navbar-brand" href="#">
					<img src="./style/img/hexel-logo.svg" class="d-inline-block align-top" width="30" alt="HEXEL" loading="lazy">
					Munukala Esmeralda
					</a>
					<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#top-of-page">Home</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="#about-us">About Us</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<a class="nav-link" href="./index.php#plant-database">Plant Database</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show" id="installapp">
							<a class="nav-link" href="./pwa_install.html" id="btnInstallApp">Install App</a>
						</li>
						<li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show">
							<?php if($userID == 0): ?><a class="nav-link" href="#" data-toggle="modal" data-target="#loginhtml">Login</a>
							<?php else: ?><a class="nav-link" href="#" data-toggle="modal" data-target="#logouthtml">Logout</a>
							<?php endif; ?>
						</li>
					</ul>
				</div>
			</nav>
			<div id="top-of-page"></div>
			<div class="toast fixed-bottom" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="toast-header">
				<strong class="mr-auto">System</strong>
				<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				</div>
				<div class="toast-body" id="plant-update-msg"></div>
			</div>
			
			<div class="parallax-bg-main">
				<div class="container-fluid" id="title">
					<div class="row">
						<div class="col-sm-auto mx-auto text-center text-white">
							<img src="./style/img/hexel-logo.svg" style="background-color:#1B2631" class="w-50 rounded-circle shadow p-2 mb-3" id="hexel-logo" loading="lazy">
							<h1 class="text-capitalize px-5 py-3 title-text rounded shadow p-2 mb-3">Munukala Esmeralda</h1>
							<div id="about-us"><!-- anchor bookmark --></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="container-fluid py-5 shadow" id="content">
				<div class="row mb-3">
					<div class="container mb-5">
						<div class="col-sm-auto mx-auto text-center">
							<h3>About Us</h3>
							<p class="lead">The purpose of this project is to provide the people residing in urban areas that are
	very congested, with the right knowledge, whether they know about it or not, about what
	is happening when they are exposed to indoor plants or when they do not have indoor
	plants in their homes. Equipping the citizens of Manila with information about indoor air
	purifying plants, the project aims to provide facts and real-life scenarios that can help
	them improve the quality of life they are experiencing with or without the indoor plants.
	Where trees cannot be planted anymore to provide green benefits like cool shade and
	reduced particulate matters, some indoor plants like the Snake Plant and Peace Lily
	achieves the same environmental positive outcomes.</p>
						</div>
					</div>
				</div>
				
				<div class="row mb-3">
					<div class="container">
						<div class="col-sm-6 mx-auto">
							<h3 class="text-center">Testimonials</h3>
						</div>
					</div>
				</div>
				<div class="row mb-5">
					<div class="container">
						<div class="row">
							<div class="col-sm-6">
								<div class="card">
									<div class="card-body">
										<blockquote class="blockquote mb-0">
											<p>Easy to use. Stylish.</p>
											<footer class="blockquote-footer">Anonymous</footer>
										</blockquote>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="card">
									<div class="card-body">
										<blockquote class="blockquote mb-0">
											<p>Well optimized or dynamic in my opinion. The app works both on my phone and pc.</p>
											<footer class="blockquote-footer">Anonymous</footer>
										</blockquote>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="card">
									<div class="card-body">
										<blockquote class="blockquote mb-0">
											<p>Very handy. Reminds me of the info that I need about the plants that I care about.</p>
											<footer class="blockquote-footer">Anonymous</footer>
										</blockquote>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
			
			<div class="parallax-bg-plant">
				<div class="container-fluid" id="title">
					<div class="row">
						<div class="col-sm-auto mx-auto text-center text-white">
							<h1 class="text-capitalize px-5 py-3 title-text rounded shadow p-2 mb-3">The Plant Database</h1>
						</div>
					</div>
				</div>
			</div>
			
			<div class="container-fluid mb-5">
				
				<div id="plant-database"><!-- anchor bookmark --></div>
				
				<div class="row">
					<div class="container">
						<div class="row">
							<hr class="w-75 my-3">
							<div class="col-sm-6 mx-auto mb-2">
								<h4 class="text-center">Explore Plants</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-8 mx-auto">
								<div class="input-group shadow bg-light mb-5">
									<form class="p-0 m-0 flex-fill" id="search-form">
										<div class="input-group">
											<?php if($userID != 0): ?>
											<div class="input-group-prepend">
												<a href="#" class="btn btn-info" id="add-plant" data-toggle="modal" data-target="#addplantmodal">
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
														<path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
													</svg>
													<small>Add Plant</small>
												</a>
											</div>
											<?php endif; ?>
											<input type="text" class="form-control" placeholder="Search Plant" id="search-plant" name="search-plant" aria-label="Search Plant" aria-describedby="search-plant">
											
											<label for="search-plant" class="sr-only">Search</label>
											<div class="input-group-append">
												<button class="btn btn-outline-success" type="submit" id="search-plant-submit">
													<svg id="search-plant" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
														<path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
													</svg>
													<small>Search</small>
												</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="row mb-5">
							<div class="col-sm-8 p-1 border mx-auto">
								<div class="list-group" id="plant-database-out">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="pwa-alert"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="container mb-5">
						<hr class="mb-5 w-75">
						<div class="col-sm-auto mx-auto">
							<div class="row justify-content-center">
								<h3 class="text-center">Contact Us</h3>
							</div>
							<div class="row my-3 justify-content-center">
								<div class="col-sm-6">
									<div class="embed-responsive embed-responsive-16by9">
										<iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1623.313293868794!2d120.98816359356913!3d14.604120629425832!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9f8b14eb259%3A0xad4d12caac9a068e!2sFEU%20Institute%20of%20Technology!5e0!3m2!1sen!2sph!4v1606116799010!5m2!1sen!2sph" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
									</div>
								</div>
								<div class="col-sm-4">
									<h5 class="mb-3">Far Eastern University Institute of Technology</h5>
									<p class="m-0">
									<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-envelope" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
									</svg>&nbsp;<strong>Mail Us:</strong>
									</p>
									<p class="m-0">C/O HEXEL<p class="m-0">P. Paredes St</p><p class="m-0">Sampaloc, Manila,</p><p class="m-0">1015 Metro Manila</p>
									<p class="my-2">
									<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chat-right-text" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M2 1h12a1 1 0 0 1 1 1v11.586l-2-2A2 2 0 0 0 11.586 11H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z"/>
									<path fill-rule="evenodd" d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
									</svg>&nbsp;
									<strong>E-Mail:</strong>&nbsp;<a href="mailto:studiohexel@gmail.com">studiohexel@gmail.com</a></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="container-fluid mx-auto text-center fixed-bottom p-1 m-0 invisible" id="offline-banner" style="background-color: #212F3D;">
				<p class="text-uppercase text-light"><strong><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-wifi-off" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path d="M10.706 3.294A12.545 12.545 0 0 0 8 3 12.44 12.44 0 0 0 .663 5.379a.485.485 0 0 0-.048.736.518.518 0 0 0 .668.05A11.448 11.448 0 0 1 8 4c.63 0 1.249.05 1.852.148l.854-.854zM8 6c-1.905 0-3.68.56-5.166 1.526a.48.48 0 0 0-.063.745.525.525 0 0 0 .652.065 8.448 8.448 0 0 1 3.51-1.27L8 6zm2.596 1.404l.785-.785c.63.24 1.228.545 1.785.907a.482.482 0 0 1 .063.745.525.525 0 0 1-.652.065 8.462 8.462 0 0 0-1.98-.932zM8 10l.934-.933a6.454 6.454 0 0 1 2.012.637c.285.145.326.524.1.75l-.015.015a.532.532 0 0 1-.611.09A5.478 5.478 0 0 0 8 10zm4.905-4.905l.747-.747c.59.3 1.153.645 1.685 1.03a.485.485 0 0 1 .048.737.518.518 0 0 1-.668.05 11.496 11.496 0 0 0-1.812-1.07zM9.02 11.78c.238.14.236.464.04.66l-.706.706a.5.5 0 0 1-.708 0l-.707-.707c-.195-.195-.197-.518.04-.66A1.99 1.99 0 0 1 8 11.5c.373 0 .722.102 1.02.28zm4.355-9.905a.53.53 0 1 1 .75.75l-10.75 10.75a.53.53 0 0 1-.75-.75l10.75-10.75z"/>
</svg> Offline Viewing Mode. Some Features May Be Unavailable.</strong></p>
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
							&nbsp;<small>Back To Top</small>
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
			
		<!-- Modal and Toast -->
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
		
		<!-- Add Plant -->
		<div class="modal fade" id="addplantmodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="login-modal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title" id="add-plant-modal">Add Plant</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					</div>
					<div class="modal-body">
						<div class="container">
							<div id="add-plant-loader" class="d-flex justify-content-center p-0 m-0 invisible">
								<div class="spinner-grow text-secondary" role="status">
									<span class="sr-only">Loading...</span>
								</div>
							</div>
							<div id="modal-form-add-plant">
								<form id="form-add-plant" method="post">
									<div class="form-group">
										<label for="new-plant-name">Plant Name: </label>
										<input type="text" class="form-control" id="new-plant-name" name="new-plant-name" placeholder="Plant Name" required>
										<div id="new-plant-name-err"></div>
									</div>
									<input type="hidden" name="plant-uid" value="<?php echo $userID; ?>">
									<div class="form-group">
										<label for="new-plant-desc" aria-describedby="new-plant-desc-help">Plant Description: </label>
										<small id="new-plant-desc-help" class="form-text text-muted m-0 p-0">&nbsp;
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
											<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
										</svg>
										Tip: You may use html tag to format text. Example for bullets you use &nbsp;<samp>&lt;ul&gt;&lt;li&gt;Text 1 Here&lt;/li&gt;&lt;li&gt;Text 2 Here&lt;/li&gt;&lt;/ul&gt;</samp>
										</small>
										<textarea class="form-control" id="new-plant-desc" name="new-plant-desc" maxlength="999" required></textarea>
										<div id="new-plant-desc-err"></div>
									</div>
									<div class="form-group">
										<label for="new-plant-care" aria-describedby="new-plant-care-help">Plant Care Guide: </label>
										<small id="new-plant-care-help" class="form-text text-muted m-0 p-0">&nbsp;
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
											<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
										</svg>
										Tip: You may use html tag to format text. Example for bullets you use &nbsp;<samp>&lt;ul&gt;&lt;li&gt;Text 1 Here&lt;/li&gt;&lt;li&gt;Text 2 Here&lt;/li&gt;&lt;/ul&gt;</samp>
										</small>
										<textarea class="form-control" id="new-plant-care" name="new-plant-care" maxlength="999" required></textarea>
										<div id="new-plant-care-err"></div>
									</div>
									<div class="form-row">
										<div class="col">
											<label for="new-plant-price-from">Starting Price: </label>
											<input type="text" pattern="^\d*\.?[0-9]{1,2}$" class="form-control" id="new-plant-price-from" name="new-plant-price-from" placeholder="00.00" required>
											<div id="new-plant-price-from-err"></div>
										</div>
										<div class="col">
											<label for="new-plant-price-to">Maximum Price: </label>
											<input type="text" pattern="^\d*\.?[0-9]{1,2}$" class="form-control" id="new-plant-price-to" name="new-plant-price-to" placeholder="00.00" required>
											<div id="new-plant-price-to-err"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="new-plant-price-comment">Additional Comment For Pricing: </label>
										<input type="text" class="form-control" id="new-plant-price-comment" name="new-plant-price-comment" placeholder="Comment for price">
									</div>
									<div class="form-group">
										<label for="new-plant-file" aria-describedby="new-plant-file-help">Plant Cover Photo:</label>
										<small id="new-plant-file-help" class="form-text text-muted m-0 p-0">&nbsp;
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
											<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
										</svg>
										Supported format: jpeg and png. Maximum image size is 5MB!
										</small>
										<input type="file" class="form-control-file" id="new-plant-file" name="new-plant-file" accept=".jpg,.jpeg,.png" required>
										<div id="new-plant-file-err"></div>
									</div>
									<div class="form-group mb-3">
										<label for="new-plant-gallery" aria-describedby="new-plant-gallery-help">Plant Gallery:</label>
										<small id="new-plant-gallery-help" class="form-text text-muted m-0 p-0">&nbsp;
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
											<path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
										</svg>
										Optional! Supported format: jpeg and png. Maximum image size is 5MB!
										</small>
										<input type="file" class="form-control-file" id="new-plant-gallery" name="new-plant-gallery[]" multiple="multiple" accept=".jpg,.jpeg,.png">
										<div id="new-plant-gallery-err"></div>
									</div>
									<button type="reset" class="btn btn-danger">Reset Fields</button>
									<button type="submit" class="btn btn-info">Add New Plant</button>
								</form>
							</div>
						</div>
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
		
		<?php if($showsplash == 0 && isset($_GET['page'])): ?>
		<!-- Alert Box -->
		<div class="d-flex justify-content-center align-items-center fixed-bottom">
			<div class="toast shadow mb-5" id="alert-box" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="toast-header">
					<strong class="mr-auto">System</strong>
					<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="toast-body" id="alert-msg"><?php
					// Only show this in index page.
					if(isset($_GET['page']) && isset($_GET['result'])) {
						// is it from login or plant database
						if($_GET['page'] == 'login') {
							// login success or not
							if($_GET['result'] == '1') {
								echo "You are now logged in. You may now add and customize plant database.";
							} else {
								echo "Something went wrong when trying to log you in. Try again later.";
							}
						}
						if($_GET['page'] == 'logout') {
							if($_GET['result'] == '1') {
								echo "You are now logged out.";
							} elseif($_GET['result'] == '2') {
								// make sure to properly destroy session since we are using meta tag to log out instead of javascript
								echo "You have been logged out due to inactivity.";
							} else {
								echo "Something went wrong when trying to log you out. Try again later.";
							}
						}
						if($isOffline == 1) {
							echo "You are currently not connected to the internet. Some features may be unavailable until you are online!";
						}
						if($_GET['page'] == 'plant') {
							switch($_GET['result']) {
								case '1':
								echo "Successfully added plant.";
								break;
								case '2':
								echo "Successfully modified plant.";
								break;
								case '3':
								echo "Plant deleted from the database.";
								break;
								case '4':
								echo "An unknown error has occurred while trying to add plant.";
								break;
								case '5':
								echo "An unknown error has occurred while trying to modify plant.";
								break;
								case '6':
								echo "An unknown error has occurred while trying to delete plant.";
								break;
								default:
								echo "Unrecognized Message.";
							}
						}
					}
				?></div>
			</div>
		</div>
		<?php endif; ?>
		
		
		<!-- The core Firebase JS SDK is always required and must be listed first -->
		<script src="https://www.gstatic.com/firebasejs/8.1.1/firebase-app.js"></script>
		
		<!-- TODO: Add SDKs for Firebase products that you want to use
			 https://firebase.google.com/docs/web/setup#available-libraries -->
		<script src="https://www.gstatic.com/firebasejs/8.2.2/firebase-messaging.js"></script>
		<script src="https://www.gstatic.com/firebasejs/8.1.1/firebase-firestore.js"></script>
		<script src="https://www.gstatic.com/firebasejs/8.2.2/firebase-analytics.js"></script>

		<script>
		  // Your web app's Firebase configuration
		  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
		  var firebaseConfig = {
			apiKey: "<API SECRET>",
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
		  firebase.analytics();
		  const dbFirebase = firebase.firestore();
		  const messaging = firebase.messaging();
		  messaging.requestPermission().then(function() {
			 console.log('Have permission'); 
			 return messaging.getToken();
		  }).then(function(currentToken) {
			  console.log('Token: ', currentToken);
		  }).catch(function(err) {
			  console.log('Error: ');
		  });
		  
		  messaging.onMessage(function(payload) {
			 toastMsg(payload.notification);
			 console.log('onMessage: ', payload);
		  });
		  
		  const toastMsg = (content) => {
			  const toastHtml = `<div class="alert alert-danger" role="alert">
				  <strong>${content.title}</strong> ${content.body}
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				`;
			
			document.querySelector('.pwa-alert').innerHTML = toastHtml;
		  };
		</script>
		<script src="./script/hexel-app.js"><!-- PWA --></script>
		<script src="./script/dbFirebase.js"><!-- Hexel Firebase --></script>
		<script src="./script/jquery-3.5.1.min.js"><!-- JQuery --></script>
		<script src="./script/bootstrap.bundle.min.js"><!-- Bootstrap --></script>
		<script src="./script/hexel.all.script.js"><!-- Javascript Files --></script>
		<script src="./script/hexel.index.script.js"><!-- Javascript Files --></script>
		
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
		</script>
	</body>
</html>