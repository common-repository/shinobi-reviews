<?php
/**
 * The pre-register part of the membership management systems only for Shinobi Reviews
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

class Authenticate {

	public function __construct() {
		add_action( 'wp_ajax_authenticate', [ $this, 'authenticate' ] );
		add_action( 'wp_ajax_nopriv_authenticate', [ $this, 'authenticate' ] );
	}

	public function authenticate() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		$email = filter_input( INPUT_POST, 'email' );

		if ( wp_verify_nonce( $nonce, 'authenticate' ) ) {
			SessionManager::shinobi_reviews_session_start();

			if ( UsersManager::get_row_by_email( $email ) ) {
				$_SESSION['shinobi_membership_email'] = $email;

				wp_send_json_success(
					[
						'login'             => [
							'action' => 'login',
							'nonce'  => wp_create_nonce( 'login' ),
						],
						'sendResetPassword' => [
							'action' => 'send_reset_password',
							'nonce'  => wp_create_nonce( 'send_reset_password' ),
						],
						'logout'            => [
							'action' => 'logout',
							'nonce'  => wp_create_nonce( 'logout' ),
						],
					]
				);
			}

			// generate a temporary password for registration
			$password = wp_generate_password( 8, false, false );

			$result = wp_mail(
				$email,
				__( 'Notification of temporary registration completion', 'shinobi-reviews' ) . ' - ' . get_bloginfo( 'name' ), // subject
				MailFactory::get_pre_registeration_message( $password )
			);

			if ( $result ) {
				$_SESSION['shinobi_membership_id']       = 0;
				$_SESSION['shinobi_membership_email']    = $email;
				$_SESSION['shinobi_membership_password'] = $password;

				wp_send_json_success(
					[
						'register' => [
							'action' => 'register',
							'nonce'  => wp_create_nonce( 'register' ),
						],
						'logout'   => [
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
}
