<?php
/**
 * Generate Form Parts
 *
 * @package    Shinobi_Works
 * @subpackage Generate
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Works\Generate;

use Shinobi_Works\Converter;

/**
 * Form Parts Class
 */
class FormParts {

	const SHINOBI_FORM_PARTS = 'shinobi-form-parts';

	/**
	 * Input
	 *
	 * @param array $atts_arr is attribute of input tag.
	 * @return string
	 */
	public static function input( $new_atts_arr = [], $echo = false ) {
		$atts_arr = [
			'type'       => 'text',
			'data-class' => self::SHINOBI_FORM_PARTS . ' shinobi-input',
		];
		if ( is_array( $new_atts_arr ) && $new_atts_arr ) {
			$atts_arr = array_replace( $atts_arr, $new_atts_arr );
		}
		$atts  = Converter::get_attributes( $atts_arr );
		$input = "<input $atts>";
		if ( true === $echo ) {
			echo $input; // phpcs:ignore
		} else {
			return $input;
		}
	}

	/**
	 * Textarea
	 *
	 * @param array $atts_arr is textarea attributes.
	 * @return string
	 */
	public static function textarea( $new_atts_arr = [], $entered_text, $echo = false ) {
		$atts_arr = [
			'data-class' => self::SHINOBI_FORM_PARTS . ' shinobi-textarea',
		];
		if ( is_array( $new_atts_arr ) && $new_atts_arr ) {
			$atts_arr = array_replace( $atts_arr, $new_atts_arr );
		}
		$atts     = Converter::get_attributes( $atts_arr );
		$textarea = "<textarea $atts>$entered_text</textarea>";
		if ( $echo ) {
			echo $textarea; // phpcs:ignore
		} else {
			return $textarea;
		}
	}

	/**
	 * Selectbox
	 *
	 * @param string $name
	 * @param array  $option_atts
	 *
	 * @return string
	 */
	public static function selectbox( $name, $option_atts = [], $echo = false ) {
		$options = '';
		if ( is_array( $option_atts ) && $option_atts ) {
			foreach ( $option_atts as $atts ) {
				if ( ! isset( $atts['value'] ) ) {
					continue;
				}
				$value = $atts['value'];
				$label = isset( $atts['label'] ) && $atts['label'] ? $atts['label'] : $value;
				unset( $atts['value'] );
				unset( $atts['label'] );
				$atts = Converter::get_attributes( $atts );
				if ( $atts ) {
					$atts = " $atts";
				}
				$options .= "<option value=\"{$value}\"{$atts}>{$label}</option>";
			}
		}
		$select_atts_arr = [
			'name'       => $name,
			'data-class' => self::SHINOBI_FORM_PARTS . ' shinobi-selectbox',
		];
		$select_atts     = Converter::get_attributes( $select_atts_arr );
		$selectbox       = "<select $select_atts>$options</select>";
		if ( $echo ) {
			echo $selectbox; // phpcs:ignore
		} else {
			return $selectbox;
		}
	}

	/**
	 * Button
	 *
	 * @param string $button_text
	 * @param array  $atts_arr
	 * @return string
	 */
	public static function button( $button_text, $new_atts_arr = [], $echo = false ) {
		$atts_arr = [
			'type'       => 'button',
			'data-class' => self::SHINOBI_FORM_PARTS . ' shinobi-button',
		];
		if ( is_array( $new_atts_arr ) && $new_atts_arr ) {
			$atts_arr = array_replace( $atts_arr, $new_atts_arr );
		}
		$atts   = Converter::get_attributes( $atts_arr );
		$button = "<button $atts>$button_text</button>";
		if ( $echo ) {
			echo $button; // phpcs:ignore
		} else {
			return $button;
		}
	}

}
