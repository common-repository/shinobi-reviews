<?php
/**
 * Fetch the review of the current user.
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.5.8
 */

namespace Shinobi_Reviews\App;

use Shinobi_Reviews\App\Membership\SessionManager;
use Shinobi_Works\WP\DB;

class FetchOwnReview {

	public function __construct() {
		add_action( 'wp_ajax_ShinobiReviews/fetchReview', [ $this, 'fetch_review' ] );
		add_action( 'wp_ajax_nopriv_ShinobiReviews/fetchReview', [ $this, 'fetch_review' ] );
	}

	/**
	 * Return the value that is used for Ajax
	 *
	 * @return array
	 */
	public static function form_actions() {
		return [
			'fetchReview' => [
				'action' => 'ShinobiReviews/fetchReview',
				'nonce'  => wp_create_nonce( 'ShinobiReviews/fetchReview' ),
			],
		];
	}

	/**
	 * Ajax function of fetching review
	 *
	 * @return void
	 */
	public function fetch_review() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'ShinobiReviews/fetchReview' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$form_id = filter_input( INPUT_POST, 'formId', FILTER_VALIDATE_INT );
			$user_id = SessionManager::get_shinobi_membership_id();

			if ( $user_id ) { // Check if the user has already posted a review.
				$results = DB::get_results( SHINOBI_REVIEWS_LIST_TABLE, ARRAY_A );

				if ( $results ) {
					foreach ( $results as $index => $data ) {
						if ( intval( $data['review_form_id'] ) === $form_id ) {
							if ( (int) $data['user_id'] === $user_id ) {
								wp_send_json_success( $data );
							}
						}
					}
				}
			} else {
				wp_send_json_error( null );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

}
