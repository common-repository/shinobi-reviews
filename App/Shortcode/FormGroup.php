<?php
/**
 * Form Group
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.4.8
 */

namespace Shinobi_Reviews\App\Shortcode;

use Shinobi_Works\WP\DB;

class FormGroup {

	public static function render( $atts ) {
		$all_form_data = DB::get_option( 'shinobi_reviews_form_data' );

		if ( ! $all_form_data ) {
			return '<p>' . __( 'Not found the shinobi-reviews form.', 'shinobi-reviews' ) . '</p>';
		}

		$ids  = is_array( $atts['ids'] ) ? $atts['ids'] : explode( ',', $atts['ids'] );
		$body = null;

		foreach ( $ids as $index => $form_id ) {
			if ( isset( $all_form_data[ $form_id ] ) ) {
				$body .= self::generate_form( $all_form_data[ $form_id ], $form_id );
			}
		}

		if ( ! $body ) {
			return '<p>' . __( 'Not found the shinobi-reviews form.', 'shinobi-reviews' ) . '</p>';
		}

		self::enqueue_script();

		return '<div class="ShinobiReviews-FormGroup">' . $body . '</div>';
	}

	private static function generate_form( $form_data, $form_id ) {
		$all_review_data = DB::get_results( SHINOBI_REVIEWS_LIST_TABLE );
		$review_count    = 0;
		$ratings         = [];
		$post_id         = -1;

		if ( $all_review_data ) {
			foreach ( $all_review_data as $index => $review_data ) {
				if ( $review_data->review_form_id === $form_id && (int) $review_data->review_approved ) {
					$ratings[] = (int) $review_data->review_rating;
					$_post_id  = (int) $review_data->review_post_id;

					if ( $_post_id ) {
						$post_id = $_post_id;
					}
				}
			}
			$review_count = count( $ratings );
		}

		$props = [
			'name'             => $form_data['name'],
			'reviewCount'      => $review_count,
			'aggregateRatings' => $review_count ? round( array_sum( $ratings ) / $review_count, 1 ) : null,
			'src'              => self::get_image( $form_data ),
			'postedBy'         => get_permalink( $form_data['postId'] ? $form_data['postId'] : $post_id ),
		];

		return '<div data-props="' . esc_attr( wp_json_encode( $props ) ) . '"></div>';
	}

	private static function get_image( $form_data ) {
		if ( $form_data['image'] ) {
			$src = $form_data['image'][0];

			foreach ( $form_data['image'] as $key => $image ) {
					// Check if the image exists in the media library.
					if ( attachment_url_to_postid( $image ) ) {
							$info   = getimagesize( $image );
							$width  = $info[0];
							$height = $info[1];
							if ( $width === $height ) {
									$src = $image;
							}
					}
			}
		} else {
			$src = mb_substr( $form_data['name'], 0, 1 );
		}

		return $src;
	}

	private static function enqueue_script() {
		$base_name         = 'form-group/form-group';
		$script_url        = SHINOBI_REVIEWS_ASSETS_URL . "$base_name.js";
		$script_path       = SHINOBI_REVIEWS_ASSETS_DIR . "$base_name.js";
		$script_asset_path = SHINOBI_REVIEWS_ASSETS_DIR . "$base_name.asset.php";
		$script_asset      = file_exists( $script_asset_path )
		? require $script_asset_path
		: [
			'dependencies' => [],
			'version'      => filemtime( $script_path ),
		];

		wp_enqueue_script(
			'ShinobiReviews-FormGroup',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}
}
