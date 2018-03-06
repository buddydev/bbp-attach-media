<?php
/**
 * Plugin Name: bbPress Attach Media
 * Version: 1.0.0
 * Author: BuddyDev Team
 * Author URI: https://buddydev.com
 * Description: Allow to attach media to forum posts, It is for demonstration purpose and not finished.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Main class.
 */
class BBP_Attach_Media_Helper {

	/**
	 * Plugin base url.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Plugin root dir path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * BBP_Attach_Media_Helper constructor.
	 */
	public function __construct() {
		$this->url  = plugin_dir_url( __FILE__ );
		$this->path = plugin_dir_path( __FILE__ );

		$this->setup();
	}

	/**
	 * Setup hooks.
	 */
	public function setup() {

		add_action( 'bbp_loaded', array( $this, 'load' ) );
		// save meta.
		add_action( 'bbp_new_reply_post_extras', array( $this, 'save_media' ) );
		add_filter( 'user_has_cap', array( $this, 'add_upload_cap_filter' ), 0, 3 );
		// assets load.
		add_action( 'bbp_enqueue_scripts', array( $this, 'load_js' ) );

		// add the attach button to reply.
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', 'bbp_add_media_attach_button' );

		// add the media list to the posts.
		add_action( 'bbp_theme_after_reply_content', array( $this, 'list_attachments' ) );
	}

	/**
	 * Load required files.
	 */
	public function load() {
		require_once $this->path . 'bpp-attach-media-functions.php';
		require_once $this->path . 'bbp-attachment-template-tags.php';
	}

	/**
	 * Save media in meta.
	 *
	 * @param int $reply_id topic reply id.
	 */
	public function save_media( $reply_id ) {

		$media_ids = wp_parse_id_list( $_POST['bbp-attached-media-ids'] );

		if ( $media_ids ) {
			bbp_attach_media_update_media( $reply_id, $media_ids );
		} else {
			// delete.
			bbp_attach_media_delete_media( $reply_id );
		}
	}

	/**
	 * Allow all users to upload.
	 *
	 * @param array  $allcaps all caps.
	 * @param string $cap requested cap.
	 * @param array  $args args.
	 *
	 * @return mixed
	 */
	public function add_upload_cap_filter( $allcaps, $cap, $args ) {

		if ( $args[0] != 'upload_files' && $args[0] != 'edit_post' ) {
			return $allcaps;
		}

		if ( ! $this->enable_upload_filters() ) {
			return $allcaps;
		}

		if ( $args[0] == 'upload_files' ) {
			$allcaps[ $cap[0] ] = true;
		} elseif ( $args[0] == 'edit_post' ) {
			$user_id = get_current_user_id();
			$post_id = isset( $args[2] ) ? absint( $args[2] ) : 0;

			if ( $post_id ) {
				$post = get_post( $post_id );

				if ( $post && $post->post_author == $user_id && $args[1] == $user_id ) {
					$allcaps[ $cap[0] ] = true;

				}
			}
		}

		return $allcaps;
	}

	/**
	 * Enable upload filters.
	 *
	 * @return bool
	 */
	public function enable_upload_filters() {
		return true;
	}

	/**
	 * List attachments.
	 */
	public function list_attachments() {
		$reply_id = bbp_get_reply_id();
		bbp_list_attached_media( $reply_id );
	}

	/**
	 * Load js
	 */
	public function load_js() {

		if ( is_user_logged_in() && bbp_is_single_topic() && current_user_can( 'publish_replies' ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'bbp-attach-media-js', plugin_dir_url( __FILE__ ) . 'assets/bbp-attach-media.js', array( 'jquery' ) );
		}
	}
}

new BBP_Attach_Media_Helper();
