<?php
class Awp_Author_Bio extends WP_Widget {
    // Controller
    protected $defaults;
    function __construct() {
        $this->defaults = array(
            'title'          => 'Author Details',
            'alignment'	 => 'left',
            'user'           => '',
            'size'           => '45',
            'author_info'    => 'bio',
            'text-bio'       => '',
            'page'           => '',
            'page_link_text' => __( 'Read More', 'awp' ) . '&#x02026;',
            'posts_link'     => '0',
            'sort_radiobox'  => '0',
        );
        $widget_ops = array(
            'classname'   => __('awp_author_bio awp_widget', 'awp'),
            'description' => __( 'Displays user profile block with Gravatar', 'awp' ),
        );
        $control_ops = array(
            'id_base' => 'author_bio',
            'width'   => 200,
            'height'  => 250,
        );
        parent::__construct( 'author_bio', __( 'AWP Author Details', 'awp' ), $widget_ops, $control_ops );

    }
    function widget($args, $instance) {
        extract ($args);
        $instance = wp_parse_args((array) $instance, $this->defaults);
        echo $before_widget;
        echo "<div class='awp-post-item awp-author-inner'>";

        if ( $instance['title']) {
            echo $args['before_title'] .$instance['title']. $args['after_title'];
        }
        $text='';
        $text.=get_the_author_meta('user_firstname',$instance['user']);
        $text.=" ".get_the_author_meta('user_lastname',$instance['user']);
        if($instance['alignment']){
            $text .= '<span class="align' . esc_attr( $instance['alignment'] ) . '">';
        }
        $text.=get_avatar($instance['user'],$instance['size']);
        if($instance['alignment'])
        $text.='</span>';
        if($instance['sort_radiobox']=="bio")
        $text.="<div class='awp-author-details'>".get_the_author_meta('description',$instance['user'])."</div>";
        else
        $text.='<div class="awp-author-details">'.$instance['text-bio']."</div>";
        $text .= $instance['page'] ? sprintf( ' <a class="awp-pagelink" href="%s">%s</a>', get_page_link( $instance['page'] ), $instance['page_link_text'] ) : '';
        echo wpautop($text);
        if ( $instance['posts_link'] )
        printf( '<div class="awp-posts-link"><a href="%s">%s</a></div>', get_author_posts_url( $instance['user'] ), __( 'View My Blog Posts', 'awp' ) );

        echo '</div>';
        echo $after_widget;
    }
    function form($instance) {
        $instance = wp_parse_args( (array) $instance, $this->defaults );  ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'awp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <?php echo __('Select a user. the email address for this account will be used to pull the Gravatar image.', 'awp'); ?>
        </p>
        <?php wp_dropdown_users( array( 'who' => 'authors', 'name' => $this->get_field_name( 'user' ), 'selected' => $instance['user'] ) ); ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Gravatar Size', 'awp' ); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>">
                <?php
                $sizes = array( __( 'Small', 'awp' ) => 45, __( 'Medium', 'awp' ) => 65, __( 'Large', 'awp' ) => 85, __( 'Extra Large', 'awp' ) => 125 );
                $sizes = apply_filters( 'solo_gravatar_sizes', $sizes );
                foreach ( (array) $sizes as $label => $size ) { ?>
                    <option value="<?php echo absint( $size ); ?>" <?php selected( $size, $instance['size'] ); ?>><?php printf( '%s (%spx)', $label, $size ); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'alignment' ); ?>"><?php _e( 'Gravatar Alignment', 'awp' ); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'alignment' ); ?>" name="<?php echo $this->get_field_name( 'alignment' ); ?>">

                <option value="left" <?php selected( 'left', $instance['alignment'] ); ?>><?php _e( 'Left', 'awp' ); ?></option>
                <option value="center" <?php selected( 'center', $instance['alignment'] ); ?>><?php _e( 'Center', 'awp' ); ?></option>
                <option value="right" <?php selected( 'right', $instance['alignment'] ); ?>><?php _e( 'Right', 'awp' ); ?></option>
            </select>
        </p>
        <p>
            <?php echo __('Select the text you want to use as author description.', 'awp'); ?>
        </p>
        <p>
            <input type="radio"
            id="<?php echo $this->get_field_id('sort_radiobox'); ?>"
            name="<?php echo $this->get_field_name('sort_radiobox'); ?>"
            <?php if (isset($instance['sort_radiobox']) && $instance['sort_radiobox']=="bio") echo "checked";?>
            value="bio"><label for="<?php echo $this->get_field_id('posts_link'); ?>"><?php echo __('Author Bio', 'awp') ?></label><br>
            <input type="radio"
            id="<?php echo $this->get_field_id('sort_radiobox'); ?>"
            name="<?php echo $this->get_field_name('sort_radiobox'); ?>"
            <?php if (isset($instance['sort_radiobox']) && $instance['sort_radiobox']=="text") echo "checked";?>
            value="text"><label for="<?php echo $this->get_field_id('posts_link'); ?>"><?php echo __('Custom Text', 'awp') ?></label>
            <textarea class="widefat" rows="6" cols="4" id="<?php echo $this->get_field_id('text-bio'); ?>"
                name="<?php echo $this->get_field_name('text-bio'); ?>" value="<?php echo $instance['text-bio']; ?>"><?php echo htmlspecialchars($instance['text-bio']);?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('posts_link'); ?>"><?php echo __('Choose your "about me" page from the list below. this will be the page linked at the end of the about me section.', 'awp') ?></label>
                <?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'page' ), 'show_option_none' => __( 'None', 'awp' ), 'selected' => $instance['page'] ) ); ?>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('page_link_text'); ?>"><?php _e('Extended page link text', 'awp'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('page_link_text'); ?>" name="<?php echo $this->get_field_name('page_link_text'); ?>" type="text" value="<?php echo $instance['page_link_text']; ?>" />
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance['posts_link'], 'on'); ?> id="<?php echo $this->get_field_id('posts_link'); ?>" name="<?php echo $this->get_field_name('posts_link'); ?>" />
                <label for="<?php echo $this->get_field_id('posts_link'); ?>"><?php echo __('Show Author Archive link?', 'awp') ?></label>
            </p>
        <?php }

        function update($new_instance,$old_instance){
            $new_instance['title']          = strip_tags( $new_instance['title'] );
            $new_instance['bio_text']       = current_user_can( 'unfiltered_html' ) ? $new_instance['bio_text'] : solo_formatting_kses( $new_instance['bio_text'] );
            $new_instance['page_link_text'] = strip_tags( $new_instance['page_link_text'] );
            return $new_instance;
        }
    }
