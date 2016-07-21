
jQuery( document ).ready( function ( $ ) {

    //media frame
    var bbp_media_frame;
    var $bbp_attached_media_ids = $( '#bbp-attached-media-ids' );
    var $bbp_media_list    = $( '.bbp-form' ).find( 'ul.bbp-media-list' );

    jQuery( '.bbp-attach-media' ).on( 'click', '.bbp-add-media-btn', function( event ) {
        console.log('Handling wtf');
        var $el = $( this );
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( bbp_media_frame ) {
            bbp_media_frame.open();
            return;
        }

        // Create new media frame
        bbp_media_frame = wp.media.frames.bbp_media_frame = wp.media({
            //
            title: $el.data( 'uploader-title' ),
            button: {
                text: $el.data( 'update' )
            },
            states: [
                new wp.media.controller.Library({
                    title: $el.data( 'uploader-title' ),
                    filterable: 'all',
                    multiple: true
                })
            ]
        });

        // When media is selected, run a callback.
        bbp_media_frame.on( 'select', function() {
            var selection = bbp_media_frame.state().get( 'selection' );
            var attachment_ids = $bbp_attached_media_ids.val();

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $bbp_media_list.append( '<li class="bbp-media-item" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><a href="#" class="delete" title="' + $el.data('delete') + '">' + 'X' + '</a></li>' );
                }
            });

            $bbp_attached_media_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        bbp_media_frame.open();
    });

    // Remove media
    $( 'ul.bbp-media-list' ).on( 'click', 'a.delete', function() {
        var $media =     $( this ).closest( 'li.bbp-media-item' );
        //remove from list
        $media.remove();

        //send the media id and post id to server to allow removing it

        return false;
    });

});