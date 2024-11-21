jQuery(document).ready(function ($) {
    // Open WordPress media uploader when the upload button is clicked
    $('#sbclb_upload_button').on('click', function (e) {
        e.preventDefault();

        // Create a custom media uploader
        var custom_uploader = wp.media({
            title: 'Select Logo',
            button: {
                text: 'Use this logo', // Button text
            },
            multiple: false, // Set to false to allow only one file to be selected
        }).on('select', function () {
            // Get the selected image details
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            
            // Set the image URL in the input field
            $('#sbclb_logo_image').val(attachment.url);
        }).open(); // Open the media uploader
    });
});
