<?php
/**
 * The membership management systems only for Shinobi Reviews
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Shinobi Reviews
 * @author     Shinobi Reviews <support@shinobiworks.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html/ GPL v3 or later
 * @link       https://shinobiworks.com/
 * @since      1.3.0
 */

namespace Shinobi_Reviews\App\Membership;

use Shinobi_Reviews\App\FetchOwnReview;
use Shinobi_Works\WP\DB;

class Membership {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'create_table' ] );

		new Authenticate();
		new Registeration();
		new Login();
		new ResetPassword();
		new Logout();
	}

	/**
	 * Create the membership table
	 */
	public function create_table() {
		DB::create_table(
			'1.0.0',
			SHINOBI_MEMBERSHIP_TABLE,
			"
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_nicename varchar(50) NOT NULL,
			user_pass varchar(255) NOT NULL,
			user_email varchar(100) NOT NULL,
			user_status varchar(20) DEFAULT '1' NOT NULL,
			review_count bigint(20) UNSIGNED DEFAULT '0' NOT NULL,
			user_registered datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  id (ID),
			UNIQUE KEY user_name (user_email)
			"
		);
	}

	/**
	 * Print the json data of Shinobi Reviews
	 *
	 * @return array
	 */
	public static function form_actions() {
		$user_id             = SessionManager::set_shinobi_membership_id();
		$isset_email         = isset( $_SESSION['shinobi_membership_email'] );
		$form_actions        = [];
		$logout_form_actions = [
			'logout' => [
				'action' => 'logout',
				'nonce'  => wp_create_nonce( 'logout' ),
			],
		];
		$login_form_actions  = [
			'login'             => [
				'action' => 'login',
				'nonce'  => wp_create_nonce( 'login' ),
			],
			'sendResetPassword' => [
				'action' => 'send_reset_password',
				'nonce'  => wp_create_nonce( 'send_reset_password' ),
			],
		] + $logout_form_actions;

		if ( 0 === $user_id && $isset_email ) {
			$form_actions = [
				'register' => [
					'action' => 'register',
					'nonce'  => wp_create_nonce( 'register' ),
				],
			] + $logout_form_actions;
		} elseif ( -1 === $user_id ) {
			$form_actions = $isset_email ? $login_form_actions : [
				'authenticate' => [
					'action' => 'authenticate',
					'nonce'  => wp_create_nonce( 'authenticate' ),
				],
			];
		} else {
			$form_actions = FetchOwnReview::form_actions() + $logout_form_actions;
		}

		return $form_actions;
	}

	public static function is_membership() {
		$is_membership = DB::get_option( 'isMembership', DB::get_option( 'is_membership' ) );

		return filter_var( $is_membership, FILTER_VALIDATE_BOOLEAN );
	}
}
