<?php
class Awp_Author_List extends WP_Widget {
	// Controller
	function __construct() {
		$widget_ops = array('classname' => 'author_list awp_widget', 'description' => __('Displays author list'));
		$control_ops = array('width' => 200, 'height' => 250);
		parent::__construct(false, $name = __('AWP Author List'), $widget_ops, $control_ops );
	}
	function form($instance) {
		$defaults=array('title' => __('Author List'),'exc'=>'','noauth'=>__('50'));
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title =$defaults['title'];
		}
		if ( isset( $instance[ 'noauth' ] ) ) {
			$noauth= $instance[ 'noauth' ];
		}
		else {
			$noauth=$defaults['noauth'];
		}
		if ( isset( $instance[ 'exc' ] ) ) {
			$exc= $instance[ 'exc' ];
		}
		else {
			$exc=$defaults['exc'];
		}?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'awp'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exc'); ?>"><?php _e('Exclude the user(ID)', 'awp'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('exc'); ?>" name="<?php echo $this->get_field_name('exc'); ?>" type="text" value="<?php echo esc_attr($exc); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('noauth'); ?>"><?php _e('No of author:', 'awp'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('noauth'); ?>" name="<?php echo $this->get_field_name('noauth'); ?>" type="number" value="<?php echo $noauth;?>" >
		</p>

		<?php }
		function update($new_instance,$old_instance){
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['exc'] = strip_tags( $new_instance['exc'] );
			$instance['noauth']=strip_tags($new_instance['noauth']);
			return $instance;
		}
		function widget($args, $instance) {
			$title = apply_filters('widget_title', $instance['title']);
			// Display the widget title
			echo $args['before_widget'];
			if ( $title ){
				echo $args['before_title'].$title.$args['after_title'];
			}
			awp_get_author_list($instance['noauth'],$instance['exc']);
			echo $args['after_widget'];
		}
	}
