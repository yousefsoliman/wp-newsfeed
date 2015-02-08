<?php
/**
 * Plugin Name: Newsfeed
 */
class Newsfeed extends WP_Widget
{

    public function __construct() {
        parent::__construct('newsfeed', 'Newsfeed', array('description' => 'Add RSS feed content from another blog source to the sidebar.'));
    }

    public function get_rss_posts($url, $count) {
        $rss = fetch_feed($url);
        $max = $rss->get_item_quantity($count);
        return $rss->get_items(0, $max);
    }

    public function widget($args, $instance) {
        if (empty($instance['turnoff_css'])) wp_enqueue_style('newsfeed', plugin_dir_url(__FILE__) . 'newsfeed.css');
        ?>
        <div class="newsfeed">
        <?php
        $posts = $this->get_rss_posts($instance['rss_feed_url'], $instance['display_number_posts']);
        foreach ($posts as $post) {
            ?>
            <div class="newsfeed-post">
                <a target="_blank" href="<?php echo $post->get_permalink(); ?>" class="newsfeed-post-title"><?php echo $post->get_title(); ?></a>
                <p class="newsfeed-post-description"><?php echo substr($post->get_description(), 0, $instance['cap_description_length']); if ($instance['append_ellipsis']) echo ' [...]'; ?></p>
                <?php if ($instance['show_date']): ?><p class="newsfeed-post-date"><?php echo $post->get_date('j F Y'); ?></p><?php endif; ?>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
    }

    public function update($new_instance, $old_instance) {
        return $new_instance;
    }

    public function form($instance) {
        ?>
        <div>
            <p>
                <label>RSS Feed URL:</label>
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('rss_feed_url'); ?>" value="<?php echo $instance['rss_feed_url']; ?>">
            </p>
            <p>
                <label>Number of Posts to Display:</label>
                <input type="text" size="3" name="<?php echo $this->get_field_name('display_number_posts'); ?>" value="<?php echo $instance['display_number_posts']; ?>">
            </p>
            <p>
                <label>Cap Description Length:</label>
                <input type="text" size="3" name="<?php echo $this->get_field_name('cap_description_length'); ?>" value="<?php echo $instance['cap_description_length']; ?>">
                <label>words</label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('show_date'); ?>" <?php if ($instance['show_date']) echo 'checked'; ?>>
                <label>Show date</label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('append_ellipsis'); ?>" <?php if ($instance['append_ellipsis']) echo 'checked'; ?>>
                <label>Append [...] to description</label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('turnoff_css'); ?>" <?php if ($instance['turnoff_css']) echo 'checked'; ?>>
                <label>Turn off default CSS styles</label>
            </p>
        </div>
        <?php
    }
}

function register_newsfeed_as_widget() {
    register_widget('Newsfeed');
}

add_action('widgets_init', 'register_newsfeed_as_widget');
?>