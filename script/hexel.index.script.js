fetchPlant('');

$(document).ready(function() {
	//$('.hexel-splash-title').removeClass('invisible').hide();
	if($('body').is('[data-offline]')) {
		$('.toast').toast({ animation: true, autohide: false });
	} else {
		$('.toast').toast({ animation: true, delay: 10000 });
	}
	
	// toast message and alert for plant changes from firebase
	
	
	if($('body').data('splash') === 'no') {
		$('#alert-box').toast('show');
		/*$('.hexel-splash-title').delay(800).fadeIn(500);
		$('#hexel-splash').delay(4000).fadeOut(200, function() {
			$('#hexel-splash').removeClass('d-flex'); // Remove bootstrap class d-flex which makes hide()/fadeOut()/display: none not work
			$('#content').delay(200).removeClass('invisible').fadeIn();
		});*/
	}/* else {
		$('#hexel-splash').removeClass('d-flex');
		$('#hexel-splash').hide();
		$('#main-splash').hide();
		$('#content').removeClass('invisible').show();
		$('#main-menu').fadeIn(200, function() {
			// Load the alert box message
			$('#alert-box').toast('show');
		});
	}*/
	$('#refresh-btn').click(function() {
		window.location.replace('./index.php#plant-database');
	});
	
	$('#badge-new-item').click(function() {
		$(this).hide();
	});
	
	$('#btnMenu').click(function() {
		//window.location.replace("./article.html");
		$('#main-splash').fadeOut(100, function() {
			$('#main-menu').fadeIn(200, function() {
				// Load the alert box message
				$('#alert-box').toast('show');
			});
		});
	});
	
	/*
	$(window).scroll(function() {
		if($('body').is('[data-webview]')) {
			//$('#navbar-top').addClass('fixed-top');
		} else {
			//$('#navbar-top').toggleClass('container-fluid container', $(window).scrollTop() > 1);
			//$('#navbar-top').toggleClass('container fixed-top', $(window).scrollTop() > 50);
			//$('.navbar').toggleClass('navbar-light bg-light rounded shadow', $(window).scrollTop() > 50);
			//$('.navbar').toggleClass('navbar-dark bg-dark', $(window).scrollTop() <= 50);
			//$('#navbar-top').toggleClass('container-fluid', $(window).scrollTop() <= 50);
			//$('nav').toggleClass('navbar-light', $(window).scrollTop() > 1);
			/*$('#navbar-top nav').removeClass('navbar-dark', $(window).scrollTop() > 1);
			$('#navbar-top nav').addClass('navbar-light', $(window).scrollTop() > 1);
			$('#navbar-top nav').removeClass('navbar-light', $(window).scrollTop() < 1);
			$('#navbar-top nav').addClass('navbar-dark', $(window).scrollTop() < 1);
			$('#navbar-top nav').toggleClass('bg-light', $(window).scrollTop() > 1);
		}
	});
	*/

	$('#search-plant').keyup(function() {
		$("#search-form").submit();
	});
	
	$('#search-form').submit(function() {
		//console.log('key pressed: ' + $('#search-plant').val());
		fetchPlant($('#search-plant').val());
		return false; // stop page from refreshing on submit.
	});
	
	$('#form-add-plant').submit(function(e) {
		var plantForm = new FormData($('#form-add-plant')[0]); // form data instead of serialize for file upload to work.
		console.log($('#form-add-plant').serialize());
		$('#add-plant-loader').addClass('my-5 visible');
		$('#add-plant-loader').removeClass('p-0 m-0 invisible');
		$('#add-plant-modal').text('Please Wait...');
		$('#modal-form-add-plant form').fadeOut(100);
		$.ajax({
			type: 'POST',
			url: './script/validateAddPlant.php',
			data: plantForm,
			cache: false,
			processData: false,
			contentType: false, // make sure jquery doesn't send string only. we have files to upload as well
			success: function(testing) {
				$('#add-plant-loader').removeClass('my-5 visible');
				$('#add-plant-loader').addClass('p-0 m-0 invisible');
				$('#modal-form-add-plant form').fadeIn(300);
				$('#add-plant-modal').text('Add Plant');
				var arrayError = testing;
				console.log(arrayError);
				if(arrayError['has-error'] == "YES") {
					//console.log("something wrong");
					//validateError();
					
					var x = Object.keys(arrayError);
					
						
						x.forEach(function(key) {
							//console.log(key);
							$('#form-add-plant').find(':input').each(function() {
								var addID = $(this).attr("id");
								if(key == addID) {
									var errAttribute = "#" + addID + "-err"; // the div below input form. example #new-plane-name-err
									$("#" + addID).addClass("is-invalid");
									$("#" + addID).attr("aria-describedby", errAttribute);
									$(errAttribute).addClass("invalid-feedback");
									$(errAttribute).html(JSON.stringify(arrayError[key]));
								} else {
									$("#" + addID).addClass("is-valid");
								}
								//console.log(key == addID);
							});
						});
						/*
						//console.log(addID);
						console.log(addID == x);
						if(x[addID] != undefined) {
							var errAttribute = "#" + addID + "-err"; // the div below input form. example #new-plane-name-err
							$("#" + addID).addClass("is-invalid");
							$("#" + addID).attr("aria-describedby", errAttribute);
							$(errAttribute).addClass("invalid-feedback");
							$(errAttribute).html(JSON.stringify(arrayError[x]));
						} else {
							$("#" + x).addClass("is-valid");
						}*/
					
					
					/*
					$('#form-add-plant').find(':input').each(function() {
						var x = $(this).attr("id");
						console.log(arrayError[x] == undefined);
						//console.log("#" + x);
						console.log(Object.keys(arrayError));
						
						if(arrayError[x] == undefined) {
							console.log("#" + x);
							$("#" + x).addClass("is-valid");
						} else {
							var errAttribute = "#" + x + "-err"; // the div below input form. example #new-plane-name-err
							$("#" + x).addClass("is-invalid");
							$("#" + x).attr("aria-describedby", errAttribute);
							$(errAttribute).addClass("invalid-feedback");
							$(errAttribute).html(JSON.stringify(arrayError[x]));
						}
					});*/
					//if(arrayError['has-error-message'] == "NULL" || arrayError['has-error-message'] == undefined) {
					//	window.location.replace('./index.php?ref=nosplash&page=plant&result=4#plant-database');
					//}
				} else {
					//console.log("should be ok");
					//validateSuccess
					// add to firebase
					const plantFS = {
						plant_id: arrayError['has-uid'],
						plant_name: plantForm.get('new-plant-name'),
						plant_img: arrayError['has-img'],
						plant_desc: plantForm.get('new-plant-desc'),
						plant_care: plantForm.get('new-plant-care'),
						plant_price: plantForm.get('new-plant-price-from') + " - " + plantForm.get('new-plant-price-to'),
						plant_comment: plantForm.get('new-plant-price-comment'),
						plant_gallery: arrayError['has-gallery']
					};
					dbFirebase.collection('plant_db').add(plantFS).catch(e => console.log(e));
					$('#modal-form-add-plant').html(arrayError['has-error-message']);
					
					setTimeout(function() {
						window.location.replace('./index.php?ref=nosplash&page=plant&result=1#plant-database'); // go back to home
					}, 2000);
				}
			},
			error: function(err) {
				console.log(err);
				window.location.replace('./index.php?ref=nosplash&page=plant&result=4#plant-database');
			},
			dataType: 'json'
		});
		e.preventDefault();
	});
	
	// event listeners
	$('#addplantmodal').on('hidden.bs.modal', function() {
		fetchPlant('');
		$('#add-plant-loader').removeClass('my-5 visible');
		$('#add-plant-loader').addClass('p-0 m-0 invisible');
	});
});

