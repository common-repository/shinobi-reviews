<?php
/**
 * Is this an api system? No way!
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com
 * @since      1.3.8
 */

namespace Shinobi_Reviews\App\Module;

use Shinobi_Reviews\App\Helper;
use Shinobi_Works\WP\DB;

class ApiLike {

	public function __construct() {
		add_action( 'wp_ajax_fetch_auth_user_data', [ $this, 'fetch_auth_user_data' ] );
		add_action( 'wp_ajax_save_auth_user_data', [ $this, 'save_auth_user_data' ] );
		add_action( 'add_option_shinobiReviewsVersion', [ $this, 'reset_token' ] );
		add_action( 'update_option_shinobiReviewsVersion', [ $this, 'reset_token' ] );
	}

	/**
	 * Authenticate user
	 *
	 * @return void
	 */
	public function fetch_auth_user_data() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'fetch_auth_user_data' ) ) {
			$posted_data = json_decode( filter_input( INPUT_POST, 'data' ), true );

			foreach ( [ 'note' ] as $_i => $_key ) {
				$service = $posted_data[ $_key ];
				$email   = $service['email'];
				$token   = $service['token'];

				if ( $service && $email && $token ) {
					$index = array_search( $email, array_column( $this->dummy_data_base, 'user_email' ), true );

					$data = $this->dummy_data_base[ $index ];

					if ( $data ) {
						$decrypt_site_key = openssl_decrypt(
							base64_decode( $token, true ), // phpcs:ignore
							'aes-256-cbc',
							base64_decode( $data['secret_key'], true ), // phpcs:ignore
							OPENSSL_RAW_DATA,
							base64_decode( $data['iv'], true ) // phpcs:ignore
						);

						if ( base64_decode( $data['site_key'], true ) === $decrypt_site_key ) { // phpcs:ignore
							DB::update_option( 'shinobiReviewsAuthToken', $token );
							wp_send_json_success( $data );
						}
					}
				}
			}

			wp_send_json_error();
		} else {
			wp_send_json_error( __( 'An unexpected error has occurred.', 'shinobi-reviews' ) );
		}
	}

	/**
	 * Save user data
	 *
	 * @return void
	 */
	public function save_auth_user_data() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'save_auth_user_data' ) ) {
			$data = json_decode( filter_input( INPUT_POST, 'data' ), true );

			if ( $data ) {
				DB::update_option( 'shinobiReviewsAuthUserData', $data );
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} else {
			wp_send_json_error( __( 'An unexpected error has occurred.', 'shinobi-reviews' ) );
		}
	}

	public function reset_token() {
		$option_name = 'shinobiReviewsAuthUserData';
		$user_data   = DB::get_option( $option_name );

		if ( $user_data && isset( $user_data['ID'] ) && isset( $user_data['site_key'] ) ) {
			$user_id  = $user_data['ID'];
			$site_key = $user_data['site_key'];

			if ( 0 === $user_id ) { // for Fanbox user
				DB::delete_option( $option_name );

				Helper::notice_to_admin(
					// translators:Fanboxトークンのサポートが終了しました
					__( 'The support for Fanbox-token is over', 'shinobi-reviews' ),
					[
						// translators:いつもシノビレビューをご利用いただきありがとうございます。
						__( 'Thank you for always choosing Shinobi Reviews.', 'shinobi-reviews' ),
						// translators:Fanboxトークンのサポートがバージョン1.4.4で終了したため、ご留意ください。
						__( 'The support for Fanbox-token is over in version 1.4.4, please be aware of this.', 'shinobi-reviews' ),
						// translators:注：すでにご入手いただいているトークンは、以前のバージョンのシノビレビューで引き続きご使用いただけます。
						__( 'Note: the token you have gotten before can be still used in previous version of Shinobi Reviews.', 'shinobi-reviews' ),
						'------------',
						// translators:このメールはShinobi Reviewsから送信されました
						__( 'This e-mail was sent from Shinobi Reviews', 'shinobi-reviews' ),
					]
				);
			}
		}
	}

	/**
	 * Dummy data base
	 *
	 * @var array
	 */
	private $dummy_data_base = [
		[
			'ID'         => 1,
			'user_email' => 'note@shinobireviews.com',
			'site_key'   => 'OUgbnIFnDmCUPXj8KK1eliHr4dvcH6TtQeb8hZQvza0=',
			'secret_key' => 'Ym9rxyAgxVlWXGvW02eK/6upmDMi0ukM8Pdrsf/R1L0=',
			'iv'         => 'RQOmJtyM+Ga0Ty4wRABqLQ==',
		],
	];
}
