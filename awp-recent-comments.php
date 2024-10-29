<?php
class Awp_Recent_Comments extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'awp_recent_comments awp_widget', 'description' => __('Widget to display recent comments.', 'awp'));
        $control_ops = array('width' => 200, 'height' => 250);
        parent::__construct(false, $name = __('AWP Recent Comments', 'awp'), $widget_ops, $control_ops );
        $this->alt_option_name = 'widget_recent_comments';
        if ( is_active_widget(false, false, $this->id_base) )
        add_action( 'comment_post', array($this, 'flush_widget_cache') );
        add_action( 'edit_comment', array($this, 'flush_widget_cache') );
        add_action( 'transition_comment_status', array($this, 'flush_widget_cache') );
    }
    function widget( $args, $instance ) {
        global $comments, $comment,$image,$com;
        extract($args, EXTR_SKIP);
        $output = '';
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments', 'awp' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number )
        $number = 5;
        $comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish' ) ) );
        $output .= $before_widget;
        if ( $title )
        $output .= $before_title . $title . $after_title;
        $output .= '<ul class="awp-list">';
        if ( $comments ) {
            // Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
            $post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
            _prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

            foreach ( (array) $comments as $comment) {
                $posttitle=get_the_title($comment->comment_post_ID);
                if($instance['show_comment']){
                    $com=get_comment_text();
                }
                if($instance['show_gravatar']){
                    $comm=get_comment_ID();
                    $id = get_comment($comm);
                    $image=get_avatar($id,'32');
                }
                $output .=  '<li class="awp-post-item">' .sprintf(_x('%1$s %2$s on %3$s %4$s', 'awp'), '<div class="awp-comment-avatar">'.$image .'</div><div class="awp-post-comment">',get_comment_author_link(), '<a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . $posttitle . '</a></div>' ,'<div class="awp-comment-text">'.$com.'</div>') . '</li>';
            }
        }
        $output .= $after_widget;
        echo $output;
        $cache[$args['widget_id']] = $output;
        wp_cache_set('widget_recent_comments', $cache, 'widget');
    }
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = absint( $new_instance['number'] );
        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_recent_comments']) )
        delete_option('widget_recent_comments');
        $instance['show_comment']=strip_tags($new_instance['show_comment']);
        $instance['show_gravatar']=strip_tags($new_instance['show_gravatar']);
        return $instance;
    }
    function form( $instance ) {
        $defaults=array('title'=>'Recent Comments','show_comment'=>'0','number'=>'5','show_gravatar'=>'0');
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'awp' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_comment'], 'on'); ?> id="<?php echo $this->get_field_id('show_comment'); ?>" name="<?php echo $this->get_field_name('show_comment'); ?>" />
            <label for="<?php echo $this->get_field_id('show_comment'); ?>"><?php _e( 'Display comment-text', 'awp'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:', 'awp' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
            <p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked($instance['show_gravatar'], 'on'); ?> id="<?php echo $this->get_field_id('show_gravatar'); ?>" name="<?php echo $this->get_field_name('show_gravatar'); ?>" />
                    <label for="<?php echo $this->get_field_id('show_gravatar'); ?>"><?php _e( 'Display commenter\'s gravatar', 'awp' ); ?></label>
                </p>
                <?php
            }
        }
