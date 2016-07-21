<?php
/**
 * Get an array of media ids attached to the given post
 *
 * @param $entry_id
 *
 * @return mixed
 */
function bbp_attach_media_get_media( $post_id ) {
	return get_post_meta( $post_id, '_attached_media_id' );
}

/**
 * Update media attached to the given post
 *
 * @param $post_id
 * @param $ids
 */
function bbp_attach_media_update_media( $post_id, $ids ) {

	$ids = wp_parse_id_list( $ids );
	$ids = array_filter( $ids ); //remove the empty entry if any
	//delete old gallery
	bbp_attach_media_delete_media( $post_id );
	//add each media as individual entry
	foreach ( $ids as $media_id ) {
		add_post_meta( $post_id, '_attached_media_id', $media_id );
	}

}

/**
 * Delete all media attached to the given post
 * @param $post_id
 *
 * @return bool
 */
function bbp_attach_media_delete_media( $post_id ) {
	return delete_post_meta( $post_id, '_attached_media_id' ) ;
}
