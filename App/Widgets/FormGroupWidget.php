<?php
/**
 * Review Items
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

use Shinobi_Reviews\App\Shortcode\FormGroup;
use Shinobi_Works\WP\DB;
use WP_Widget;

class FormGroupWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'ShinobiReviews__FormGroupWidget', // Base ID
			__( 'Form Group', 'shinobi-reviews' ) . ' by Shinobi Reviews', // Name
			[
				'description' => __( 'A widget to display multiple aggregate ratings', 'shinobi-reviews' ), // Args
			]
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$title         = apply_filters( 'widget_title', $instance['title'] );
		$form_group_id = $instance['form_group_id'];
		echo wp_kses_post( $before_widget );
		if ( $title ) {
			echo wp_kses_post( $before_title ) . esc_html( $title ) . wp_kses_post( $after_title );
		}
		if ( $form_group_id ) {
			$form_group_data = DB::get_option( 'shinobiReviewsFormGroup' );
			if ( $form_group_data && isset( $form_group_data['data'][ $form_group_id ] ) ) {
				$form_group = $form_group_data['data'][ $form_group_id ];
				echo wp_kses_post( FormGroup::render( [ 'ids' => $form_group['ids'] ] ) );
			}
		}
		echo wp_kses_post( $after_widget );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$form_group = DB::get_option( 'shinobiReviewsFormGroup' );
		if ( ! $form_group && ! isset( $form_group['data'] ) ) {
			printf(
				// translators:フォームグループが見つかりませんでした。%sから設定してください。
				esc_html__( 'Not found a form group. Please configure from %s.', 'shinobi-reviews' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=shinobi-reviews' ) ) . '">' . esc_html__( 'main menu of Shinobi Reviews', 'shinobi-reviews' ) . '</a>'
			);
		} else {
			$title         = isset( $instance['title'] ) ? $instance['title'] : __( 'New title', 'shinobi-reviews' );
			$form_group_id = isset( $instance['form_group_id'] ) ? $instance['form_group_id'] : 0;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'shinobi-reviews' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_name( 'form_group_id' ) ); ?>"><?php esc_html_e( 'Form Group', 'shinobi-reviews' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'form_group_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'form_group_id' ) ); ?>">
					<option value="" hidden> - </option>
					<?php foreach ( $form_group['data'] as $id => $value ) { ?>
						<option value="<?php echo esc_attr( $id ); ?>" <?php echo checked( $id, $form_group_id ) ? 'selected' : ''; ?>><?php echo esc_html( $value['name'] ); ?></option>
					<?php } ?>
				</select>
			</p>
			<?php
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                  = [];
		$instance['title']         = $new_instance['title'] ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['form_group_id'] = $new_instance['form_group_id'] ? wp_strip_all_tags( $new_instance['form_group_id'] ) : 0;

		return $instance;
	}
}
