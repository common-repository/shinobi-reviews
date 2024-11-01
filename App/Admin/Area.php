<?php
/**
 * Area
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.4.0
 */

namespace Shinobi_Reviews\App\Admin;

class Area {

	public function __construct() {
		add_filter( 'plugin_action_links_' . SHINOBI_REVIEWS_PLUGIN_BASE_NAME, [ $this, 'add_plugin_action_link' ], 10, 1 );
	}

	public function add_plugin_action_link( $links ) {
		$custom = [];

		if ( ! is_network_admin() ) {
			$custom['settings'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( [ 'page' => SHINOBI_REVIEWS ], admin_url( 'admin.php' ) ) ),
				__( 'Settings', 'shinobi-reviews' )
			);
		}

		return array_merge( $custom, (array) $links );
	}
}