const popWeb = (data, id) => {
	
	const htmlTable = `
		<a href='./article.php?pid=${data.plant_id}&id=${id}' data-pwa='${id}' target='_self' class='list-group-item list-group-item-action list-group-item-success shadow my-2 rounded' id='plant-list-group'>
			<div class='d-flex justify-content-between'>
			<span class='text-sm-left ml-1 mr-1 no-wrap' id='l-shoulder' aria-hidden='true'></span>
			<span class='text-sm-center text-dark text-capitalize'>${data.plant_name}</span>
			<span class='text-sm-right mr-1 ml-1 no-wrap' id='r-shoulder' aria-hidden='true'></span>
			</div>
		</a>
	`;
	// Add to DOM
	document.getElementById('plant-database-out').innerHTML += htmlTable;
	
};

const popDefault = (ctr) => {
	var htmlTable;
	if(ctr == 0) {
		htmlTable = `
			<a href='#' target='_self' class='list-group-item list-group-item-action list-group-item-success disabled'>
			<div class='d-flex justify-content-between'>
			<span class='text-sm-left ml-1 mr-1 no-wrap' id='l-shoulder' aria-hidden='true'></span>
			<span class='text-sm-center text-dark text-capitalize'>Oops! Plant database seems to be empty! Try again later!</span>
			<span class='text-sm-right mr-1 ml-1 no-wrap' id='r-shoulder' aria-hidden='true'></span>
			</div></a>
		`;
	}
	if(ctr == 'ok') {
		htmlTable = `
			<a href='#' target='_self' class='list-group-item list-group-item-action list-group-item-success disabled no-search'>
			<div class='d-flex justify-content-between'>
			<span class='text-sm-left ml-1 mr-1 no-wrap' id='l-shoulder' aria-hidden='true'></span>
			<span class='text-sm-center text-dark text-capitalize'>Oops! Plant not found. Please refine your search or try again later!</span>
			<span class='text-sm-right mr-1 ml-1 no-wrap' id='r-shoulder' aria-hidden='true'></span>
			</div></a>
		`;
	}
	document.getElementById('plant-database-out').innerHTML = htmlTable;
};

