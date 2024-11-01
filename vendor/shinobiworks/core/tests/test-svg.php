<?php
/**
 * SVG Test
 */

use Shinobi_Works\Generate\SVG;
use Shinobi_Works\WP\Utils;

/**
 * SVG test case
 */
class SVG_Test extends WP_UnitTestCase {

	/**
	 * Test rating star
	 *
	 * @return void
	 */
	public function test_svg() {
		$svg_group[] = SVG::arrow_drop_down();
		$svg_group[] = SVG::file_copy();
		$svg_group[] = SVG::account_circle();
		$svg_group[] = SVG::face();
		$svg_group[] = SVG::email();
		$svg_group[] = SVG::close();
		$svg_group[] = SVG::lock();
		$svg_group[] = SVG::edit();
		$svg_group[] = SVG::sort();
		$svg_group[] = SVG::help();
		$svg_group[] = SVG::info();
		foreach ( $svg_group as $svg ) {
			$this->assertSame( $svg, Utils::esc_svg( $svg ) );
		}
	}

}
