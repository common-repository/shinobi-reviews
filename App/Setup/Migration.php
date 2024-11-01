<?php
/**
 * Support for the old version
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.3.0
 */

namespace Shinobi_Reviews\App\Setup;

use Shinobi_Works\WP\DB;

class Migration {

	public function __construct() {
		add_action( 'init', [ $this, 'delete_attachment_1to3_column' ], 5 );
		add_action( 'init', [ $this, 'migrate_form_data' ], 5 );
	}

	/**
	 * Refactoring table for the version before 1.3.0
	 *
	 * @return void
	 */
	public function delete_attachment_1to3_column() {
		if ( DB::get_table_version( SHINOBI_REVIEWS_LIST_TABLE ) === '1.0.0' ) {
			$review_table = DB::get_results( SHINOBI_REVIEWS_LIST_TABLE, ARRAY_A );

			if ( $review_table ) {
				global $wpdb;
				$table_name_with_prefix = $wpdb->prefix . SHINOBI_REVIEWS_LIST_TABLE;

				$wpdb->query( "ALTER TABLE $table_name_with_prefix ADD COLUMN review_attachment_ids varchar(255) NULL" ); // phpcs:ignore

				foreach ( $review_table as $index => $review_data ) {
					$attachment_ids = [];

					for ( $i = 1; $i < 4; $i++ ) {
						if ( $review_data[ "review_attachment_id_$i" ] ) {
							$attachment_ids[] = $review_data[ "review_attachment_id_$i" ];
						}
					}

					if ( $attachment_ids ) {
						DB::update(
							SHINOBI_REVIEWS_LIST_TABLE,
							[ 'review_attachment_ids' => wp_json_encode( array_values( $attachment_ids ) ) ],
							[ 'ID' => $review_data['ID'] ]
						);
					}
				}
			}

			\update_option( SHINOBI_REVIEWS_LIST_TABLE . '_table_ver', '1.0.1' );
		}
	}

	/**
	 * Migrate form data
	 *
	 * @return void
	 */
	public function migrate_form_data() {
		if ( DB::get_table_version( SHINOBI_REVIEWS_SHORTCODE_TABLE ) === '1.0.3' ) {
			$all_form_data = DB::get_results( SHINOBI_REVIEWS_SHORTCODE_TABLE );

			if ( $all_form_data ) {
				foreach ( $all_form_data as $index => $form_data ) {
					$processed_form_data[ $form_data->ID ] = $this->generate_form_data( $form_data );
				}

				DB::update_option( 'shinobi_reviews_form_data', $processed_form_data );

				$last_id = end( array_keys( $processed_form_data ) ) + 1;
				DB::update_option( 'shinobi_reviews_form_last_id', $last_id );
			}

			\update_option( SHINOBI_REVIEWS_SHORTCODE_TABLE . '_table_ver', '1.0.4' );
		}
	}

	/**
	 * Generate form data
	 *
	 * @param object $form_data
	 * @return array
	 */
	private function generate_form_data( $form_data ) {
		$review_type         = $form_data->review_type;
		$generated_form_data = [
			'@context' => 'https://schema.org/',
			'@type'    => $review_type,
			'name'     => trim( $form_data->review_thing ),
		];

		$attachment_id = $form_data->attachment_id;
		if ( $attachment_id && wp_get_attachment_url( $attachment_id ) ) {
			$generated_form_data += [
				'image' => [ wp_get_attachment_url( $attachment_id ) ],
			];
		}

		switch ( $review_type ) {
			// Product
			case 'Product':
				$flag           = false;
				$price          = $form_data->price;
				$price_currency = $form_data->price_currency;
				if ( 0 <= $price && $price_currency ) {
					$flag      = true;
					$price_arr = [
						'priceCurrency' => $price_currency,
						'price'         => $price,
					];
				}
				if ( $flag ) {
					$generated_form_data += [
						'offers' => [
							'@type' => 'Offer',
						] + $price_arr,
					];
				}
				break;
			// Restaurant
			case 'Restaurant':
				$serves_cuisine = $form_data->serves_cuisine;
				if ( $serves_cuisine ) {
					$generated_form_data += [
						'servesCuisine' => trim( $serves_cuisine ),
					];
				}
				// Restaurant and Localbusiness
			case 'LocalBusiness':
				// Check if the phone number.
				$phone_number = $form_data->phone_number;
				if ( $phone_number ) {
					$generated_form_data += [
						'telephone' => trim( $phone_number ),
					];
				}
				// Check if the address.
				$address_country  = $form_data->address_country;
				$postal_code      = $form_data->postal_code;
				$address_region   = $form_data->address_region;
				$address_locality = $form_data->address_locality;
				$street_address   = $form_data->street_address;
				if ( $address_country && $postal_code && $address_region && $address_locality && $street_address ) {
					$generated_form_data += [
						'address' => [
							'@type'           => 'PostalAddress',
							'streetAddress'   => trim( $street_address ),
							'addressLocality' => trim( $address_locality ),
							'addressRegion'   => trim( $address_region ),
							'postalCode'      => trim( $postal_code ),
							'addressCountry'  => trim( $address_country ),
						],
					];
				}
				break;
			// Recipe
			case 'Recipe':
				$description = $form_data->description;
				if ( $description ) {
					$generated_form_data += [
						'description' => trim( $description ),
					];
				}
				$keywords = $form_data->keywords;
				if ( $keywords ) {
					$generated_form_data += [
						'keywords' => trim( $keywords ),
					];
				}
				$recipe_category = $form_data->recipe_category;
				if ( $recipe_category ) {
					$generated_form_data += [
						'recipeCategory' => trim( $recipe_category ),
					];
				}
		}

		return $generated_form_data;
	}

	/**
	 * Migrate DB::get_option()
	 *
	 * @since 1.4.0
	 * @deprecated 1.4.4
	 * @return void
	 */
	public function migrate_shinobi_options() {
		if ( SHINOBI_REVIEWS_VERSION <= '1.4.0' ) {
			$pair_of_names = [
				[
					'old' => 'shinobiReviews_fanbox_userData',
					'new' => 'shinobiReviewsAuthUserData',
				],
				[
					'old' => 'shinobiReviews_fanbox_token',
					'new' => 'shinobiReviewsAuthToken',
				],
			];

			foreach ( $pair_of_names as $index => $option_names ) {
				$old_option_name = $option_names['old'];
				$new_option_name = $option_names['new'];

				$old_option = DB::get_option( $old_option_name );
				$new_option = DB::get_option( $new_option_name );

				if ( ! $new_option && $old_option ) {
					DB::update_option( $new_option_name, $old_option );
					DB::delete_option( $old_option_name );
				}
			}
		}
	}
}
