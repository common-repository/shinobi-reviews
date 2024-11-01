<?php
/**
 * Helper functions for membership system
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

class SessionManager {

	public function __construct() {
		add_action( 'send_headers', [ $this, 'shinobi_reviews_session_start' ] );
	}

	/**
	 * The session start
	 *
	 * @return void
	 */
	public static function shinobi_reviews_session_start() {
		if ( ! headers_sent() ) {
			if ( PHP_SESSION_ACTIVE !== session_status() ) {
				session_name( SHINOBI_MEMBERSHIP_SESSION_NAME );
				session_start();
			}
		}
	}

	/**
	 * Get the membership id of Shionbi Reviews
	 *
	 * @return int|false|null
	 */
	public static function get_shinobi_membership_id() {
		return filter_var( self::set_shinobi_membership_id(), FILTER_VALIDATE_INT );
	}

	/**
	 * Set the membership id of Shionbi Reviews
	 *
	 * @return int|null
	 */
	public static function set_shinobi_membership_id( $id = null ) {
		if ( $id ) {
			$_SESSION['shinobi_membership_id'] = $id;
		}

		if ( ! isset( $_SESSION['shinobi_membership_id'] ) ) {
			$_SESSION['shinobi_membership_id'] = -1;
		}

		return $_SESSION['shinobi_membership_id'];
	}
}
