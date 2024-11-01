<?php
/**
 * The login part of the membership management systems only for Shinobi Reviews
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.3.0
 */

namespace Shinobi_Reviews\App\Membership;

use Shinobi_Reviews\App\FetchOwnReview;

class Login {

	public function __construct() {
		add_action( 'wp_ajax_login', [ $this, 'login' ] );
		add_action( 'wp_ajax_nopriv_login', [ $this, 'login' ] );
	}

	public function login() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'login' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$user_data = UsersManager::get_row_by_email( $_SESSION['shinobi_membership_email'] );

			if ( $user_data && $user_data->user_pass ) {
				$password = filter_input( INPUT_POST, 'password' );

				if ( password_verify( $password, $user_data->user_pass ) ) {
					$_SESSION = [];

					SessionManager::set_shinobi_membership_id( $user_data->ID );

					wp_send_json_success(
						[
							'post'   => [
								'review' => [
									'action' => 'post_review',
									'nonce'  => wp_create_nonce( 'post_review' ),
								],
								'media'  => [
									'action' => 'post_media',
									'nonce'  => wp_create_nonce( 'post_media' ),
								],
							],
							'logout' => [
								'action' => 'logout',
								'nonce'  => wp_create_nonce( 'logout' ),
							],
						] + FetchOwnReview::form_actions()
					);
				} else {
					wp_send_json_error( __( 'Entered password is incorrect.', 'shinobi-reviews' ) );
				}
			} else {
				wp_send_json_error( __( 'An unexpected error has occurred.', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}
}
