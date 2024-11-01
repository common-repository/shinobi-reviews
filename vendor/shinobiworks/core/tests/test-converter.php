<?php
/**
 * Converter Test
 */

use PHPUnit\Framework\TestCase;
use Shinobi_Works\Converter;

/**
 * Coverter Test Class
 */
class ConverterTest extends TestCase {

	/**
	 * Test mb_trim()
	 *
	 * @return void
	 */
	public function test_mb_trim() {
		$this->assertSame( '左に全角スペース', Converter::mb_trim( '　左に全角スペース' ) );
		$this->assertSame( '左に全角スペース', Converter::mb_trim( '　　　　　左に全角スペース' ) );
		$this->assertSame( '右に全角スペース', Converter::mb_trim( '右に全角スペース　' ) );
		$this->assertSame( '右に全角スペース', Converter::mb_trim( '右に全角スペース　　　　　' ) );
	}

	/**
	 * Test get_attributes()
	 *
	 * @return void
	 */
	public function test_get_attributes() {
		/**
		 * Array has multiple attributes.
		 */
		$atts_array = [
			'id'        => 'uniq_id',
			'class'     => 'common-class',
			'data-name' => 'shinobi-works',
		];
		$expected   = 'id="uniq_id" class="common-class" data-name="shinobi-works"';
		$actual     = Converter::get_attributes( $atts_array );
		$this->assertSame( $expected, $actual );
		/**
		 * Array has only one attribute.
		 */
		$atts_array_2 = [
			'id' => 'uniq_id',
		];
		$this->assertSame( 'id="uniq_id"', Converter::get_attributes( $atts_array_2 ) );
		/**
		 * This test should fail because the value before conversion is a string.
		 */
		$this->assertFalse( Converter::get_attributes( 'I am wrong value' ) );
		/**
		 * This test should fail because the value before conversion is a empty array.
		 */
		$this->assertFalse( Converter::get_attributes( [] ) );
	}

}
