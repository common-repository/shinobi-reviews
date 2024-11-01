<?php
/**
 * Admin menu
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.3.0
 */

namespace Shinobi_Reviews\App\Admin;

use Shinobi_Reviews\App\Membership\Membership;
use Shinobi_Works\WP\DB;

/**
 * Menu class
 */
class Menu {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
		add_action( 'admin_menu', [ $this, 'add_submenu_page' ] );
	}

	/**
	 * Admin Files
	 */
	public function add_menu_page() {
		add_menu_page(
			__( 'Review', 'shinobi-reviews' ),
			__( 'Review', 'shinobi-reviews' ),
			'manage_options',
			SHINOBI_REVIEWS,
			'',
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgOTAgODUuOTIiPjxkZWZzPjxjbGlwUGF0aCBpZD0iYSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTUgLTcuMDQpIj48cG9seWdvbiBwb2ludHM9IjUuMTcgMzkuOTIgMTIuNjggNDIuNTUgNDAuODcgNDIuMyAzOS4xIDM5LjU5IDUuMTcgMzkuOTIiIGZpbGw9Im5vbmUiLz48L2NsaXBQYXRoPjxjbGlwUGF0aCBpZD0iYiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTUgLTcuMDQpIj48cG9seWdvbiBwb2ludHM9IjUwIDcuMTcgNTAgMTUuNjMgNTguOTkgNDIuMzEgNjAuNzMgMzkuNjEgNTAgNy4xNyIgZmlsbD0ibm9uZSIvPjwvY2xpcFBhdGg+PGNsaXBQYXRoIGlkPSJjIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNSAtNy4wNCkiPjxwb2x5Z29uIHBvaW50cz0iNjQuNDUgNTkuMTkgODcuMyA0Mi42MiA5NC43OCA0MC4xMyA2Ny40MiA2MC4xOCA2NC40NSA1OS4xOSIgZmlsbD0ibm9uZSIvPjwvY2xpcFBhdGg+PGNsaXBQYXRoIGlkPSJkIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNSAtNy4wNCkiPjxwb2x5Z29uIHBvaW50cz0iNTAuMTYgNjkuNTMgNzMuMDEgODUuODcgNzcuODkgOTIuOTIgNTAuMTMgNzIuODEgNTAuMTYgNjkuNTMiIGZpbGw9Im5vbmUiLz48L2NsaXBQYXRoPjxjbGlwUGF0aCBpZD0iZSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTUgLTcuMDQpIj48cG9seWdvbiBwb2ludHM9IjI2Ljk1IDg1Ljg1IDIyLjE1IDkyLjc4IDMyLjU2IDYwLjA2IDM1LjUyIDU5LjEyIDI2Ljk1IDg1Ljg1IiBmaWxsPSJub25lIi8+PC9jbGlwUGF0aD48Y2xpcFBhdGggaWQ9ImYiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC01IC03LjA0KSI+PHBhdGggZD0iTTQzLjUyLDUzLjUyYTYuNjYsNi42NiwwLDEsMSw2LjY2LDYuNjZBNi42Niw2LjY2LDAsMCwxLDQzLjUyLDUzLjUyWk01MC4xMyw1OWE1LjY3LDUuNjcsMCwwLDAsNS43My01LjYsNS43NCw1Ljc0LDAsMCwwLTExLjQ2LDBBNS42Niw1LjY2LDAsMCwwLDUwLjEzLDU5WiIgZmlsbD0ibm9uZSIvPjwvY2xpcFBhdGg+PC9kZWZzPjxwYXRoIGQ9Ik02MC43NSwzOS43MSw5NSw0MCw2Ny4zMSw2MC4xOSw3OCw5Myw1MC4wOSw3Mi43M2wtMjcuOTMsMjBMMzIuNjIsNjAsNSwzOS45MWwzNC4xMi0uMzFMNTAsN1pNNTAuMTMsNTlhNS41Miw1LjUyLDAsMSwwLTUuNzMtNS41MkE1LjYzLDUuNjMsMCwwLDAsNTAuMTMsNTlaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNSAtNy4wNCkiIGZpbGw9IiNmZmYyOGUiLz48cGF0aCBkPSJNNTguOTMsNDIuMzdsMjguNDMuMjctMjMsMTYuNTQsOC44NSwyNi44OEw1MC4wOCw2OS40NiwyNi44OSw4NS44OWw4LjY4LTI2Ljg0TDEyLjY0LDQyLjU0LDQxLDQyLjI4bDktMjYuNzFaTTUwLjEzLDU5YTUuNTIsNS41MiwwLDEsMC01LjczLTUuNTJBNS42Myw1LjYzLDAsMCwwLDUwLjEzLDU5WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTUgLTcuMDQpIiBmaWxsPSIjZmVkNjAwIi8+PC9zdmc+',
			4
		);
	}

	/**
	 * Add Menu Page in wp-admin.php
	 */
	public function add_submenu_page() {
		$hook = add_submenu_page(
			SHINOBI_REVIEWS,
			__( 'Dashboard', 'shinobi-reviews' ),
			__( 'Dashboard', 'shinobi-reviews' ),
			'manage_options',
			SHINOBI_REVIEWS,
			function() {
				echo '<div id="shinobi-reviews" style="clear:both;"></div>';
			}
		);

		// When the shinobi reviews menu is added, run the function.
		add_action( "load-$hook", [ $this, 'bootstrap' ] );
	}

	/**
	 * Bootstrap
	 */
	public function bootstrap() {
		$this->admin_enqueue_scripts();
	}

	/**
	 * Admin enqueue scripts
	 */
	public function admin_enqueue_scripts() {
		$base_name         = 'admin/admin';
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
		wp_add_inline_script(
			SHINOBI_REVIEWS,
			'const shinobiReviewsAdmin = ' . wp_json_encode(
				[
					'formEditor'     => [
						'data'      => DB::get_option( 'shinobi_reviews_form_data', null ),
						'lastId'    => DB::get_option( 'shinobi_reviews_form_last_id', '1' ),
						'nonce'     => wp_create_nonce( 'save_form' ),
						'formGroup' => DB::get_option( 'shinobiReviewsFormGroup', null ),
					],
					'reviewEditor'   => [
						'data'  => $this->process_review_data(),
						'nonce' => wp_create_nonce( 'review_editor' ),
					],
					'reviewerEditor' => [
						'data'  => $this->process_reviewer_data(),
						'nonce' => wp_create_nonce( 'reviewer_editor' ),
					],
					'settingsEditor' => [
						'data'  => [
							'isMembership'                  => Membership::is_membership(),
							'isMediaUploader'               => json_decode( DB::get_option( 'isMediaUploader', 'true' ) ),
							'defaultReviewerName'           => DB::get_option( 'defaultReviewerName', __( 'Unregistered user', 'shinobi-reviews' ) ),
							'initialReviewCount'            => DB::get_option( 'initialReviewCount', -1 ),
							'initialReviewApproval'         => DB::get_option( 'initialReviewApproval', 0 ),
							'recaptchaSiteKey'              => DB::get_option( 'recaptchaSiteKey', DB::get_option( 'recaptcha_site_key', '' ) ),
							'recaptchaSecretKey'            => DB::get_option( 'recaptchaSecretKey', DB::get_option( 'recaptcha_secret_key', '' ) ),
							'recaptchaVerificationStrength' => DB::get_option( 'recaptchaVerificationStrength', DB::get_option( 'recaptcha_verification_strength', '50' ) ),
						],
						'nonce' => wp_create_nonce( 'settings_editor' ),
					],
					'auth'           => [
						'fetchUserData' => [
							'action' => 'fetch_auth_user_data',
							'nonce'  => wp_create_nonce( 'fetch_auth_user_data' ),
						],
						'saveUserData'  => [
							'action' => 'save_auth_user_data',
							'nonce'  => wp_create_nonce( 'save_auth_user_data' ),
						],
						'signIn'        => [
							'token'  => DB::get_option( 'shinobiReviewsAuthToken', '' ),
							'action' => 'sign_in_by_token',
							'nonce'  => wp_create_nonce( 'sign_in_by_token' ),
						],
					],
					'posts'  => $this->process_posts_data(),
					'locale' => get_locale(),
				]
			),
			'before'
		);
		wp_enqueue_media();
	}

	private function process_review_data() {
		$review_data     = DB::get_results( SHINOBI_REVIEWS_LIST_TABLE );
		$review_data_obj = [];

		// If there is review data, process it.
		if ( $review_data && is_array( $review_data ) ) {
			foreach ( $review_data as $index => $data ) {
				$attachmen_ids                 = json_decode( $data->review_attachment_ids ?? '' );
				$date_time                     = new \DateTime( $data->review_date, wp_timezone() );
				$data->review_date_for_sorting = strtotime( $data->review_date );
				$data->review_date             = $date_time->format( get_option( 'date_format' ) );

				// If data has attachment ids, get its image src.
				if ( $attachmen_ids && is_array( $attachmen_ids ) ) {
					$data->review_attachment_ids = $attachmen_ids;

					foreach ( $attachmen_ids as $index => $id ) {
						$media_data = wp_get_attachment_image_src( $id, 'medium' );

						if ( $media_data ) {
							$data->attachment_media[ $id ] = $media_data[0];
						}
					}
				}

				$review_data_obj[] = $data;
			}

			// Sort by date.
			array_multisort( array_column( $review_data_obj, 'review_date_for_sorting' ), SORT_DESC, $review_data_obj );
		}

		return $review_data_obj;
	}

	private function process_posts_data() {
		$posts = get_posts(
			[
				'numberposts' => -1,
				'post_type'   => [
					'post',
					'page',
				],
			]
		);

		return array_map(
			function( $post ) {
				return [
					'ID'         => $post->ID,
					'post_title' => $post->post_title,
					'post_url'   => get_permalink( $post->ID ),
				];
			},
			$posts
		);
	}

	private function process_reviewer_data() {
		$reviewer_data     = DB::get_results( SHINOBI_MEMBERSHIP_TABLE );
		$reviewer_data_obj = null;

		if ( $reviewer_data && is_array( $reviewer_data ) ) {
			$reviewer_data_obj = [];

			foreach ( $reviewer_data as $index => $data ) {
				$date_time             = new \DateTime( $data->user_registered, wp_timezone() );
				$data->user_registered = $date_time->format( get_option( 'date_format' ) );

				unset( $data->user_pass );

				$reviewer_data_obj[ $data->ID ] = $data;
			}
		}

		return $reviewer_data_obj;
	}
}
