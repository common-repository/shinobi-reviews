<?php
/**
 * The register part of the membership management systems only for Shinobi Reviews
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

use Shinobi_Works\WP\DB;

class Registeration {

	public function __construct() {
		add_action( 'wp_ajax_register', [ $this, 'register' ] );
		add_action( 'wp_ajax_nopriv_register', [ $this, 'register' ] );
	}

	public function register() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'register' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$email    = $_SESSION['shinobi_membership_email'];
			$password = filter_input( INPUT_POST, 'password' );

			if ( UsersManager::get_row_by_email( $email ) ) {
				wp_send_json_error( __( 'You have already registered.', 'shinobi-reviews' ) );
			}

			if ( ! isset( $_SESSION['shinobi_membership_email'] ) || ! isset( $_SESSION['shinobi_membership_password'] ) ) {
				wp_send_json_error( __( 'Not found the required values.', 'shinobi-reviews' ) );
			}

			if ( $password !== $_SESSION['shinobi_membership_password'] ) {
				wp_send_json_error( __( 'Entered password is incorrect.', 'shinobi-reviews' ) );
			}

			$username = filter_input( INPUT_POST, 'username' );

			$insert_result = DB::insert(
				SHINOBI_MEMBERSHIP_TABLE,
				[
					'user_nicename'   => $username,
					'user_email'      => $email,
					'user_pass'       => password_hash( $password, PASSWORD_BCRYPT ),
					'user_registered' => current_time( 'mysql' ),
				]
			);

			if ( $insert_result ) {
				$send_result = wp_mail(
					$_SESSION['shinobi_membership_email'],
					__( 'Registration Successful!', 'shinobi-reviews' ) . ' - ' . get_bloginfo( 'name' ),
					MailFactory::get_registeration_message( $username )
				);

				if ( $send_result ) {
					$_SESSION = [];

					$user_id = UsersManager::get_row_by_email( $email )->ID;

					$_SESSION['shinobi_membership_id'] = $user_id;

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
						]
					);
				} else {
					wp_send_json_error( __( 'Failed to send an email.', 'shinobi-reviews' ) );
				}
			} else {
				wp_send_json_error( __( 'Failed to register.', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}
}
