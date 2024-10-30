jQuery( document ).ready( function( $ ) {
    jQuery.datetimepicker.setLocale(language_info.language);
    // Uploading files
    var file_frame;
    var mb_wp_media_post_id = wp.media.model.settings.post.id; // Store the old id

    //Get user timezone to cast datetime to UTC
    jQuery('#mb_user_time_zone').val(new Date().getTimezoneOffset());
    jQuery('#mb_upload_image_button').on('click', function( event ){
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            // Set the post ID to what we want
            file_frame.uploader.uploader.param( 'post_id', 0 );
            // Open frame
            file_frame.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = 0;
        }
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to upload',
            button: {
                text: 'Use this image',
            },
            multiple: false	// Set to true to allow multiple files to be selected
        });
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
            $( '#image_attachment_id' ).val( attachment.id );
            // Restore the main post ID
            wp.media.model.settings.post.id = mb_wp_media_post_id;
        });
        // Finally, open the modal
        file_frame.open();
    });
    // Restore the main ID when the add media button is pressed
    jQuery( 'a.add_media' ).on( 'click', function() {
        wp.media.model.settings.post.id = mb_wp_media_post_id;
    });


    jQuery('#mb_date_timepicker_start').datetimepicker(getSettingsByLanguage(language_info.language, "start"));
    jQuery('#mb_date_timepicker_end').datetimepicker(getSettingsByLanguage(language_info.language, "end"));

    function getSettingsByLanguage(language, type) {
        var returnSettings = {
            timepicker: true
        };
        if (language === "en") {
            jQuery.extend(returnSettings, {
                style: "min-width: 310px",
                format: "m/d/Y h:i a",
                formatTime: 'h:i a',
                formatDate: 'm/d/Y',
                validateOnBlur: false,
                onShow: getOnShowProperty(type)
            });
        }
        else {
            jQuery.extend(returnSettings, {
                format: 'd/m/Y H:i',
                formatTime: 'H:i',
                formatDate: 'd/m/Y',
                onShow: getOnShowProperty(type)
            });
        }

        return returnSettings;
    }

    function getOnShowProperty(type) {
        if(type === "start") {
            return function( ct ){
                this.setOptions({
                    maxDate:jQuery('#mb_date_timepicker_end').val()?jQuery('#mb_date_timepicker_end').val():false
                })
            };
        }
        return function( ct ){
            this.setOptions({
                minDate:jQuery('#mb_date_timepicker_start').val()?jQuery('#mb_date_timepicker_start').val():false
            })
        };
    }
});