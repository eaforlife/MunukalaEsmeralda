$(document).ready(function() {
	
	
	// event listeners
	$('#delete-plant-modal').on('show.bs.modal', function() {
		$('#plant-name-for-modal').text($('#plant-name-title').text());
	});
	
	$('#edit-plant-modal').on('show.bs.modal', function() {
		editDefaults();
	});
	
	$('#btn-reset-edit').click(function() {
		editDefaults();
	});
	
	$('#btn-delete-plant').click(function() {
		var formDel = $('#delete-plant-form').serialize();
		var pwaID = $('#plant-pwa').val();
		$('#deleteplantmodal').text('Deleting..');
		$('#delete-modal-body').text('Please Wait');
		$('#btn-delete-plant, #btn-delete-close').prop('disabled',true);
		$x = $('#edit-plant-id').val();
		console.log($x);
		$.ajax({
			type: 'POST',
			url: './script/validateEditPlant.php',
			data: formDel,
			success: function(reply) {
				console.log(reply);
				var repObj = reply;
				if(repObj['has-error'] == 'YES') {
					$('#delete-modal-body').text('Error: ' + repObj['msg'] + '<br>Please wait');
					setTimeout(function() {
						window.location.replace('./index.php?ref=nosplash&page=plant&result=6#plant-database');
					}, 2000);
				} else {
					$('#delete-modal-body').text('Done. Please wait...');
					
					// Delete From Firebase
					dbFirebase.collection('plant_db').doc(pwaID).delete();
					
					setTimeout(function() {
						window.location.replace('./index.php?ref=nosplash&page=plant&result=3#plant-database'); // go back to home
					}, 1200);
				}
			},
			error: function(err) {
				// error handling
				//console.log(err);
				window.location.replace('./index.php?ref=nosplash&page=plant&result=6#plant-database');
			},
			dataType: 'json'
		});
	});
	
	//var $editForm = $('#form-edit-plant'), defForm = $editForm.serialize();
	//console.log(defForm);
	$('#btn-submit-edit').click(function() {
		// Fire up loading
		$(this).text('Please Wait');
		$('#editplantmodal').text('Please Wait');
		$('#btn-reset-edit').prop('disabled', true);
		$('#btn-cancel-edit').prop('disabled', true);
		$('#btn-submit-edit').prop('disabled', true);
		$('#form-edit-plant fieldset').fadeOut(200);
		
		var changedCounter = 0;
		if($('#edit-plant-desc').val().toLowerCase() !== $('#plant-description p').text().toLowerCase()) {
			changedCounter += 1;
		}
		if($('#edit-plant-care').val().toLowerCase() !== $('#plant-care span').text().toLowerCase()) {
			changedCounter += 1;
		}
		if($('#edit-plant-price-from').val() !== $('#plant-price-from').text()) {
			changedCounter += 1;
		}
		if($('#edit-plant-price-to').val() !== $('#plant-price-to').text()) {
			changedCounter += 1;
		}
		if($('#edit-plant-price-comment').val().toLowerCase() !== $('#plant-comment').text().toLowerCase()) {
			changedCounter += 1;
		}
		
		if(changedCounter > 0) {
			//console.log($('#form-edit-plant').serialize());
			$('#form-edit-plant').submit();
		} else {
			// Nothing was changed.
			$(this).text('No changes has been made');
			editDefaults();
			setTimeout(function() {
				$("#edit-plant-modal").modal('hide');
			}, 2000);
		}
	});
	
	$('#form-edit-plant').submit(function(e) {
		var formEdit = $('#form-edit-plant').serialize();
		var pwaEdit = new FormData($('#form-edit-plant')[0]);
		
		//console.log(formEdit);
		
		$.ajax({
			type: 'POST',
			url: './script/validateEditPlant.php',
			data: formEdit,
			success: function(reply) {
				//console.log(reply);
				var repObj = reply;
				$('#editplantmodal').text('Done');
				if(repObj['has-error'] == 'YES') {
					//$('#modal-edit-body').html('Error: ' + repObj['msg']);
					$('#form-edit-plant').find(':input').each(function() {
						
						var errNameAttr = $(this).attr("name");
						
						$('#btn-submit-edit').text('Submit');
						$('#editplantmodal').text('Edit Plant');
						$('#btn-reset-edit').prop('disabled', false);
						$('#btn-cancel-edit').prop('disabled', false);
						$('#btn-submit-edit').prop('disabled', false);
						
						if(repObj[errNameAttr] == null) {
							$("#" + errNameAttr).addClass("is-valid");
						} else {
							var errAttribute = "#" + errNameAttr + "-err"; // the div below input form. example #new-plane-name-err
							$("#" + errNameAttr).addClass("is-invalid");
							$("#" + errNameAttr).attr("aria-describedby", errAttribute);
							$(errAttribute).addClass("invalid-feedback");
							$(errAttribute).html(repObj[errNameAttr].toString());
						}
						
						$('#form-edit-plant fieldset').fadeIn(200);
					});
				} else {
					$('#modal-edit-body').html('Finishing...');
					$('#btn-submit-edit').text('Please Wait...');
					
					// Update Firebase
					dbFirebase.collection("plant_db").doc(pwaEdit.get('edit-pwa-id')).update({
						plant_desc: pwaEdit.get("edit-plant-desc"),
						plant_care: pwaEdit.get("edit-plant-care"),
						plant_price: pwaEdit.get("edit-plant-price-from") + " - " + pwaEdit.get("edit-plant-price-to"),
						plant_comment: pwaEdit.get("edit-plant-price-comment")
					});
					
					setTimeout(function() {
						window.location.replace('./index.php?ref=nosplash&page=plant&result=2#plant-database');
					}, 2000);
				}
			},
			dataType: 'json',
			error: function(err) {
				console.log(err);
				$('#editplantmodal').text('Unexpected Error');
				$('#modal-edit-body').html('Please Wait.');
				setTimeout(function() {
					window.location.replace('./index.php?ref=nosplash&page=plant&result=5#plant-database');
				}, 1400);
			}
			
		});
		
		//$('#btn-submit-edit').text('No changes has been made');
		e.preventDefault(); // avoid any redirects and submit
	});
	
	$('#btn-add-gallery-plant').click(function() {
		//console.log('Hello');
		$('#add-gallery-form').submit();
	});
	
	$('#add-gallery-form').submit(function(e) {
		var galleryForm = new FormData($('#add-gallery-form')[0]);
		var pwaGalleryId = galleryForm.get('edit-plant-pwa-id');
		console.log(pwaGalleryId);
		$('#addgallerymodal').text('Uploading...');
		$('#add-gallery-modal-body').html('<p>Please Wait</p>');
		$.ajax({
			type: 'POST',
			url: './script/gallery_manager.php',
			data: galleryForm,
			cache: false,
			processData: false,
			contentType: false,
			success: function(reply) {
				//console.log(reply);
				$('#addgallerymodal').text('Done');
				var resp = reply;
				console.log(resp);
				if(resp['has-error'] == 'YES') {
					$('#add-gallery-modal-body').html('<p>Unable to add more photos. Try again later.</p>');
				} else {
					// update PWA
					dbFirebase.collection("plant_db").doc(pwaGalleryId).update({
						plant_gallery: resp['has-gallery']
					});
					$('#add-gallery-modal-body').html('<p>You may now close this window.</p>');
				}
			},
			error: function(err) {
				console.log(err);
				$('#addgallerymodal').text('Done');
				$('#add-gallery-modal-body').html('<p>An unexpected error has occurred. Please try again later.</p>' + JSON.stringify(err));
			},
			dataType: 'json'
		});
		
		e.preventDefault();
	});
	
	$('#gallery-delete-form').submit(function(e) {
		var testForm = $('#gallery-delete-form').serialize();
		var test12 = $('#gallery-delete-form #edit-pwa-tag').val();
		var test123 = $('#gallery-delete-form input:checkbox:not(:checked)').map(function () {
			// returns psuedo unchecked data-pwa value
			return $(this).data('pwa');
		}).get();
		var newPWAString = "";
		var oldPWAString = test12.split("~");
		var pwaGalleryId = $('#gallery-delete-form #edit-pwa-id').val();
		
		for(x = 0; x < test123.length; x++) {
			for(y = 0; y < oldPWAString.length; y++) {
				if(oldPWAString[y] === test123[x]) {
					newPWAString = test123[x] + "~" + newPWAString;
				}
			}
		}
		if(newPWAString == "") {
			newPWAString = "false";
		}
		console.log("New gallery string: ", newPWAString);
		console.log("Plant ID: ", pwaGalleryId);
		
		$(this).prop('disabled', true);
		$.post('./script/gallery_manager.php', testForm, function(e) {
				$(this).prop('disabled', false);
				$(this).trigger('reset');
				dbFirebase.collection("plant_db").doc(pwaGalleryId).update({
					plant_gallery: newPWAString
				});
			}, 
			'json'
		).fail(function(e) {
			// error handling
			//window.location.reload(true);
			console.log('fail post');
			console.log(e);
			//$('#delete-debug-text').text(JSON.stringify(e));
			//alert(JSON.stringify(e));
		}).always(function() {
			setTimeout(function() {
				// need to reload page after certain seconds for firestore update to work...
				window.location.reload(true);
			}, 1500);
		});
		
		e.preventDefault();
	});
	
	$('#gallery-delete-button').click(function() {
		$('#gallery-delete-form').submit();
	});
	$('#gallery-delete-reset').click(function() {
		$('#gallery-delete-form').trigger('reset');
	});
	
	// event handlers
	$('#add-gallery-modal').on('hide.bs.modal', function() {
		window.location.reload(true);
	});
	$('#edit-plant-modal').on('hide.bs.modal', function() {
		editDefaults();
	});
	
});

function editDefaults() {
	$('#current-plant-name').val($('#plant-name-title').text().toUpperCase());
	$('#edit-plant-desc').val($('#plant-description p').text()).prop('disabled', false).removeClass('is-valid is-invalid');
	$('#edit-plant-care').val($('#plant-care span').text()).prop('disabled', false).removeClass('is-valid is-invalid');
	$('#edit-plant-price-from').val($('#plant-price-from').text()).prop('disabled', false).removeClass('is-valid is-invalid');
	$('#edit-plant-price-to').val($('#plant-price-to').text()).prop('disabled', false).removeClass('is-valid is-invalid');
	$('#edit-plant-price-comment').val($('#plant-comment').text()).prop('disabled', false).removeClass('is-valid is-invalid');
}