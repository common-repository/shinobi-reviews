<?php
/**
 * Hello Block Editor
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.4.0
 */

namespace Shinobi_Reviews\App;

use Shinobi_Works\WP\DB;

class BlockEditor {

	public function __construct() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
		add_action( 'init', [ $this, 'register_block_type' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
	}

	public function enqueue_block_editor_assets() {
		$base_name         = 'block-editor/block-editor';
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
		wp_add_inline_script( SHINOBI_REVIEWS, 'const shinobiReviewsMainPageUrl = "' . admin_url( 'admin.php?page=shinobi-reviews' ) . '";', 'before' );
	}

	public function register_block_type() {
		register_block_type(
			'shinobi-reviews/shinobi-reviews',
			[
				'attributes'      => [
					'id'            => [
						'type'    => 'string',
						'default' => '',
					],
					'is_membership' => [
						'type'    => 'boolean',
						'default' => false,
					],
				],
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	public function render_callback( $attributes = [] ) {
		$shortcode_atts = '';

		if ( $attributes ) {
			foreach ( $attributes as $key => $value ) {
				if ( is_bool( $value ) ) {
					if ( ! $value ) {
						continue;
					}
					$value = wp_json_encode( $value );
				}
				$shortcode_atts .= " $key=\"$value\"";
			}
		}

		return "[shinobi-reviews$shortcode_atts]";
	}

	public function register_rest_route() {
		$namespace = 'shinobi-reviews/v2';

		register_rest_route(
			$namespace,
			'forms',
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => function() {
					$form_data = DB::get_option( 'shinobi_reviews_form_data', null );
					$args      = [];

					if ( $form_data ) {
						foreach ( $form_data as $id => $value ) {
							$args[] = [
								'label' => $value['name'],
								'value' => "$id",
							];
						}
					}

					return rest_ensure_response( $args );
				},
				'permission_callback' => function() {
					return current_user_can( 'administrator' ) ? true : new \WP_Error();
				},
			]
		);
	}

}
