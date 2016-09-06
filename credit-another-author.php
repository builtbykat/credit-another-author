<?php
/*
* Plugin Name: Credit Another Author
* Author: Kat S
* Author URI: none
* Plugin URI: none
* Version: 1.0
* Description: Adds post meta to credit another author if metakey exists
*/

// FUTURE WORK
// can we pull this 'swap' off in an author's blog listing page?
// support multiple classes
// option to / to not prefix .post_{id} to classes



function credit_other() {
    new Credit_Other();
}

add_action( 'load-post.php', 'credit_other' );
add_action( 'load-post-new.php', 'credit_other' );
add_action('posts_selection', 'credit_other');

class Credit_Other {
    public function __construct()
    {
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'creditOtherMetaBox'));
            add_action('save_post', array($this, 'saveAuthor'));
        }
        add_action('wp_enqueue_scripts', array($this, 'registerScript'));
        add_action('wp_footer', array($this, 'printScript'));
        add_action('the_post', array($this, 'getPostId'));
    }
    public function creditOtherMetaBox()
    {
        $id = 'credit-other';
        $title = __('Credit Another Author');
        $callback = array(&$this, 'metaBox');
        $post_type = 'post';
        $context = 'side';
        $priority = 'high';
        add_meta_box( $id, $title, $callback, $post_type, $context, $priority);
    }
    public function metaBox()
    {
        global $post;

        wp_nonce_field('make-nonce','credit-other-nonce');

        $meta_value = (array) get_post_meta($post->ID, 'credit-other', true);

        ?>
            <p>
                <label for="credit-other">
                    <?php _e('<em>Will replace actual author in the byline</em><br/><br/>Name'); ?>
                </label>
                <input class="widefat" type="text" name="credit-other[name]" id="credit-other" value="<?php echo array_key_exists('name', $meta_value) ? $meta_value['name'] : '' ?>" /><br/><br/>
                <label for="link-other">
                    <?php _e('URL (link author name to something)'); ?>
                </label>
                <input class="widefat" type="text" name="credit-other[url]" id="link-other" value="<?php echo array_key_exists('url', $meta_value) ? $meta_value['url'] : '' ?>" /><br/><br/>
                <label for="avatar-other">
                    <?php _e('Image (upload to media library then paste image url here)'); ?>
                </label>
                <input class="widefat" type="text" id="avatar-other" name="credit-other[img]" value="<?php echo array_key_exists('img', $meta_value) ? $meta_value['img'] : '' ?>"/>
                <input type="hidden" name="credit-other[id]" value="<?php echo $post->ID; ?>" />
            </p>
        <?php
    }
    public function saveAuthor()
    {
        global $post;

        $post_id = $post->ID;

        if (!isset($_POST['credit-other-nonce']) || !wp_verify_nonce($_POST['credit-other-nonce'], 'make-nonce'))
            return $post_id;

        if (!current_user_can('edit_posts', $post_id))
            return $post_id;

        foreach ($_POST['credit-other'] as $key=>$value) {
            $new_meta_value[$key] = ( isset( $value ) ? sanitize_text_field( $value ) : '' );
            $meta_key = 'credit-other';
            $meta_value = get_post_meta( $post_id, $meta_key, true );
        }
        if ($new_meta_value == '') {
            delete_post_meta( $post_id, $meta_key, $meta_value );
        } elseif ($meta_value != $new_meta_value) {
            update_post_meta( $post_id, $meta_key, $new_meta_value );
        }
    }
    public function registerScript()
    {
        $post_id = $this->getPostId();
        wp_register_script('credit-other-script', plugins_url().'/'.plugin_basename(__DIR__).'/credit-other.js');
        $meta_value = (array) get_post_meta( $post_id, 'credit-other', true );
        wp_localize_script( 'credit-other-script', 'other_author', array('val' => $meta_value));
    }
    public function getPostId() {
        global $post;
        return $post->ID;
    }
    public function printScript()
    {
            wp_print_scripts('credit-other-script');
    }
}

include_once('caa-options.php');