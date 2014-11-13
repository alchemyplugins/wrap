<?php

namespace AlchemyPlugins;

class WrapWidget extends \WP_Widget
{
	public static function get_var_fields( $post_id, $field_id, $field_name, $field_value = null ) {
		$output = '';
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );
			preg_match_all( '/\{\{(.*)\}\}/i', $post->post_content, $matches );
			if ( is_array( $matches[1] ) ) {
				foreach( $matches[1] as $var ) {
					$var_field_id = $field_id . '-' . $var;
					$var_field_name = $field_name . '[' . $var . ']';
					$var_field_value  = @$field_value[$var];
					$output .= sprintf( '<p><label for="%s">%s</label>', $var_field_id, ucfirst($var) . ':' );
					$output .= sprintf( '<input class="widefat" type="text" id="%s" name="%s" value="%s"></p>', $var_field_id, $var_field_name, esc_attr( $var_field_value ) );
				}
			}
		}
		return $output;
	}

	public function __construct() {
		parent::__construct(
			'ap_wrapper_widget',
			__('Content Instance', '<%= pkg.name %>'), // Name
			array( 'description' => __( 'Place a content instance', '<%= pkg.name %>' ),
				'classname' => 'sample-widget'
			) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		var_dump($args);
		var_dump($instance);
		var_dump('ok1');
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo __( 'Hello, World!', '<%= pkg.name %>' );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$output = '';

		$title = @$instance['title'];
		$output .= sprintf( '<p><label for="%s">%s</label>', $this->get_field_id( 'title' ), __( 'Title:' ) );
		$output .= sprintf( '<input class="widefat" id="%s" name="%s" type="text" value="%s"></p>', $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), esc_attr( $title ) );


global $cpt, $post;
		// consider workflows, perhaps allow other post status array( 'publish', 'pending', 'draft', 'future', 'private')
		$my_query = new \WP_Query( array(
			'post_type' => $cpt,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'ignore_sticky_posts' => true
		) );

		$post_id = @$instance['post_id'];
		$output .= sprintf( '<p><label for="%s">%s</label>', $this->get_field_id( 'post_id' ), __( 'Snippet', '<%= pkg.name %>' ) );
		$output .= sprintf( '<select id="%s" name="%s">', $this->get_field_id( 'post_id' ), $this->get_field_name( 'post_id' ) );
		$output .= '<option value="">' . __( 'Select snippet ...', '<%= pkg.name %>' ) .'</option>';
		if( $my_query->have_posts() ) {
			while ($my_query->have_posts()) {
				$my_query->the_post();
				$output .= sprintf( '<option value="%s"%s>%s</option>', $post->ID, ($post->ID==$post_id?' selected':''), get_the_title() );
			}
		}
		$output .= '</select></p>';

		$output .= '<p><a class="<%= pkg.settings.namespace %>-edit-snippet-link" href="#" style="display:none;">' . __( 'Edit snippet', '<%= pkg.name %>' ) . '</a></p>';

		$output .= '<div class="<%= pkg.settings.namespace %>-current-vars">';
		if ( $post_id ) {
			$output .= WrapWidget::get_var_fields( $post_id, $this->get_field_id('vars'), $this->get_field_name('vars'), @$instance['vars'] );
		}
		$output .= '</div>';
		$output .= '<div class="<%= pkg.settings.namespace %>-vars"></div>';

		echo '<div class="<%= pkg.settings.namespace %>-widget-form">' . $output . '</div>';

		?><script type="text/javascript">
			jQuery(document).ready(function($) {
				var select_el = $('#<?php echo $this->get_field_id( 'post_id' ); ?>');
				var container_el = select_el.closest('.<%= pkg.settings.namespace %>-widget-form');
				select_el.on('change', function() {
					var val = select_el.val();
					var data = {
						'action': '<%= pkg.settings.namespace %>_get_wrapper_vars',
						'post_id': val,
						'field_id': '<?php echo $this->get_field_id('vars'); ?>',
						'field_name': '<?php echo $this->get_field_name('vars'); ?>'
					};
					$.post(ajaxurl, data, function(response) {
						$('.<%= pkg.settings.namespace %>-vars', container_el).empty();
						$('.<%= pkg.settings.namespace %>-current-vars', container_el).hide().find(':input').attr('disabled', 'disabled');
						// check current post ID
						if ('<?php echo $post_id; ?>' == val) {
							$('.<%= pkg.settings.namespace %>-current-vars', container_el).show().find(':input').removeAttr('disabled');
						} else {
							$('.<%= pkg.settings.namespace %>-vars', container_el).html(response);
						}
					});
				});
				var link_el = $('.<%= pkg.settings.namespace %>-edit-snippet-link', container_el);
				link_el.click(function(e){
					var post_id = select_el.val();
					if (post_id) {
						link_el.attr('href', '/wp-admin/post.php?post=' + post_id + '&action=edit');
					} else {
						e.preventDefault();
					}
				});
			});
		</script><?php
	}
}
