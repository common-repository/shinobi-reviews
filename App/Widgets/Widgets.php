<?php
/**
 * Widget Functions
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Works <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.4.8
 */

namespace Shinobi_Reviews\App\Widgets;

class Widgets {
	public function __construct() {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	public function register_widgets() {
		$namespace = 'Shinobi_Reviews\App\Widgets';
		$files     = glob( __DIR__ . '/*.php' );
		if ( $files ) {
			foreach ( $files as $index => $file ) {
				if ( __FILE__ === $file ) {
					continue;
				}
				$file = explode( '/', $file );
				$file = end( $file );
				$file = str_replace( '.php', '', $file );
				register_widget( "$namespace\\$file" );
			}
		}
	}
}
