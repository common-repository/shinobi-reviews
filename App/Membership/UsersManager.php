<?php
/**
 * Users management system
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

class UsersManager {

	/**
	 * Get a row from shinobi membership database by email address
	 *
	 * @return object|null
	 */
	public static function get_row_by_email( $email ) {
		$membership_table = DB::get_results( SHINOBI_MEMBERSHIP_TABLE );

		foreach ( $membership_table as $index => $row ) {
			if ( $email === $row->user_email ) {
				return $row;
			}
		}

		return null;
	}
}
