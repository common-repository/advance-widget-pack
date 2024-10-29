<?php
	 /*
	 Plugin Name: Advance Widget Pack
	 Plugin URI: http://www.github.com/saumya010/advance-widget-pack/
	 Description: A plugin to display author bio, author list, popular posts, random posts, featured posts, recent posts and recent comments.
	 Version: 1.0.8
	 Author: Saumya Sharma, Purva Jain, Nidarshana Sharma, Nikita Pariyani, Shruti Taldar
	 Author URI: http://github.com/saumya010
	 License: GPL2 or later
	 License URI: http://www.gnu.org/licenses/gpl-2.0.html
	 */
?>
<?php

function awp_stylesheet()
{
  wp_register_style( 'awp-plugin', plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'awp-plugin',plugins_url('style.css', __FILE__) );
}
add_action('wp_enqueue_scripts', 'awp_stylesheet');

add_action('wp_head', 'awp_add_view');
function awp_get_author_list($noauth,$exc){
		echo "<ul>";
		wp_list_authors(array('number'=>$noauth,'exclude'=>$exc));
		echo "</ul>";
}

function awp_display_featured_image(){
		global $post;
		$post_id=$post->ID;
		if ( has_post_thumbnail($post_id) ) {
				the_post_thumbnail('medium');
		}
}
function awp_display_post_author_name(){
		global $post;
		$author_id= $post->post_author;
		if( get_the_author_meta('first_name',$author_id) || get_the_author_meta('last_name',$author_id) ) {
			echo get_the_author_meta('first_name',$author_id);
			echo " ";
			echo get_the_author_meta('last_name',$author_id);
		}
		else {
			echo get_the_author_meta('display_name',$author_id);
		}
}
function awp_display_author_description($post_id=0){
				$post = get_post( $post_id );
				$auth_id=$post->post_author;
				if( get_the_author_meta( 'description', $auth_id) ) {
					echo get_the_author_meta( 'description', $auth_id);
				}
				else {
					echo "Sorry! No description found.";
				}
}
function awp_add_view(){
		if(is_single()){
				global $post;
				$current_views=get_post_meta($post->ID, "wp_views", true);
				if(!isset($current_views) OR empty($current_views) OR !is_numeric($current_views) ) {
						$current_views = 0;
				}
				$new_views = $current_views + 1;
				update_post_meta($post->ID, "wp_views", $new_views);
				return $new_views;
		}
}
function awp_get_view_count() {
		global $post;
		$current_views = get_post_meta($post->ID, "wp_views", true);
		if(!isset($current_views) OR empty($current_views) OR !is_numeric($current_views) ) {
				$current_views = 0;
		}
		return $current_views;
}
function awp_show_views($singular = "view", $plural = "views", $before = "This post has: ") {
		global $post;
		$current_views = get_post_meta($post->ID, "wp_views", true);
		$views_text = $before . $current_views . " ";
		if ($current_views == 1) {
				$views_text .= $singular;
		}
		else if( $current_views >= 1 ) {
				$views_text .= $plural;
		}
		else {
			$views_text .= "0 views";
		}
		echo $views_text;
}

function custom_excerpt_length( $length ) {
	return 15;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function awp_excerpt_more($more) {
				global $post;
				return '<a class="moretag" href="'. get_permalink($post->ID) . '">	 Read More.</a>';
}
add_filter('excerpt_more', 'awp_excerpt_more');

include 'awp-recent-post.php';
include 'awp-popular-post.php';
include 'awp-random-post.php';
include 'awp-feature-post.php';
include 'awp-recent-comments.php';
include 'awp-author-list.php';
include 'awp-author-bio.php';
add_action('widgets_init', function () {
	return register_widget('Awp_Recent_Comments');
});
add_action('widgets_init', function () {
	return register_widget('Awp_Feature_Post');
});
add_action('widgets_init', function () {
	return register_widget('Awp_Recent_Post');
});
add_action('widgets_init', function () {
	return register_widget('Awp_Popular_Post');
});
add_action('widgets_init', function () {
	return register_widget('Awp_Random_Post');
});
add_action('widgets_init', function () {
	return register_widget('Awp_Author_List');
});
add_action('widgets_init', function () {
	return register_widget('Awp_Author_Bio');
});
