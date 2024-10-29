<?php
class Awp_Popular_Post extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname' => 'awp_popular_post awp_widget',
			'description' => __('Widget to display popular posts', 'awp')
		);
		$control_ops = array(
			'id_base' => 'awp_popular_post',
			'width' => 200,
			'height' => 250
		);
		parent::__construct('awp_popular_post', $name = __('AWP Popular Posts', 'awp'), $widget_ops, $control_ops );
	}
	public function widget($awp_args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);
		$post_count= apply_filters('widget_title', $instance['post_count']);
		global $author_id;
		// Display the widget title
		echo $awp_args['before_widget'];
		if ( $title ){
			echo $awp_args['before_title'] .$title. $awp_args['after_title'];
		}
		if($instance['sort_radio']=="comments") {
			$args = array(
				"posts_per_page" => $post_count,
				"post_type" => "post",
				"post_status" => "publish",
				"orderby" => "comment_count",
				"order" => "DESC"
			);
		}
		else {
			$args = array(
				"posts_per_page" => $post_count,
				"post_type" => "post",
				"post_status" => "publish",
				"meta_key" => "wp_views",
				"orderby" => "meta_value_num",
				"order" => "DESC"
			);
		}
		$asc_list = new WP_Query($args);
		if($asc_list->have_posts()) {
			echo "<ul class='awp-list'>";
			while ( $asc_list->have_posts() ) : $asc_list->the_post();
			echo '<li class="awp-post-item"><h4 class="awp-post-title"><a href="'.get_permalink().'">'.the_title('', '', false)."</a></h4>";
			if($instance['featured-image']){
				echo"<div class='awp-featured-image'>";
				if(has_post_thumbnail())
				the_post_thumbnail();
				echo"</div>";
			}
			if($instance['post-date']||$instance['post-author']){
				echo"<div class='awp-post-meta'>";
			}
			if ($instance['post-date']){
				echo"<div>On: ";
				echo get_the_date();
				echo"</div>";
			}
			if(function_exists("awp_display_post_author_name")){
				if($instance['post-author']){
					echo "<div>By: ";
					awp_display_post_author_name();
					echo "</div>";
				}
			}
			if($instance['post-date']||$instance['post-author']){
				echo"</div>";
			}
			if( $instance['post-category'] || $instance['comments'] || $instance['views'] )
			{
				if ($instance['post-category']){
					echo "<div><strong>Category: </strong>";
					echo get_the_category_list();
					echo"</div>";
				}
				if ($instance['comments']){
					echo "<div>";
					comments_number();
					echo"</div>";
				}
				if ($instance['views']){
					echo"<div>";
					awp_show_views();
					echo"</div>";
				}
			}
			if ($instance['post-excerpt']){
				echo"<div class='awp-post-excerpt'>";
				the_excerpt();
				echo"</div>";
			}
			echo"</li>";
			endwhile;
			echo "</ul>";
		}
		echo $awp_args['after_widget'];
	}
function form($instance) {
	$defaults = array( 'title' => __('Popular Posts', 'awp'), 'post_count' => __('5'),'sort_radio'=>'views','featured-image'=>'0','post-date'=>'0','post-author'=>'0','post-category'=>'0','comments'=>'0','views'=>'0','post-excerpt'=>'0');
	$instance = wp_parse_args( (array) $instance, $defaults );
	if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
		$post_count=$instance['post_count'];
	}
	else {
		$title =$defaults['title'];
		$post_count=$defaults['post_count'];
	}
	?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'awp'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Number of Posts:', 'awp'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('post_count'); ?>" name="<?php echo $this->get_field_name('post_count'); ?>" value="<?php echo $post_count; ?>" type="number" >
	</p>
	<p>
		<input type="radio" id="<?php echo $this->get_field_id('sort_radio'); ?>"
		name="<?php echo $this->get_field_name('sort_radio'); ?>"
		<?php if (isset($instance['sort_radio']) && $instance['sort_radio']=="views") echo "checked";?>
		value="views"><?php _e('Sort by Views', 'awp'); ?> <br>
		<input type="radio" id="<?php echo $this->get_field_id('sort_radio'); ?>
		" name="<?php echo $this->get_field_name('sort_radio'); ?>"
		<?php if (isset($instance['sort_radio']) && $instance['sort_radio']=="comments")echo "checked";?>
		value="comments"><?php _e('Sort by Comments', 'awp'); ?>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['featured-image'], 'on'); ?> id="<?php echo $this->get_field_id('featured-image'); ?>" name="<?php echo $this->get_field_name('featured-image'); ?>" />
		<label for="<?php echo $this->get_field_id('featured-image'); ?>"><?php _e('Display Featured Image', 'awp'); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['post-date'], 'on'); ?> id="<?php echo $this->get_field_id('post-date'); ?>" name="<?php echo $this->get_field_name('post-date'); ?>" />
		<label for="<?php echo $this->get_field_id('post-date'); ?>"><?php _e('Display Post Date', 'awp'); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['post-author'], 'on'); ?> id="<?php echo $this->get_field_id('post-author'); ?>" name="<?php echo $this->get_field_name('post-author'); ?>" />
		<label for="<?php echo $this->get_field_id('post-author'); ?>"><?php _e('Display Name of the Author', 'awp'); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['post-category'], 'on'); ?> id="<?php echo $this->get_field_id('post-category'); ?>" name="<?php echo $this->get_field_name('post-category'); ?>" />
		<label for="<?php echo $this->get_field_id('post-category'); ?>"><?php _e('Display Post Category', 'awp'); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['comments'], 'on'); ?> id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" />
		<label for="<?php echo $this->get_field_id('comments'); ?>"><?php _e('Display Number of Comments', 'awp'); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['views'], 'on'); ?> id="<?php echo $this->get_field_id('views'); ?>" name="<?php echo $this->get_field_name('views'); ?>" />
		<label for="<?php echo $this->get_field_id('views'); ?>"><?php _e('Display Number of  Views', 'awp'); ?></label>
	</p>
	<p>
		<input class="checkbox" type="checkbox" <?php checked($instance['post-excerpt'], 'on'); ?> id="<?php echo $this->get_field_id('post-excerpt'); ?>" name="<?php echo $this->get_field_name('post-excerpt'); ?>" />
		<label for="<?php echo $this->get_field_id('post-excerpt'); ?>"><?php _e('Display Post Excerpt', 'awp'); ?></label>
	</p>
	<?php
}
function update($new_instance,$old_instance){
	$instance = $old_instance;
	$instance['title'] = strip_tags( $new_instance['title'] );
	$instance['post_count'] = strip_tags( $new_instance['post_count'] );
	$instance['sort_radio'] = strip_tags( $new_instance['sort_radio'] );
	$instance['featured-image'] = strip_tags( $new_instance['featured-image']);
	$instance['post-date'] = strip_tags( $new_instance['post-date']);
	$instance['post-category'] = strip_tags( $new_instance['post-category']);
	$instance['comments'] = strip_tags( $new_instance['comments']);
	$instance['views'] = strip_tags( $new_instance['views']);
	$instance['post-excerpt'] = strip_tags( $new_instance['post-excerpt']);
	$instance['post-author'] = strip_tags( $new_instance['post-author']);
	return $instance;
}
}
?>
