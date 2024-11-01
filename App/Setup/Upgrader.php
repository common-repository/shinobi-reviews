<?php
/**
 * Upgrader
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.4.0
 */

namespace Shinobi_Reviews\App\Setup;

class Upgrader {

	public function __construct() {
		add_action( 'init', [ $this, 'update_version' ], 100 );
		add_action( 'upgrader_process_complete', [ $this, 'plugin_updated' ], 10, 2 );
	}

	/**
	 * Update version
	 *
	 * @return void
	 */
	public function update_version() {
		if ( SHINOBI_REVIEWS_VERSION !== \get_option( 'shinobiReviewsVersion' ) ) {
			\update_option( 'shinobiReviewsVersion', SHINOBI_REVIEWS_VERSION );
		}
	}

	public function plugin_updated( $upgrader_object, $options ) {
		if ( 'update' === $options['action'] && 'plugin' === $options['type'] ) {
			foreach ( $options['plugins'] as $each_plugin ) {
				if ( SHINOBI_REVIEWS_PLUGIN_BASE_NAME === $each_plugin ) {
					$this->update_version();
				}
			}
		}
	}
}
