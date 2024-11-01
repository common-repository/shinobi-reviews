<?php
/**
 * Settings
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

class SettingsEditor {

	public function __construct() {
		add_action( 'wp_ajax_update_shinobi_reviews_settings', [ $this, 'update_shinobi_reviews_settings' ] );
	}

	public function update_shinobi_reviews_settings() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( wp_verify_nonce( $nonce, 'settings_editor' ) ) {
			$posted_data = json_decode( filter_input( INPUT_POST, 'data' ), true );

			if ( $posted_data ) {
				foreach ( $posted_data as $key => $value ) {
					if ( is_bool( $value ) ) {
						$value = wp_json_encode( $value );
					}
					DB::update_option( $key, $value );
				}
			}

			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'Invalid request.', 'shinobi-reviews' ) );
		}
	}
}
