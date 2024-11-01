<?php
/**
 * Post
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Reviews\App;

use Shinobi_Reviews\App\Membership\MailFactory;
use Shinobi_Reviews\App\Membership\SessionManager;
use Shinobi_Works\WP\DB;

/**
 * Main Class
 */
class InsertReview {

	public function __construct() {
		add_action( 'wp_ajax_post_review', [ $this, 'post_review' ] );
		add_action( 'wp_ajax_nopriv_post_review', [ $this, 'post_review' ] );
		add_action( 'wp_ajax_post_media', [ $this, 'post_media' ] );
		add_action( 'wp_ajax_nopriv_post_media', [ $this, 'post_media' ] );
	}

	/**
	 * Prepare a data for JS
	 *
	 * @return array
	 */
	public static function form_actions() {
		return [
			'post' => [
				'review' => [
					'action' => 'post_review',
					'nonce'  => wp_create_nonce( 'post_review' ),
				],
				'media'  => [
					'action' => 'post_media',
					'nonce'  => wp_create_nonce( 'post_media' ),
				],
			],
		];
	}

	/**
	 * Ajax function of posting review
	 *
	 * @return void
	 */
	public function post_review() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'post_review' ) ) {
			SessionManager::shinobi_reviews_session_start();

			$form_id        = filter_input( INPUT_POST, 'formId', FILTER_VALIDATE_INT );
			$user_id        = SessionManager::get_shinobi_membership_id();
			$attachment_ids = filter_input( INPUT_POST, 'attachmentIds' );
			$review_rating  = filter_input( INPUT_POST, 'rating' );
			$review_content = filter_input( INPUT_POST, 'content' );
			$insert_arr     = [
				'review_rating'         => $review_rating,
				'review_content'        => $review_content,
				'review_post_id'        => url_to_postid( filter_input( INPUT_SERVER, 'HTTP_REFERER' ) ),
				'review_approved'       => DB::get_option( 'initialReviewApproval', 0 ),
				'review_form_id'        => $form_id,
				'review_date'           => current_time( 'mysql' ),
				'review_author'         => filter_input( INPUT_POST, 'name' ),
				'user_id'               => $user_id,
				'review_attachment_ids' => $attachment_ids,
			];

			if ( $user_id > 0 ) {
				$user_data = DB::get_row_by_id( SHINOBI_MEMBERSHIP_TABLE, $user_id );

				if ( $user_data ) {
					// Check if the user has already posted a review, if so, update it.
					$results = DB::get_results( SHINOBI_REVIEWS_LIST_TABLE, ARRAY_A );
					if ( $results ) {
						foreach ( $results as $index => $data ) {
							if ( intval( $data['review_form_id'] ) === $form_id ) {
								if ( (int) $data['user_id'] === $user_id ) {
									// if this review has attachments, delete them.
									self::delete_attachments( json_decode( $data['review_attachment_ids'], true ) );
									// update the review.
									$is_review_updated = DB::update(
										SHINOBI_REVIEWS_LIST_TABLE,
										$insert_arr,
										[ 'ID' => $data['ID'] ]
									);
									if ( $is_review_updated ) {
										$is_mail_sent_to_reviewer = wp_mail(
											$user_data->user_email,
											__( 'Your review was successfully updated!', 'shinobi-reviews' ) . ' - ' . get_bloginfo( 'name' ),
											MailFactory::get_review_updated_message( $user_data->user_nicename )
										);
										Helper::notice_to_admin(
											__( 'An existing review was updated!', 'shinobi-reviews' ) . ' by Shinobi Reviews',
											[
												__( 'The reviewer who posted a review before has updated the review.', 'shinobi-reviews' ) . ' ' .
												__( 'The content of the review is here.', 'shinobi-reviews' ),
												'------------',
												$review_content,
												'------------',
												__( 'Since this review was updated by a reviewer you need to check and approve it.', 'shinobi-reviews' ),
											]
										);

										wp_send_json_success( __( 'Succeeded to update the review!', 'shinobi-reviews' ) );
									} else {
										Helper::notice_to_admin(
											__( 'Failed to update an existing review.', 'shinobi-reviews' ) . ' by Shinobi Reviews',
											[
												__( 'Failed to update an existing review because an unexpected error has occurred.', 'shinobi-reviews' ),
											]
										);

										wp_send_json_error( __( 'Failed to update an existing review.', 'shinobi-reviews' ) );
									}
								}
							}
						}
					}

					// Insert the review.
					$is_review_inserted = DB::insert( SHINOBI_REVIEWS_LIST_TABLE, $insert_arr );

					if ( $is_review_inserted ) {
						// Send mail to reviewer about posted review.
						$is_mail_sent_to_reviewer = wp_mail(
							$user_data->user_email,
							__( 'Your review was successfully posted!', 'shinobi-reviews' ) . ' - ' . get_bloginfo( 'name' ),
							MailFactory::get_review_posting_done_message( $user_data->user_nicename )
						);
						// Update the review count.
						$is_reviewer_updated = DB::update(
							SHINOBI_MEMBERSHIP_TABLE,
							[ 'review_count' => ++$user_data->review_count ],
							[ 'ID' => $user_id ]
						);

						// If a review count was not updated, notice to admin about it.
						if ( ! $is_reviewer_updated ) {
							Helper::notice_to_admin(
								__( 'Failed to update a review count.', 'shinobi-reviews' ) . ' by Shinobi Reviews',
								[
									__( 'Failed to update a review count because an unexpected error has occurred.', 'shinobi-reviews' ),
								]
							);
						}
					}

					if ( isset( $is_mail_sent_to_reviewer ) && ! $is_mail_sent_to_reviewer ) {
						Helper::notice_to_admin(
							// translators:レビュアーへのメール送信に失敗しました
							__( 'Failed to send a mail to a reviewer', 'shinobi-reviews' ) . ' By Shinobi Reviews',
							[
								// translators:予期せぬエラーが発生したため、レビュアーへのメール送信に失敗しました。
								__( 'Failed to send a mail to a reviewer because an unexpected error has occurred.', 'shinobi-reviews' ),
							]
						);
					}
				}
			} else {
				// Insert the review data.
				$is_review_inserted = DB::insert( SHINOBI_REVIEWS_LIST_TABLE, $insert_arr );
			}

			if ( isset( $is_review_inserted ) && $is_review_inserted ) {
				// Send mail to administer about posted review.
				Helper::notice_to_admin(
					__( 'New review posted!', 'shinobi-reviews' ) . ' by Shinobi Reviews', // Subject
					[
						// translators:新しいレビューが投稿されました！
						__( 'New review posted!', 'shinobi-reviews' ) . ' ' .
						// translators:以下のURLをクリックし、内容をご確認ください。
						__( 'Click the following URL to check the review contents.', 'shinobi-reviews' ),
						get_admin_url() . 'admin.php?page=' . SHINOBI_REVIEWS,
						'------------',
						// translators:このメールはShinobi Reviewsから送信されました
						__( 'This e-mail was sent from Shinobi Reviews', 'shinobi-reviews' ),
					]
				);

				wp_send_json_success( __( 'Succeeded to post your review!', 'shinobi-reviews' ) );
			} else {
				wp_send_json_error( __( 'Failed to save a review.', 'shinobi-reviews' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

	/**
	 * Ajax function of posting media
	 *
	 * @return void
	 */
	public function post_media() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'post_media' ) ) {
			$files = $_FILES;

			if ( ! $files || count( $files ) > 3 ) {
				wp_send_json_error( __( 'Not found a media file.', 'shinobi-reviews' ) );
			}

			$attachment_ids = [];

			foreach ( $files as $key => $file ) {
				if ( $file['size'] > 10000000 ) {
					wp_send_json_error( __( 'The size of uploaded file exceeds the limit.', 'shinobi-reviews' ) );
				}

				$uploaded_file = wp_handle_upload( $file, [ 'test_form' => false ] );

				if ( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
					$url         = $uploaded_file['url'];
					$keys        = wp_parse_url( $url );
					$path        = explode( '/', $keys['path'] );
					$file_name   = end( $path );
					$attachment  = [
						'guid'           => $url,
						'post_mime_type' => $uploaded_file['type'],
						'post_title'     => $file_name,
						'post_content'   => '',
						'post_status'    => 'inherit',
					];
					$attach_id   = wp_insert_attachment(
						$attachment,
						$uploaded_file['file'],
						url_to_postid( filter_input( INPUT_POST, 'post_url' ) )
					);
					$attach_data = wp_generate_attachment_metadata(
						$attach_id,
						$uploaded_file['file']
					);

					wp_update_attachment_metadata( $attach_id, $attach_data );

					$attachment_ids[] = $attach_id;
				} else {
					wp_send_json_error( __( 'An unexpected error has occurred.', 'shinobi-reviews' ) );
				}
			}

			wp_send_json_success( $attachment_ids );
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}

	/**
	 * Delete old attachments before updating a review
	 *
	 * @param array|null $attachment_ids
	 * @return void
	 */
	private static function delete_attachments( $attachment_ids ) {
		if ( is_array( $attachment_ids ) && count( $attachment_ids ) > 0 ) {
			foreach ( $attachment_ids as $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}
		}
	}

}
