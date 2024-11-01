<?php
/**
 * Aggregate Ratings
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

use Shinobi_Reviews\App\InsertReview;
use Shinobi_Reviews\App\Membership\Membership;
use Shinobi_Reviews\App\Membership\SessionManager;
use Shinobi_Reviews\App\Module\Recaptcha;
use Shinobi_Works\WP\DB;

class AggregateRatings {

	/**
	 * Render the shortcode
	 *
	 * @param array $shortcode_atts
	 * @return void
	 */
	public static function render( $shortcode_atts ) {
		$form_id = $shortcode_atts['id'];

		// get the all form data of Shinobi Reviews
		$all_structured_data = DB::get_option( 'shinobi_reviews_form_data' );
		if ( ! $all_structured_data ) {
			return '<p>' . __( 'Not found the shinobi-reviews form.', 'shinobi-reviews' ) . '</p>';
		}

		// get the form data of the specific form
		$structured_data = $all_structured_data[ $form_id ] ?? null;
		if ( ! $structured_data ) {
			return '<p>' . __( 'Not found the shinobi-reviews form.', 'shinobi-reviews' ) . '</p>';
		}

		$html = self::generate_element_with_props( $structured_data, $shortcode_atts );

		self::enqueue_script();

		return '<div class="ShinobiReviews">' . $html . '</div>';
	}

	/**
	 * Generate an element with props
	 *
	 * @param array $form_data
	 * @param array $shortcode_atts
	 * @return string
	 */
	private static function generate_element_with_props( $structured_data, $shortcode_atts ) {
		$form_id       = $shortcode_atts['id'];
		$is_membership = filter_var( $shortcode_atts['is_membership'], FILTER_VALIDATE_BOOLEAN );

		// get the form data of the specific form
		$review_data = self::process_review_data( $form_id );

		// Integratation of formData and aggregateRating
		if ( $review_data ) {
			$review_count = count( $review_data );

			$structured_data += [
				'aggregateRating' => [
					'@type'       => 'AggregateRating',
					'ratingValue' => round( array_sum( array_column( $review_data, 'rating' ) ) / $review_count, 1 ),
					'ratingCount' => $review_count,
					'reviewCount' => $review_count,
				],
			];
		}

		$props = [
			'reviews'            => $review_data,
			'structuredData'     => $structured_data,
			'formId'             => (int) $form_id,
			'locale'             => get_locale(),
			'signIn'             => [
				'token'  => DB::get_option( 'shinobiReviewsAuthToken' ) ? 'O9WJ9ruwbCaTQPy+fYyb5y0ywa7wMDt1mLKLpyysPyaDGILZhTgyWiceGKI5psUO' : '',
				'action' => 'sign_in_by_token',
				'nonce'  => wp_create_nonce( 'sign_in_by_token' ),
			],
			'initialReviewCount' => (int) DB::get_option( 'initialReviewCount', -1 ),
			'isMediaUploader'    => json_decode( DB::get_option( 'isMediaUploader', 'true' ) ),
			'formActions'        => self::form_actions( $is_membership ),
			'recaptcha'          => Recaptcha::form_actions(),
			'isMembership'       => Membership::is_membership() || $is_membership,
		];

		return '<div data-props="' . esc_attr( wp_json_encode( $props ) ) . '"></div><script type="application/ld+json">' . wp_json_encode( $structured_data ) . '</script>';
	}

	/**
	 * Get form actions
	 * @param bool $is_membership
	 *
	 * @return array
	 */
	private static function form_actions( $is_membership = false ) {
		$form_actions = InsertReview::form_actions();

		if ( Membership::is_membership() || $is_membership ) {
			$form_actions += Membership::form_actions();
		} else {
			SessionManager::shinobi_reviews_session_start();
			$_SESSION = [];
		}

		return $form_actions;
	}

	private static function enqueue_script() {
		$base_name         = 'shinobi-reviews-js/shinobi-reviews-js';
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
			SHINOBI_REVIEWS,
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
		wp_set_script_translations( SHINOBI_REVIEWS, 'shinobi-reviews', SHINOBI_REVIEWS_PLUGIN_DIR . 'languages' );
		wp_enqueue_script( 'shinobi-reviews/google-recaptcha' );
	}

	/**
	 * Process the review data for JS
	 *
	 * @return array
	 */
	private static function process_review_data( $form_id ) {
		$all_review_data = DB::get_results( SHINOBI_REVIEWS_LIST_TABLE );

		if ( ! $all_review_data ) {
			return [];
		}

		$review_data_of_specific_form = array_filter(
			$all_review_data,
			function( $review_data ) use ( $form_id ) {
				return $review_data->review_form_id === $form_id;
			}
		);

		if ( ! $review_data_of_specific_form ) {
			return [];
		}

		$filtered_review_data = array_reduce(
			$review_data_of_specific_form,
			function( $carry, $review_data ) {
				// if the review is not approved, do not show it
				if ( '0' === $review_data->review_approved ) {
					return $carry;
				}

				$user_data = DB::get_row_by_id( SHINOBI_MEMBERSHIP_TABLE, $review_data->user_id );

				// if the user is blocked, do not show it
				if ( $user_data && '0' === $user_data->user_status ) {
					return $carry;
				}

				$reviewer       = $user_data ? $user_data->user_nicename : $review_data->review_author;
				$date_time      = new \DateTime( $review_data->review_date, wp_timezone() );
				$attachment_ids = json_decode( $review_data->review_attachment_ids ?? "", true );
				if ( $attachment_ids && is_array( $attachment_ids ) ) {
					$images = array_map(
						function( $attachment_id ) {
							return wp_get_attachment_image_src( $attachment_id, 'large' )[0];
						},
						$attachment_ids
					);
				}
				$carry[] = [
					'id'       => $review_data->ID,
					'content'  => $review_data->review_content,
					'rating'   => (int) $review_data->review_rating,
					'reviewer' => $reviewer ? $reviewer : DB::get_option( 'defaultReviewerName', __( 'Unregistered user', 'shinobi-reviews' ) ),
					'isMember' => $user_data ? true : false,
					'images'   => $images ?? [],
					'date'     => $date_time->format( 'Y/m/d' ),
					'dateTime' => strtotime( $review_data->review_date ),
				];

				return $carry;
			},
			[]
		);

		if ( ! $filtered_review_data ) {
			return [];
		}

		// Sort by date.
		array_multisort( array_column( $filtered_review_data, 'dateTime' ), SORT_DESC, $filtered_review_data );

		return array_map(
			function( $review ) {
				// Remove unnecessary data.
				unset( $review['dateTime'] );
				return $review;
			},
			$filtered_review_data
		);
	}
}
