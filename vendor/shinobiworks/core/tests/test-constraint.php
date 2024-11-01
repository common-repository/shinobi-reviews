<?php
/**
 * Constraint Test
 */

use Shinobi_Works\WP\Constraint;

/**
 * Constraint Test Class
 */
class ConstraintTest extends WP_UnitTestCase {

	/**
	 * Test is_text()
	 *
	 * @return void
	 */
	public function test_is_text() {
		/**
		 * These tests should pass.
		 */
		$pass_group[] = [
			'expected' => 'Hello World!!',
			'actual'   => '<div id="uniq_id" class="common-class"><p>Hello World!!</p></div>',
		];
		$pass_group[] = [
			'expected' => 'There are some spaces...',
			'actual'   => '<div> There are some spaces... </div>',
		];
		$pass_group[] = [
			'expected' => '全角スペースがあります',
			'actual'   => '<div>　全角スペースがあります　</div>',
		];
		$pass_group[] = [
			'expected' => '全　角　ス　ペ　ー　ス　が　あ　り　ま　す',
			'actual'   => '<div>　全　角　ス　ペ　ー　ス　が　あ　り　ま　す　</div>',
		];
		$pass_group[] = [
			'expected' => '',
			'actual'   => '<div>　　　</div>',
		];
		$pass_group[] = [
			'expected' => '',
			'actual'   => '<div>   </div>',
		];
		foreach ( $pass_group as $arr ) {
			$this->assertSame( $arr['expected'], Constraint::is_text( $arr['actual'] ) );
		}
	}
}
