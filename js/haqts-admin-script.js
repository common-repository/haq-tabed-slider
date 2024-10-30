jQuery(function(jQuery) {
    
    var file_frame,
            haqslider = {
        admin_thumb_ul: '',
        init: function() {
            this.admin_thumb_ul = jQuery('#gallery_thumbnail');
            this.admin_thumb_ul.sortable({
                placeholder: '',
				revert: true,
            });
            this.admin_thumb_ul.on('click', '.delete_slide', function() {
                if (confirm('Are you want to delete this slide?')) {
                    jQuery(this).parent().fadeOut(1000, function() {
                        jQuery(this).remove();
                    });
                }
                return false; 
            });
            
            jQuery('#slide_upload_button').on('click', function(event) {
                event.preventDefault();
                if (file_frame) {
                    file_frame.open();
                    return;
                }

                file_frame = wp.media.frames.file_frame = wp.media({
                    title: jQuery(this).data('uploader_title'),
                    button: {
                        text: jQuery(this).data('uploader_button_text'),
                    },
                    multiple: true
                });

                file_frame.on('select', function() {
                    var images = file_frame.state().get('selection').toJSON(),
                            length = images.length;
                    for (var i = 0; i < length; i++) {
                        haqslider.get_thumbnail_url(images[i]['id']);
                    }
                });
                file_frame.open();
            });
			
			jQuery('#slide_delete_button').on('click', function() {
                if (confirm('Are you sure you want to delete all the image slides?')) {
                    haqslider.admin_thumb_ul.empty();
                }
                return false;
            });

           
        },
        get_thumbnail_url: function(id, cb) {
            cb = cb || function() {
            };
            var data = {
                action: 'haqts_get_thumbnail',
                imageid: id
            };
            jQuery.post(ajaxurl, data, function(response) {
                haqslider.admin_thumb_ul.append(response);
                cb();
            });
        },
        
    };
    haqslider.init();
});