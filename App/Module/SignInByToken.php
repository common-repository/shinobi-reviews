<?php
/**
 * Authenticate whether using pro or not
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com
 * @since      1.3.8
 */

namespace Shinobi_Reviews\App\Module;

use Shinobi_Works\WP\DB;

class SignInByToken {

	public function __construct() {
		add_action( 'wp_ajax_sign_in_by_token', [ $this, 'sign_in_by_token' ] );
		add_action( 'wp_ajax_nopriv_sign_in_by_token', [ $this, 'sign_in_by_token' ] );
	}

	/**
	 * This is an api-like system, haha.
	 *
	 * @return boolean
	 */
	public function sign_in_by_token() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'sign_in_by_token' ) ) {
			$token = filter_input( INPUT_POST, 'token' );

			$user_data = DB::get_option( 'shinobiReviewsAuthUserData' );

			$decrypt_site_key = openssl_decrypt(
				base64_decode( $token, true ), // phpcs:ignore
				'aes-256-cbc',
				base64_decode( $user_data['secret_key'], true ), // phpcs:ignore
				OPENSSL_RAW_DATA,
				base64_decode( $user_data['iv'], true ) // phpcs:ignore
			);

			if ( base64_decode( $user_data['site_key'], true ) === $decrypt_site_key ) { // phpcs:ignore
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} else {
			wp_send_json_error( __( 'An unexpected error has occurred.', 'shinobi-reviews' ) );
		}
	}

}
