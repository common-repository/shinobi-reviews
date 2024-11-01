<?php
/**
 * Helper
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.2.0
 */

namespace Shinobi_Reviews\App;

use Shinobi_Works\WP\DB;

class Helper {

	const IS_SHINOBI_REVIEWS = 'is_shinobi_reviews';

	public function __construct() {
		add_action( 'save_post', [ $this, 'save_post' ], 100, 3 );
	}

	/**
	 * Notice to administer by email
	 *
	 * @return void
	 */
	public static function notice_to_admin( $subject, $message_array ) {
		$admin_email = get_option( 'admin_email' );
		$message     = implode( PHP_EOL . PHP_EOL, $message_array );
		wp_mail( $admin_email, $subject, $message );
	}

	/**
	 * Check if Shinobi Reviews is used in post.
	 *
	 * @return boolean
	 * @since 1.0.9
	 */
	public static function is_shinobi_reviews() {
		if ( ! is_singular() ) {
			return false;
		}
		$option_name = 'is_shinobi_reviews';
		$ids         = DB::get_option( $option_name );
		if ( ! $ids ) {
			$old_ids = \get_option( $option_name );
			if ( $old_ids ) {
				DB::update_option( $option_name, $old_ids );
				$ids = DB::get_option( $option_name );
			}
		}
		if ( $ids && is_array( $ids ) && in_array( get_queried_object_id(), $ids, true ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Include file in currectly
	 *
	 * @param int     $id
	 * @param object  $post post object.
	 * @param boolean $update
	 * @return void
	 */
	public function save_post( $id, $post, $update ) {
		$parent_id = wp_is_post_revision( $id );
		if ( $parent_id ) {
			$id = $parent_id;
		}
		if ( 'post' !== $post->post_type ) {
			return;
		}
		$ids = DB::get_option( self::IS_SHINOBI_REVIEWS );
		if ( ! $ids ) {
			$ids = [];
		}
		if ( is_array( $ids ) ) {
			$pattern = '[' . SHINOBI_REVIEWS . ' id=';
			$subject = $post->post_content;
			if ( false !== strpos( $subject, $pattern ) ) {
				if ( ! in_array( $id, $ids, true ) ) {
					$ids[] = $id;
					DB::update_option( self::IS_SHINOBI_REVIEWS, $ids );
				}
			} else {
				if ( in_array( $id, $ids, true ) ) {
					$ids = array_diff( $ids, [ $id ] );
					$ids = array_values( $ids );
					DB::update_option( self::IS_SHINOBI_REVIEWS, $ids );
				}
			}
		}
	}

}
