<?php
/**
 * Setup
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

class Bootstrap extends Migration {

	public function __construct() {
		parent::__construct();

		new Upgrader();

		add_action( 'init', [ $this, 'create_reviews_list_table' ], 100 );
	}

	/**
	 * Create reviews list table
	 *
	 * @since 1.3.0
	 */
	public function create_reviews_list_table() {
		DB::create_table(
			'1.3.9',
			SHINOBI_REVIEWS_LIST_TABLE,
			"
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		    review_rating bigint(20) UNSIGNED NOT NULL,
		    review_content varchar(1000) NOT NULL,
		    review_post_id bigint(20) UNSIGNED NOT NULL,
		    review_form_id bigint(20) UNSIGNED NOT NULL,
			review_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		    review_approved varchar(20) DEFAULT '0' NOT NULL,
			review_author tinytext NULL,
		    review_attachment_ids varchar(255) NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY  id (ID)
			"
		);
	}
}
