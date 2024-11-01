<?php
/**
 * Utilities Functions for WordPress
 *
 * @package    Shinobi_Works\WP
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Works\WP;

/**
 * Utils Class
 */
class Utils {

	/**
	 * Echo the SVG after escaping.
	 *
	 * @param string $svg
	 * @return void
	 */
	public static function esc_svg_e( $svg ) {
		echo self::esc_svg( $svg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Escape SVG
	 *
	 * @param string $svg
	 * @return string
	 */
	public static function esc_svg( $svg ) {
		return wp_kses(
			$svg,
			[
				'svg'            => [
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true,
				],
				'defs'           => true,
				'lineargradient' => [
					'id' => true,
				],
				'stop'           => [
					'offset'     => true,
					'stop-color' => true,
				],
				'g'              => [ 'fill' => true ],
				'title'          => [ 'title' => true ],
				'path'           => [
					'd'    => true,
					'fill' => true,
				],
				'circle'         => [
					'cx' => true,
					'cy' => true,
					'r'  => true,
				],
			]
		);
	}

}
