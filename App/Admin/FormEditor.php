<?php
/**
 * Form editor
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

use Shinobi_Works\WP\DB;

/**
 * Form editor class
 */
class FormEditor {

	public function __construct() {
		add_action( 'wp_ajax_save_form', [ $this, 'save_form' ] );
		add_action( 'wp_ajax_save_form_group', [ $this, 'save_form_group' ] );
	}

	/**
	 * Save the form data with Ajax
	 *
	 * @return void
	 */
	public function save_form() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'save_form' ) ) {
			$data      = json_decode( filter_input( INPUT_POST, 'data' ), true );
			$form_data = $data['formData'];
			$last_id   = $data['lastId'];

			if ( $form_data ) {
				DB::update_option( 'shinobi_reviews_form_data', $form_data );
			} else {
				DB::delete_option( 'shinobi_reviews_form_data' );
			}

			if ( $last_id ) {
				DB::update_option( 'shinobi_reviews_form_last_id', $last_id );
			}

			wp_send_json_success();
		}

		wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
	}

	public function save_form_group() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'save_form' ) ) {
			$form_group = json_decode( filter_input( INPUT_POST, 'formGroup' ), true );
			if ( $form_group ) {
				DB::update_option( 'shinobiReviewsFormGroup', $form_group );
				wp_send_json_success( __( 'Successfully saved!!', 'shinobi-reviews' ) );
			} else {
				wp_send_json_error( __( 'Failed to save', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

}
