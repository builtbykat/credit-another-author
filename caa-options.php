<?php
class Caa_Options {
    public function __construct() {
        add_action( 'admin_menu', array(&$this, 'caaSettings'));
        add_action('admin_init', array(&$this, 'pluginAdminInit'));
        add_action('wp_enqueue_scripts', array(&$this, 'registerScript'));
        add_action('wp_footer', array(&$this, 'printScript'));
    }
    public function caaSettings() {
        add_options_page( 
            'Credit Another Author Settings', 
            'Credit Another Author Settings', 
            'manage_options', 
            'caa-settings.php', 
            array(&$this, 'caaOptionsPage')
        );
    }
    public function caaOptionsPage() { 
    ?>
        <div class="wrap">
            <h1>Credit Another Author Settings</h1>
            <form method="post" action="options.php"> 
                <?php settings_fields('caa_options'); ?>
                <?php do_settings_sections('plugin'); ?>
                <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
            </form>
    <?php
    }
    public function pluginAdminInit(){
        register_setting( 'caa_options', 'caa_options', array(&$this, 'caaOptionsValidate'));
        add_settings_section('caa_main', 'Main Settings', array(&$this, 'caaSectionText'), 'plugin');
        add_settings_field('caa_a_class', 'author <code>.class</code>', array(&$this, 'aClassField'), 'plugin', 'caa_main');
        add_settings_field('caa_i_class', 'img <code>.class</code>', array(&$this, 'iClassField'), 'plugin', 'caa_main');
    }
    public function caaSectionText() { 
    ?>
        <p><code>.class</code> of  element containing author you wish to replace</p>
    <?php
    }
    public function aClassField() { 
        $options = get_option('caa_options');
    ?>
        <input class="widefat" type="text" name="caa_options[author-class]" id="author-class" size="40" value="<?php echo array_key_exists('author-class', $options)?  $options['author-class'] : '' ?>" />
    <?php
    }
    public function iClassField() { 
        $options = get_option('caa_options');
    ?>
        <input class="widefat" type="text" name="caa_options[img-class]" id="img-class" size="40" value="<?php echo array_key_exists('img-class', $options) ? $options['img-class'] : '' ?>" />
    <?php
    }
    public function caaOptionsValidate($input) {
        $newinput = array();
        foreach ($input as $key=>$value) {
        $newinput[$key] = trim($input[$key]);
        // TODO: can there be an all-in-one || validate separately
        /*if(!preg_match('/^[a-z0-9]$/i', $newinput['author-class'])) {
            $newinput['author-class'] = '';
        }*/
        }
        return $newinput;
    }
    public function registerScript()
    {
        wp_register_script('caa-options-script', plugins_url().'/'.plugin_basename(__DIR__).'/caa-options.js');
        $options = get_option('caa_options');
        if ($options['author-class'] != '') {
            wp_localize_script( 'caa-options-script', 'author_class', array('val' => $options));
        }
    }
    public function printScript()
    {
            wp_print_scripts('caa-options-script');
    }
}
new Caa_Options();