<?php
/**
 * Review editor
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

class ReviewEditor {

	public function __construct() {
		add_action( 'wp_ajax_update_content', [ $this, 'update_content' ] );
		add_action( 'wp_ajax_update_approval', [ $this, 'update_approval' ] );
		add_action( 'wp_ajax_delete_media', [ $this, 'delete_media' ] );
		add_action( 'wp_ajax_delete_review', [ $this, 'delete_review' ] );
	}

	public function update_content() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'review_editor' ) ) {
			$review_content = filter_input( INPUT_POST, 'review_content' );
			$review_author  = filter_input( INPUT_POST, 'review_author' );
			$id             = filter_input( INPUT_POST, 'id' );

			if ( $review_content ) {
				$update_data = [ 'review_content' => $review_content ];

				if ( $review_author ) {
					$update_data += [ 'review_author' => $review_author ];
				}

				DB::update(
					SHINOBI_REVIEWS_LIST_TABLE,
					$update_data,
					[ 'ID' => $id ]
				);

				wp_send_json_success();
			} else {
				wp_send_json_error(
					// translators:レビューの更新に失敗しました
					__( 'Failed to update the review', 'shinobi-reviews' )
				);
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

	public function update_approval() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'review_editor' ) ) {
			$review_approved = filter_input( INPUT_POST, 'reviewApproved' );
			$id              = filter_input( INPUT_POST, 'id' );

			DB::update( SHINOBI_REVIEWS_LIST_TABLE, [ 'review_approved' => $review_approved ], [ 'ID' => $id ] );

			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

	public function delete_media() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'review_editor' ) ) {
			$review_id            = filter_input( INPUT_POST, 'reviewId' );
			$delete_attachment_id = filter_input( INPUT_POST, 'attachmentId', FILTER_VALIDATE_INT );

			if ( wp_delete_attachment( $delete_attachment_id ) ) {
				$attachment_ids = json_decode( DB::get_row_by_id( SHINOBI_REVIEWS_LIST_TABLE, $review_id )->review_attachment_ids );

				unset( $attachment_ids[ array_search( $delete_attachment_id, $attachment_ids, true ) ] );

				DB::update(
					SHINOBI_REVIEWS_LIST_TABLE,
					[ 'review_attachment_ids' => $attachment_ids ? wp_json_encode( array_values( $attachment_ids ) ) : null ],
					[ 'ID' => $review_id ]
				);

				wp_send_json_success();
			} else {
				wp_send_json_error( __( 'Failed to delete the media', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request', 'shinobi-reviews' ) );
		}
	}

	public function delete_review() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'review_editor' ) ) {
			$id = filter_input( INPUT_POST, 'id' );

			DB::delete( SHINOBI_REVIEWS_LIST_TABLE, [ 'ID' => $id ] );

			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}
}
