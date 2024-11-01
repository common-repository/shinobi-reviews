<?php
/**
 * Add Shortcode
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Reviews\App\Shortcode;

class AddShortcode {

	public function __construct() {
		add_shortcode( SHINOBI_REVIEWS, [ $this, 'shortcode' ] );
		add_action( 'init', [ $this, 'define_global_variables' ], 10 );
	}

	/**
	 * Shortcode Setting
	 *
	 * @param array $raw_atts raw attribute of shortcode.
	 * @link https://codex.wordpress.org/Shortcode_API/
	 * @return string
	 */
	public function shortcode( $raw_atts ) {
		if ( is_admin() ) {
			return '';
		}

		$atts = shortcode_atts(
			[
				'id'            => null,
				'is_membership' => false,
				'ids'           => null,
			],
			$raw_atts,
			SHINOBI_REVIEWS
		);

		if ( $atts['id'] ) {
			return AggregateRatings::render( $atts );
		}

		if ( $atts['ids'] ) {
			return FormGroup::render( $atts );
		}
	}

	/**
	 * Define handler for global variables
	 *
	 * @return string
	 */
	private static function define_handle() {
		$handle = SHINOBI_REVIEWS . '-global-variables';
		wp_register_script( $handle, false, [], SHINOBI_REVIEWS_VERSION, true );
		wp_enqueue_script( $handle );
		return $handle;
	}

	/**
	 * Define global variables
	 */
	public function define_global_variables() {
		$handle = self::define_handle();

		wp_add_inline_script( $handle, 'const shinobiReviewsAjaxUrl = "' . admin_url( 'admin-ajax.php' ) . '";', 'before' );
	}
}
