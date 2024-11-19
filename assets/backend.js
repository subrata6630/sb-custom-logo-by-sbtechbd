(function($){

	$(document).ready(function(){

		// Depend on saved type to hide/show color/image field
		if (sbcl_admin.bg_type === 'color') {
			$('.type_image').hide();
			$('.type_color').show();
		} else {
			$('.type_color').hide();
			$('.type_image').show();
		}

		$('.sbcl_wp_bg_select').on('change', function(){

			// Get the selected value
			var selected_type = $(this).find(":selected").val();

			if (selected_type === 'color') {
				$('.type_image').hide();
				$('.type_color').show();
			} else {
				$('.type_color').hide();
				$('.type_image').show();
			}

		});

		// Initialize color picker
		$('.sbcl_wp_bg_color').wpColorPicker();
		$('.sbcl_wp_link_color').wpColorPicker();
		$('.sbcl_wp_link_hover_color').wpColorPicker();

		// Image Popup Function
		var frame;
		$('[id=sbcl-upload-btn]').click(function(e) {
			e.preventDefault();

			var btn = $(this);

			// If the media frame already exists, reopen it.
		    if (frame) {
		      frame.open();
		      return;
		    }

		    // Create a new media frame
			var frame = wp.media({ 
				title: btn.hasClass('sbcl-logo') ? 'Upload Logo' : 'Upload Background',
				multiple: false
			});

			// When an image is selected in the media frame...
			frame.on('select', function() {
				var uploaded_image = frame.state().get('selection').first();
				var image_url = uploaded_image.toJSON().url;
				btn.closest('td').find('input[type=text]').val(image_url);

				// Instant replace image src to display selected logo
				if (btn.hasClass('sbcl-logo')) {
					$('.sbcl_current_logo').attr('src', image_url);
				}

				// Instant replace background img src to display selected background img
				if (btn.hasClass('sbcl-bg')) {
					$('.sbcl_current_bg').attr('src', image_url);
				}
			});

			// Finally, open the modal on click
    		frame.open();
		});

	});

})(jQuery);
