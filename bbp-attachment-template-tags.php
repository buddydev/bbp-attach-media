<?php
/**
 * Template Tags.
 */

/**
 * Inject add media button.
 */
function bbp_add_media_attach_button() {
	if ( ! current_user_can( 'publish_replies' ) ) {
		return;
	}

	echo '<div class="bbp-attach-media">
        <ul class="bbp-media-list"></ul>
        <input type="hidden" name="bbp-attached-media-ids" id="bbp-attached-media-ids" value="" />
        <a class="bbp-add-media-btn" data-delete="Delete image" data-update="Add to gallery" data-uploader-title="Attach Media" href="#">Add Media</a> </div>';
}

/**
 * List all attached media.
 *
 * @param int $post_id post id.
 */
function bbp_list_attached_media( $post_id = 0 ) {
	$media_ids = bbp_attach_media_get_media( $post_id );
	if ( ! empty( $media_ids ) ) {
		// cache it.
		_prime_post_caches( $media_ids, false, true );
	}
	?>
    <ul class="bbp-media-list" id="bbp-media-list-<?php echo $post_id; ?>">
		<?php foreach ( $media_ids as $media_id ) : ?>
            <li class="bbp-media-item" data-attachment_id="<?php echo $media_id; ?>">
                <img src="<?php echo wp_get_attachment_image_url( $media_id ); ?>"/>
				<?php
				/*
					//check for permissions before allowing delete
					<a href="#" class="delete" title="Delete image"><i class="fa fa-times-circle-o" aria-hidden="true"></i> </a>
				*/
				?>
            </li>
		<?php endforeach; ?>
    </ul>
	<?php
}
