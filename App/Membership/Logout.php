<?php
/**
 * The logout part of the membership management systems only for Shinobi Reviews
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

class Logout {

	public function __construct() {
		add_action( 'wp_ajax_logout', [ $this, 'logout' ] );
		add_action( 'wp_ajax_nopriv_logout', [ $this, 'logout' ] );
	}

	public function logout() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'logout' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$_SESSION = [];

			wp_send_json_success(
				[
					'action' => 'authenticate',
					'nonce'  => wp_create_nonce( 'authenticate' ),
				]
			);
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}
}
