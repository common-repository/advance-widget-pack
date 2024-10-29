<?php
class Awp_Random_Post extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'classname'   => 'awp_random_posts awp_widget',
            'description' => __( 'Widget to display random posts.', 'awp' ),
        );
        $control_ops = array(
            'id_base' => 'awp_random_posts',
            'width'   => 200,
            'height'  => 250,
        );
        parent::__construct( 'awp_random_posts', __( 'AWP Random Posts', 'awp' ), $widget_ops, $control_ops );
    }
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $post_count = $instance['post_count'];
        // before and after widget arguments are defined by themes

        echo $args['before_widget'];
        if ( ! empty( $title ) )
        echo $args['before_title']. $title. $args['after_title'];

        $arg = new WP_Query(
        array(
            "posts_per_page" => $post_count,
            "post_type" => "post",
            "post_status" => "publish",
            "orderby" => "rand"
        )
    );
    global $post;
    if($arg->have_posts()) { echo '<ul class="awp-list">';
        while ( $arg->have_posts() ) : $arg->the_post();
        echo'<li class="awp-post-item">';
        echo '<h4 class="awp-post-title"><a href="'.get_permalink($post->ID).'">'.the_title('', '', false).'</a></h4>';
        if($instance['show_image']){
            echo '<div class="awp-featured-image">';
            awp_display_featured_image();
            echo '</div>';
        }
        if($instance['show_date'] || $instance['show_author'] )
        {
            echo '<div class="awp-post-meta">';
        }
        if($instance['show_date']){
            echo "On: ";
            echo the_date('','','',true);
        }
        if($instance['show_author']){
            echo " By: ";
            awp_display_post_author_name();
        }
        if($instance['show_date'] || $instance['show_author'] )
        {
            echo '</div>';
        }
        if($instance['show_category']){
            echo "<div><strong>Post Category: </strong>";
            echo get_the_category_list()."</div>";
        }
        if($instance['show_comment_number']){
            echo "<div>";
            comments_number();
            echo "</div>";
        }
        if($instance['show_excerpt']){
            echo "<div class='awp-post-excerpt'>";
            the_excerpt();
            echo "</div></p>";
        }
        echo "</li>";
    endwhile;
    echo "</ul>";
}
echo $args['after_widget'];
}

public function form( $instance ) {
    $defaults = array( 'title' => __('Random Posts'), 'post_count' => '5', 'show_image' => __('0')
    , 'show_date' => __('0'), 'show_author' => __('0'), 'show_category' => __('0')
    , 'show_comment_number' => __('0'), 'show_excerpt' => __('0'), 'read_more' => __('Read More..'));
    $instance = wp_parse_args( (array) $instance, $defaults );
    if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
        $post_count=$instance['post_count'];
    }
    else {
        $title =$defaults['title'];
        $post_count=$defaults['post_count'];
    }?>
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'awp'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_image'], 'on'); ?> id="<?php echo $this->get_field_id('show_image'); ?>" name="<?php echo $this->get_field_name('show_image'); ?>" />
        <label for="<?php echo $this->get_field_id('show_image'); ?>"><?php _e('Display Featured Image', 'awp'); ?></label>
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_date'], 'on'); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
        <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Display Post Date', 'awp'); ?></label>
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_author'], 'on'); ?> id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" />
        <label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Display Post Author', 'awp'); ?></label>
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_category'], 'on'); ?> id="<?php echo $this->get_field_id('show_category'); ?>" name="<?php echo $this->get_field_name('show_category'); ?>" />
        <label for="<?php echo $this->get_field_id('show_category'); ?>"><?php _e('Display Post Category', 'awp'); ?></label>
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_comment_number'], 'on'); ?> id="<?php echo $this->get_field_id('show_comment_number'); ?>" name="<?php echo $this->get_field_name('show_comment_number'); ?>" />
        <label for="<?php echo $this->get_field_id('show_comment_number'); ?>"><?php _e('Display Number of Comments', 'awp'); ?></label>
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_excerpt'], 'on'); ?> id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" />
        <label for="<?php echo $this->get_field_id('show_excerpt'); ?>"><?php _e('Display Post Excerpt', 'awp'); ?></label>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Number of Posts', 'awp'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('post_count'); ?>" name="<?php echo $this->get_field_name('post_count'); ?>" type="number" value="<?php echo $post_count;?>">
    </p>
    <?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['post_count'] = strip_tags( $new_instance['post_count'] );
    $instance['show_image'] = $new_instance['show_image'];
    $instance['show_date'] = $new_instance['show_date'];
    $instance['show_author'] = $new_instance['show_author'];
    $instance['show_category'] = $new_instance['show_category'];
    $instance['show_comment_number'] = $new_instance['show_comment_number'];
    $instance['show_excerpt'] = $new_instance['show_excerpt'];
    return $instance;
}
}
?>
