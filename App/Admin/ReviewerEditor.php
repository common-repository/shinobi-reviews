<?php
/**
 * Reviewer editor
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.3.0
 */

namespace Shinobi_Reviews\App\Admin;

use Shinobi_Works\WP\DB;

class ReviewerEditor {

	public function __construct() {
		add_action( 'wp_ajax_update_user_status', [ $this, 'update_user_status' ] );
	}

	public function update_user_status() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'reviewer_editor' ) ) {
			$id          = filter_input( INPUT_POST, 'id' );
			$user_status = filter_input( INPUT_POST, 'user_status' );

			DB::update( SHINOBI_MEMBERSHIP_TABLE, [ 'user_status' => $user_status ], [ 'ID' => $id ] );

			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'Invalid Request.', 'shinobi-reviews' ) );
		}
	}
}
