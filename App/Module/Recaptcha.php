<?php
/**
 * Recaptcha
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com
 * @since      1.0.7
 */

namespace Shinobi_Reviews\App\Module;

use Shinobi_Works\WP\DB;

class Recaptcha {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_script' ] );
		add_action( 'wp_ajax_recaptcha_verify', [ $this, 'recaptcha_verify' ] );
		add_action( 'wp_ajax_nopriv_recaptcha_verify', [ $this, 'recaptcha_verify' ] );
		add_action( 'wp_ajax_invalid_sitekey', [ $this, 'invalid_sitekey' ] );
		add_action( 'wp_ajax_nopriv_invalid_sitekey', [ $this, 'invalid_sitekey' ] );
		add_action( 'wp_ajax_invalid_site_domain', [ $this, 'invalid_site_domain' ] );
		add_action( 'wp_ajax_nopriv_invalid_site_domain', [ $this, 'invalid_site_domain' ] );
	}

	public function register_script() {
		if ( self::is_recaptcha() ) {
			$url = add_query_arg(
				[ 'render' => self::get_site_key() ],
				'https://www.google.com/recaptcha/api.js'
			);

			wp_register_script( 'shinobi-reviews/google-recaptcha', $url, [], '3.0', true );
		}
	}

	public static function form_actions() {
		return self::is_recaptcha() ? [
			'action'  => 'recaptcha_verify',
			'nonce'   => wp_create_nonce( 'recaptcha_verify' ),
			'sitekey' => self::get_site_key(),
		] : null;
	}

	public static function get_site_key() {
		return DB::get_option( 'recaptchaSiteKey', DB::get_option( 'recaptcha_site_key', '' ) );
	}

	public static function get_secret_key() {
		return DB::get_option( 'recaptchaSecretKey', DB::get_option( 'recaptcha_secret_key', '' ) );
	}

	public static function is_recaptcha() {
		return self::get_site_key() && self::get_secret_key();
	}

	public function invalid_sitekey() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'recaptcha_verify' ) ) {
			$msg = [
				// translators:Google reCAPTCHAのサイトキーが間違っている可能性があります。
				__( 'The site key of Google reCAPTCHA may be wrong.', 'shinobi-reviews' ) .
				// translators:この問題が解決するまで、Google reCAPTCHAは動作しません。
				__( 'Google reCAPTCHA will not work unless this error is resolved.', 'shinobi-reviews' ),
				'------------',
				// translators:このメールはShinobi Reviewsから送信されました
				__( 'This e-mail was sent from Shinobi Reviews', 'shinobi-reviews' ),
			];

			wp_mail(
				get_option( 'admin_email' ),
				// 内部エラーが発生しています。
				__( 'An internal error has occurred', 'shinobi-reviews' ) . ' By Shinobi Reviews',
				implode( PHP_EOL . PHP_EOL, $msg )
			);

			die();
		}
	}

	public function invalid_site_domain() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'recaptcha_verify' ) ) {
			$msg = [
				// translators:Google reCAPTCHAのドメイン設定が間違っている可能性があります。
				__( 'The domain settings of Google reCAPTCHA may be wrong.', 'shinobi-reviews' ) .
				// translators:この問題が解決するまで、Google reCAPTCHAは動作しません。
				__( 'Google reCAPTCHA will not work unless this error is resolved.', 'shinobi-reviews' ),
				'------------',
				// translators:このメールはShinobi Reviewsから送信されました
				__( 'This e-mail was sent from Shinobi Reviews', 'shinobi-reviews' ),
			];

			wp_mail(
				get_option( 'admin_email' ),
				// 内部エラーが発生しています。
				__( 'An internal error has occurred', 'shinobi-reviews' ) . ' By Shinobi Reviews',
				implode( PHP_EOL . PHP_EOL, $msg )
			);

			die();
		}
	}

	/**
	 * Verify
	 *
	 * @link https://developers.google.com/recaptcha/docs/verify/
	 * @return boolean
	 */
	public function recaptcha_verify() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'recaptcha_verify' ) ) {
			$request = [
				'body' => [
					'secret'   => self::get_secret_key(),
					'response' => filter_input( INPUT_POST, 'recaptchaToken' ),
				],
			];

			$response = wp_remote_post(
				esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify' ),
				$request
			);

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				wp_send_json_error();
			}

			$response_body = wp_remote_retrieve_body( $response );

			$response_body = json_decode( $response_body, true );

			if ( ! $response_body['success'] ) {
				switch ( $response_body['error-codes'][0] ) {
					case 'invalid-input-response':
					case 'missing-input-secret':
						$msg = [
							// translators:Google reCAPTCHAの設定が間違っている可能性があります。
							__( 'The setting of Google reCAPTCHA may be wrong.', 'shinobi-reviews' ) .
							// translators:サイトキーとシークレットキーの値が正しいことを確認してください。
							__( 'Please make sure the values of the site key and the secret key ​​are correct.', 'shinobi-reviews' ) .
							// translators:この問題が解決するまで、Google reCAPTCHAは動作しません。
							__( 'Google reCAPTCHA will not work unless this error is resolved.', 'shinobi-reviews' ),
							'------------',
							// translators:このメールはShinobi Reviewsから送信されました
							__( 'This e-mail was sent from Shinobi Reviews', 'shinobi-reviews' ),
						];

						wp_mail(
							get_option( 'admin_email' ),
							// 内部エラーが発生しています。
							__( 'An internal error has occurred', 'shinobi-reviews' ) . ' By Shinobi Reviews',
							implode( PHP_EOL . PHP_EOL, $msg )
						);
						break;
				}
			}

			$strength = (int) DB::get_option( 'recaptchaVerificationStrength', DB::get_option( 'recaptcha_verification_strength' ) );

			if ( ! $strength ) {
				$strength = 50;
			}

			$score = intval( $response_body['score'] * 100 );

			if ( $score >= $strength ) {
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} else {
			wp_send_json_error();
		}
	}

}
