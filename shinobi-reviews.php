<?php
/**
 * Plugin Name: Shinobi Reviews
 * Description: A review plugin for gathering many customer reviews and make friends with search engines.
 * Author: Shinobi Works
 * Author URI: https://shinobiworks.com/
 * Version: 1.6.0
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.0.0
 */

namespace Shinobi_Reviews;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SHINOBI_REVIEWS' ) ) {
	define( 'SHINOBI_REVIEWS', 'shinobi-reviews' );
}

if ( ! defined( 'SHINOBI_REVIEWS_PLUGIN_DIR' ) ) {
	define( 'SHINOBI_REVIEWS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SHINOBI_REVIEWS_PLUGIN_URL' ) ) {
	define( 'SHINOBI_REVIEWS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SHINOBI_REVIEWS_PLUGIN_BASE_NAME' ) ) {
	define( 'SHINOBI_REVIEWS_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'SHINOBI_REVIEWS_ADMIN_DIR' ) ) {
	define( 'SHINOBI_REVIEWS_ADMIN_DIR', SHINOBI_REVIEWS_PLUGIN_DIR . 'App/Admin/' );
}

if ( ! defined( 'SHINOBI_REVIEWS_ADMIN_URL' ) ) {
	define( 'SHINOBI_REVIEWS_ADMIN_URL', SHINOBI_REVIEWS_PLUGIN_URL . 'App/Admin/' );
}

if ( ! defined( 'SHINOBI_REVIEWS_ASSETS_DIR' ) ) {
	define( 'SHINOBI_REVIEWS_ASSETS_DIR', SHINOBI_REVIEWS_PLUGIN_DIR . 'assets/' );
}

if ( ! defined( 'SHINOBI_REVIEWS_ASSETS_URL' ) ) {
	define( 'SHINOBI_REVIEWS_ASSETS_URL', SHINOBI_REVIEWS_PLUGIN_URL . 'assets/' );
}

if ( ! defined( 'SHINOBI_REVIEWS_LIST_TABLE' ) ) {
	define( 'SHINOBI_REVIEWS_LIST_TABLE', 'shinobi_reviews' );
}

if ( ! defined( 'SHINOBI_REVIEWS_SHORTCODE_TABLE' ) ) {
	define( 'SHINOBI_REVIEWS_SHORTCODE_TABLE', 'shinobi_reviews_shortcode' );
}

if ( ! defined( 'SHINOBI_MEMBERSHIP_TABLE' ) ) {
	define( 'SHINOBI_MEMBERSHIP_TABLE', 'shinobi_membership' );
}

if ( ! defined( 'SHINOBI_OPTIONS_TABLE' ) ) {
	define( 'SHINOBI_OPTIONS_TABLE', 'shinobi_options' );
}

if ( ! defined( 'SHINOBI_MEMBERSHIP_SESSION_NAME' ) ) {
	define( 'SHINOBI_MEMBERSHIP_SESSION_NAME', 'shinobi_membership_ssid' );
}

if ( ! defined( 'SHINOBI_DEBUG' ) ) {
	define( 'SHINOBI_DEBUG', false );
}

if ( ! defined( 'SHINOBI_REVIEWS_VERSION' ) ) {
	define( 'SHINOBI_REVIEWS_VERSION', get_file_data( __FILE__, [ 'Version' ] )[0] );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Bootstrap
 */
class Bootstrap {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'activated_plugin', [ $this, 'welcome' ], 10, 2 );
		add_action( 'plugins_loaded', [ $this, 'bootstrap' ] );
	}

	/**
	 * @link https://developer.wordpress.org/reference/hooks/activated_plugin/
	 */
	public function welcome( $plugin, $network_wide ) {
		if ( SHINOBI_REVIEWS_PLUGIN_BASE_NAME === $plugin ) {
			wp_safe_redirect( esc_url( add_query_arg( [ 'page' => SHINOBI_REVIEWS ], admin_url( 'admin.php' ) ) ) );
			exit();
		}
	}

	/**
	 * Bootstrap
	 */
	public function bootstrap() {
		new \Shinobi_Works\WP\Bootstrap();

		new App\Setup\Bootstrap();

		new App\Helper();
		new App\InsertReview();
		new App\FetchOwnReview();
		new App\BlockEditor();

		new App\Shortcode\AddShortcode();

		new App\Widgets\Widgets();

		new App\Membership\Membership();
		new App\Membership\SessionManager();

		new App\Admin\Area();
		new App\Admin\Menu();
		new App\Admin\FormEditor();
		new App\Admin\ReviewEditor();
		new App\Admin\ReviewerEditor();
		new App\Admin\SettingsEditor();

		new App\Module\Recaptcha();
		new App\Module\ApiLike();
		new App\Module\SignInByToken();
	}
}

new Bootstrap();