const unpopWeb = (id) => {
	const cachedPlantID = document.querySelector(`#plant-list-group[data-pwa=${id}]`);
	// remove from DOM
	cachedPlantID.remove();
};


function plantAlert(str, ctr) {
	console.log('I am called');
	if(str === 'add') {
		$('#plant-update-msg').html('New plants added. <a href="#" class="btn btn-link btn-sm" id="refresh-btn" role="button">Click here to refresh.</a>');
		$('#new-plant-counter').text(ctr);
	}
	if(str === 'edit') {
		$('#plant-update-msg').html('Plant has been updated. <a href="#" class="btn btn-link btn-sm" id="refresh-btn" role="button">Click here to refresh.</a>');
	}
	$('.toast').toast('show');
}

function fetchPlant(str) {
	var filter, elementID;
	filter = str.toUpperCase();
	$('#plant-database-out').find('a').each(function() {
		// loop through DOM
		var txt = $(this).text().trim();
		console.log(filter);
		console.log(txt.toUpperCase().indexOf(filter));
		if(txt.toUpperCase().indexOf(filter) > -1) {
			//document.querySelector('.no-search').remove();
			$(this).removeClass("invisible");
		} else {
			//popDefault('ok');
			$(this).addClass("invisible");
		}
	});
	//var xmlhttp=new XMLHttpRequest();
	//xmlhttp.onreadystatechange=function() {
	//if (this.readyState==4 && this.status==200) {
	//		document.getElementById("plant-database-out").innerHTML=this.responseText;
//}
	//}
	//xmlhttp.open("GET","./script/fetchPlants.php?q="+str,true);
	//xmlhttp.send();
}