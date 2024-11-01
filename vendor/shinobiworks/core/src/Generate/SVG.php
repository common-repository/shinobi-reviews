<?php
/**
 * Generate SVG
 *
 * @package    Shinobi_Works
 * @subpackage Generate
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Works\Generate;

class SVG {

	/**
	 * Rating Star
	 *
	 * @param string $color
	 * @param string $rating_float
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/
	 * @return string
	 */
	public static function rating_star( $width = 24, $height = 24, $rating_float = 0, $color = 'none' ) {
		$float = $rating_float * 100;
		if ( 0 !== $rating_float ) {
			$gradient = <<< SVG1
<defs>
    <linearGradient id="gradient">
        <stop offset="0%" stop-color="{$color}" />
        <stop offset="{$float}%" stop-color="{$color}" />
        <stop offset="{$float}%" stop-color="#cccccc" />
    </linearGradient>
</defs>
SVG1;
			$fill     = 'url(#gradient)';
		} else {
			$gradient = '';
			$fill     = 'none' !== $color ? $color : '#cccccc';
		}
		$star = <<< SVG2
<svg xmlns="http://www.w3.org/2000/svg"
    width="{$width}" height="{$height}" viewBox="0 0 24 24" class="star">{$gradient}
    <path d="M12 17.27L18.18
    21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
        stroke="none" fill="{$fill}" class="star-color" />
</svg>
SVG2;
		return $star;
	}

	/**
	 * Down Arrow
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=arrow_drop_down&style=baseline
	 * @return string
	 */
	public static function arrow_drop_down( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /><path d="M0 0h24v24H0z" fill="{$fill}" /></svg>
SVG;
	}

	/**
	 * File Copy
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=file_copy&style=baseline
	 * @return string
	 */
	public static function file_copy( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path fill="{$fill}" d="M0 0h24v24H0z" /><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm-1 4l6 6v10c0 1.1-.9 2-2 2H7.99C6.89 23 6 22.1 6 21l.01-14c0-1.1.89-2 1.99-2h7zm-1 7h5.5L14 6.5V12z" /></svg>
SVG;
	}

	/**
	 * Account Circle
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=account_circle&style=baseline
	 * @return string
	 */
	public static function account_circle( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" /><path d="M0 0h24v24H0z" fill="{$fill}" /></svg>
SVG;
	}

	/**
	 * Face
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=face&style=baseline
	 * @return string
	 */
	public static function face( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M9 11.75c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zm6 0c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-.29.02-.58.05-.86 2.36-1.05 4.23-2.98 5.21-5.37C11.07 8.33 14.05 10 17.42 10c.78 0 1.53-.09 2.25-.26.21.71.33 1.47.33 2.26 0 4.41-3.59 8-8 8z" /><path fill="{$fill}" d="M0 0h24v24H0z" /></svg>
SVG;
	}

	/**
	 * Email
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=email&style=baseline
	 * @return string
	 */
	public static function email( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path fill="{$fill}" d="M0 0h24v24H0z" /><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z" /></svg>
SVG;
	}

	/**
	 * Close
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=close&style=baseline
	 * @return string
	 */
	public static function close( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" /><path d="M0 0h24v24H0z" fill="{$fill}" /></svg>
SVG;
	}

	/**
	 * Lock
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=lock&style=baseline
	 * @return string
	 */
	public static function lock( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="{$fill}" /><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" /></svg>
SVG;
	}

	/**
	 * Edit
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=edit&style=baseline
	 * @return string
	 */
	public static function edit( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" /><path d="M0 0h24v24H0z" fill="{$fill}" /></svg>
SVG;
	}

	/**
	 * Sort
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=sort&style=baseline
	 * @return string
	 */
	public static function sort( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z" /><path d="M0 0h24v24H0z" fill="{$fill}" /></svg>
SVG;
	}

	/**
	 * Help
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=help_outline&style=baseline
	 * @return string
	 */
	public static function help( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path fill="{$fill}" d="M0 0h24v24H0z" /><path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z" /></svg>
SVG;
	}

	/**
	 * Info
	 *
	 * @param string $fill
	 * @param integer $width
	 * @param integer $height
	 * @link https://material.io/resources/icons/?icon=info&style=baseline
	 * @return string
	 */
	public static function info( $width = 24, $height = 24, $fill = 'none' ) {
		return <<< SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="{$fill}" /><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
SVG;
	}
}
