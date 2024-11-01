<?php
/**
 * Converter
 *
 * @package    Shinobi_Works
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Works;

/**
 * Converter Class
 */
class Converter {

	/**
	 * Trim for multi byte string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function mb_trim( $string ) {
		return preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $string );
	}


	/**
	 * Get attributes from array
	 *
	 * @param array $atts_array attribute array
	 * @param string $old_atts old attribute
	 * @return string|false
	 */
	public static function get_attributes( $atts_array = [], $old_atts = '' ) {
		if ( ! is_array( $atts_array ) || ! $atts_array ) {
			return false;
		}
		$atts = '';
		$max  = count( $atts_array );
		$i    = 0;
		foreach ( $atts_array as $key => $value ) {
			++$i;
			$space = $i === $max ? '' : ' ';
			$key   = htmlspecialchars( $key );
			$value = htmlspecialchars( $value );
			$atts .= "{$key}=\"{$value}\"{$space}";
		}
		if ( $old_atts && is_string( $old_atts ) ) {
			$atts .= " $old_atts";
		}
		return $atts;
	}

}
