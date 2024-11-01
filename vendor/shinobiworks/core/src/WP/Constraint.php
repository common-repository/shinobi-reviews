<?php
/**
 * Constraint
 *
 * @package    Shinobi_Works
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Works\WP;

use Shinobi_Works\Converter;

/**
 * Constraint Class
 */
class Constraint {

	/**
	 * Check if pure text exists
	 *
	 * @param string $string
	 * @return string
	 */
	public static function is_text( $string ) {
		return Converter::mb_trim( wp_strip_all_tags( $string ) );
	}

}
