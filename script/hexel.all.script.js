var arrayError;
			
$(document).ready(function() {
	
	$('#login-form').submit(function(e) {
		var loginform = $('#login-form').serialize();
		// show spinner
		$('#login-form fieldset').prop('disabled');
		$('#login-submit-btn').val('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...');
		$.post('./script/login.php', loginform, function(func) {
			$('#login-spinner').fadeOut(200, function() {
				$('#login-form').show();
			});
			var loginErr = func;
			if(loginErr['has-error'] == "YES") {
				$('#login-form').find(':input').each(function() {
					var x = $(this).attr("name");
					if(loginErr[x] == null) {
						$("#" + x).addClass("is-valid");
					} else {
						var errAttribute = "#" + x + "-err"; // the div below input form. 
						$("#" + x).addClass("is-invalid");
						$("#" + x).attr("aria-describedby", errAttribute);
						$(errAttribute).addClass("invalid-feedback");
						$(errAttribute).html(loginErr[x].toString());
						$("#" + x).val(""); // reset the input field with error.
						$("#loginUsername").focus();
					}
				});
				
			} else {
				
				$('#login-form-body').html('<h4>Redirecting you to home page.</h4>');
				setTimeout(function() {
					window.location.replace('./index.php?ref=nosplash&page=login&result=1'); // go back to home
				}, 1400);
			}
			console.log("done");
		}, 'json');
		e.preventDefault();
	});
	
	$('#login-form :input').on('change input', function() {
		// This is for handling error quality of life. When error is thrown it will show error and won't go away unless you submit the form again.
		// We get ID and reset the errors of the selected input.
		var iID = $(this).val();
		if(iID.length > 1) {
			$(this).removeClass("is-invalid");
			
		}
	});
	
	$('#btn-logouthtml').click(function() {
		$(this).addClass('disabled');
		
		$.getJSON('./script/logout.php', { logout: '1' }, function(func) {
			var funcData = func;
			if(funcData['has-error'] == 'YES') {
				$('#logout-modal-msg').html('<strong>An error has occurred while trying to logout. Please try again later!</strong>');
				setTimeout(function() {
					window.location.replace('./index.php?ref=nosplash&page=logout&result=0'); // go back to home
				}, 1200);
			} else {
				$('#logout-modal-msg').html('<strong>Please wait...</strong>');
				setTimeout(function() {
					window.location.replace('./index.php?ref=nosplash&page=logout&result=1'); // go back to home
				}, 1200);
			}
		});
	});
	
	
	// jQuery event handlers
	$('#loginhtml').on('show.bs.modal', function() {
		$('#login-spinner').hide();
		//console.log('hello');
	});
	$('#loginhtml').on('hide.bs.modal', function() {
		// when you close login modal, reset form. this is the shorthand version
		$('#login-form :input').removeClass('is-invalid').removeClass('is-valid').val('');
	});
});

// functions

