<?php
/**
 * A file for processing a mail to send a reviewer
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

use Shinobi_Works\WP\DB;

class MailFactory {

	/**
	 * Mail header
	 *
	 * @return string
	 */
	private static function mail_header( $name ) {
		$default = 'ja' === get_locale() ? '[your-name] 様' : 'Dear [your-name]';
		$text    = DB::get_option( 'shinobi_membership_mail_header', $default );
		$text    = str_replace( '[your-name]', $name, $text );
		return $text;
	}

	/**
	 * Mail footer
	 *
	 * @return string
	 */
	private static function mail_footer() {
		$sender    = __( 'Sender', 'shinobi-reviews' );
		$site_name = get_bloginfo( 'name' );
		$site_url  = home_url();

		$caution = self::get_imploded_text(
			[
				// translators:このメールは送信専用のメールアドレスより配信しています。
				'※' . __( 'This e-mail is sent from a dedicated e-mail address.', 'shinobi-reviews' ) .
				// translators:このメールにご返信いただいてもお応えすることはできません。
				__( 'If you reply to this email, we will not be able to respond.', 'shinobi-reviews' ),
			]
		);

		$default = <<< EOM
{$caution}

───────────────────
{$sender}:{$site_name}
URL:{$site_url}
───────────────────
EOM;

		return DB::get_option( 'shinobi_membership_mail_footer', $default );
	}

	public static function get_pre_registeration_message( $password ) {
		$default = [
			// translators:レビューアカウントへ仮登録いただき、誠にありがとうございます。
			__( 'Thank you for temporarily registering for a review account.', 'shinobi-reviews' ) .
			// translators:あなたの仮パスワードは %s です。
			sprintf( __( 'Your temporary password is %s.', 'shinobi-reviews' ), '[password]' ) .
			// translators:設定は以上です！.
			__( "That's all for the settings!", 'shinobi-reviews' ),
			// translators:リンクの有効期限が切れている場合は、再度仮登録をお願いいたします。
			'※' . __( 'If the link has expired, please register again.', 'shinobi-reviews' ),
		];
		$default = self::get_imploded_text( $default );
		$text    = DB::get_option( 'shinobi_membership_pre_registeration_mail', $default );
		$text    = str_replace( '[password]', $password, $text );

		return self::get_imploded_text(
			[
				$text,
				self::mail_footer(),
			]
		);
	}

	public static function get_registeration_message( $name ) {
		$default = [
			// translators:レビューアカウントへご登録いただき、誠にありがとうございます。
			__( 'Thank you for registering for a review account.', 'shinobi-reviews' ) .
			// translators:レビュー投稿をお楽しみください！
			__( 'Please enjoy posting a review!', 'shinobi-reviews' ),
			// translators:同じフォームへの2回以上のレビュー投稿はできませんが、いつでも自分のレビューを更新することができます。
			'※' . __( 'Make sure that you cannot post a review to the same form more than once, but you can update your review whenever you want.', 'shinobi-reviews' ),
		];
		$default = self::get_imploded_text( $default );
		$text    = DB::get_option( 'shinobi_membership_registeration_mail', $default );

		return self::get_imploded_text(
			[
				self::mail_header( $name ),
				$text,
				self::mail_footer(),
			]
		);
	}

	public static function get_review_posting_done_message( $name ) {
		$default = [
			// translators:レビューを投稿していただきありがとうございます！
			__( 'Thank you for posting the review!', 'shinobi-reviews' ) .
			// translators:投稿されたレビューは承認後に表示されます。
			__( 'The posted review will be showed after approval.', 'shinobi-reviews' ) .
			// translators:表示までにお時間をいただく場合がございますが、ご理解いただければ幸いです。
			__( 'It may take some times before it is displayed, but we hope you understand it.', 'shinobi-reviews' ),
		];
		$default = self::get_imploded_text( $default );
		$text    = DB::get_option( 'shinobi_membership_review_posting_done_mail', $default );

		return self::get_imploded_text(
			[
				self::mail_header( $name ),
				$text,
				self::mail_footer(),
			]
		);
	}

	public static function get_password_reset_message( $password ) {
		$default = [
			// translators:あなたのリセットパスワードは %s です。
			sprintf( __( 'Your reset password is %s.', 'shinobi-reviews' ), $password ),
		];
		$default = self::get_imploded_text( $default );
		$text    = DB::get_option( 'shinobi_membership_password_reset_mail', $default );

		return self::get_imploded_text(
			[
				$text,
				self::mail_footer(),
			]
		);
	}

	/**
	 * Get the thanks message for the review updating.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function get_review_updated_message( $name ) {
		$default = [
			// translators:レビューを更新していただきありがとうございます！
			__( 'Thank you for updating your review!', 'shinobi-reviews' ) .
			// translators:レビューの更新は他のユーザーにとって非常に役立ちます。貴重なお時間をありがとうございました！
			__( 'Updating a review is much more helpful for the other users. Many thanks for your time!', 'shinobi-reviews' ),
		];
		$default = self::get_imploded_text( $default );
		$text    = DB::get_option( 'shinobi_review_updated_mail', $default );

		return self::get_imploded_text(
			[
				self::mail_header( $name ),
				$text,
				self::mail_footer(),
			]
		);
	}

	private static function get_imploded_text( $array ) {
		return implode( PHP_EOL . PHP_EOL, $array );
	}
}
