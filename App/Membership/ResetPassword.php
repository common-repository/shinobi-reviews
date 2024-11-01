<?php
/**
 * The reset password part of the membership management systems only for Shinobi Reviews
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
use Shinobi_Works\WP\DB;

class ResetPassword {

	public function __construct() {
		add_action( 'wp_ajax_send_reset_password', [ $this, 'send_reset_password' ] );
		add_action( 'wp_ajax_nopriv_send_reset_password', [ $this, 'send_reset_password' ] );
		add_action( 'wp_ajax_reset_password', [ $this, 'reset_password' ] );
		add_action( 'wp_ajax_nopriv_reset_password', [ $this, 'reset_password' ] );
	}

	public function send_reset_password() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'send_reset_password' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$password = wp_generate_password( 8, false, false );

			$_SESSION['shinobi_membership_password'] = $password;

			$result = wp_mail(
				$_SESSION['shinobi_membership_email'],
				__( 'Reset your password', 'shinobi-reviews' ) . ' - ' . get_bloginfo( 'name' ), // subject
				MailFactory::get_password_reset_message( $password )
			);

			if ( $result ) {
				wp_send_json_success(
					[
						'resetPassword' => [
							'action' => 'reset_password',
							'nonce'  => wp_create_nonce( 'reset_password' ),
						],
						'logout'        => [
							'action' => 'logout',
							'nonce'  => wp_create_nonce( 'logout' ),
						],
					]
				);
			} else {
				wp_send_json_error( __( 'Failed to send an email.', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

	public function reset_password() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'reset_password' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$password = filter_input( INPUT_POST, 'password' );

			if ( $password && $_SESSION['shinobi_membership_password'] === $password ) {
				$user_data = UsersManager::get_row_by_email( $_SESSION['shinobi_membership_email'] );

				if ( $user_data ) {
					$_SESSION = [];

					$uesr_id = $user_data->ID;

					SessionManager::set_shinobi_membership_id( $uesr_id );

					DB::update(
						SHINOBI_MEMBERSHIP_TABLE,
						[ 'user_pass' => password_hash( $password, PASSWORD_BCRYPT ) ],
						[ 'ID' => $uesr_id ]
					);

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
					wp_send_json_error( __( 'An unexpected error has occurred.', 'shinobi-reviews' ) );
				}
			} else {
				wp_send_json_error( __( 'Entered password is incorrect.', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}
}
