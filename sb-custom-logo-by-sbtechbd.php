<?php
/*
Plugin Name: sb custom logo by sbtechbd
Plugin URI: https://wordpress.org/plugins/sb-custom-logo-by-sbtechbd
Description: A plugin to change the WordPress login logo with an option to upload it from the admin panel.
Version: 1.0
Author: subrata-deb-nath
Author URI: https://subrata6630.github.io/
Text Domain: sb-custom-logo-by-sbtechbd
License: GPLv2
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add settings page
add_action('admin_menu', 'sbclb_add_admin_menu');
add_action('admin_init', 'sbclb_settings_init');
add_action('admin_enqueue_scripts', 'sbclb_enqueue_media_uploader');
add_action('login_enqueue_scripts', 'sbclb_custom_logo'); // Enqueue styles for login page

// Add Admin Menu
function sbclb_add_admin_menu()
{
    add_options_page(
        esc_html__('Sb Custom Logo by sbtechbd', 'sb-custom-logo-by-sbtechbd'),
        esc_html__('Sb Custom Logo', 'sb-custom-logo-by-sbtechbd'),
        'manage_options',
        'sbclb_custom_logo',
        'sbclb_options_page'
    );
}

// Register Settings
function sbclb_settings_init()
{
    register_setting('pluginPage', 'sbclb_settings', 'sanitize_text_field');

    add_settings_section(
        'sbclb_pluginPage_section',
        esc_html__('Upload your custom login logo', 'sb-custom-logo-by-sbtechbd'),
        'sbclb_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'sbclb_logo',
        esc_html__('Logo URL', 'sb-custom-logo-by-sbtechbd'),
        'sbclb_logo_render',
        'pluginPage',
        'sbclb_pluginPage_section'
    );
}

// Enqueue Media Uploader
function sbclb_enqueue_media_uploader($hook_suffix)
{
    if ($hook_suffix === 'settings_page_sbclb_custom_logo') {
        wp_enqueue_media(); // Enqueue media library
        wp_enqueue_script('jquery');

        // Register the custom script with a version
        wp_register_script(
            'sbclb-custom-logo-script',
            plugins_url('/assets/js/custom-logo.js', __FILE__),
            array('jquery'),
            '1.0', // Version number for caching
            true
        );

        // Enqueue the registered script
        wp_enqueue_script('sbclb-custom-logo-script');

        // Inline JavaScript for media uploader
        $inline_js = "
            jQuery(document).ready(function($) {
                $('#upload-logo-button').click(function(e) {
                    e.preventDefault();
                    var image = wp.media({
                        title: '" . esc_js(__('Upload Logo', 'sb-custom-logo-by-sbtechbd')) . "',
                        multiple: false,
                        library: { type: 'image' }
                    }).open().on('select', function() {
                        var uploaded_image = image.state().get('selection').first();
                        var image_url = uploaded_image.toJSON().url;
                        $('input[name=\"sbclb_settings[sbclb_logo]\"]').val(image_url);
                    });
                });
            });
        ";
        
        wp_add_inline_script('sbclb-custom-logo-script', $inline_js);
    }
}

// Render Logo Input Field
function sbclb_logo_render()
{
    $options = get_option('sbclb_settings');
    $logo_url = isset($options['sbclb_logo']) ? esc_url($options['sbclb_logo']) : '';
?>
<input type='text' name='sbclb_settings[sbclb_logo]' value='<?php echo esc_attr($logo_url); ?>'>
<input type="button" value="<?php esc_attr_e('Upload Image', 'sb-custom-logo-by-sbtechbd'); ?>" id="upload-logo-button"
    class="button-secondary">
<p class="description"><?php esc_html_e('Enter a URL or upload an image.', 'sb-custom-logo-by-sbtechbd'); ?></p>
<?php
}

// Settings Section Callback
function sbclb_settings_section_callback()
{
    echo esc_html__('Upload your custom login logo. Change the WordPress login logo easily from this panel.', 'sb-custom-logo-by-sbtechbd');
}

// Options Page
function sbclb_options_page()
{
?>
<form action='options.php' method='post'>
    <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
</form>
<?php
}

// Apply the custom logo to the login page
function sbclb_custom_logo()
{
    $options = get_option('sbclb_settings');
    $logo_url = isset($options['sbclb_logo']) ? esc_url($options['sbclb_logo']) : '';

    // Add inline style for the login page logo
    ?>
<style type="text/css">
#login h1 a {
    background-image: url('<?php echo esc_url($logo_url); ?>');
    width: 100%;
    height: auto;
    background-size: contain;
    background-repeat: no-repeat;
    padding-bottom: 30px;
}
</style>
<?php
}

// Register styles with versioning only on specific pages
function sbclb_register_styles() {
    if (is_admin()) {
        // Only load styles on admin pages
        wp_register_style(
            'sbclb-custom-logo-style',
            plugins_url('/assets/css/custom-logo.css', __FILE__),
            array(),
            '1.0' // Version number for caching
        );
        wp_enqueue_style('sbclb-custom-logo-style');
    }
}
add_action('admin_enqueue_scripts', 'sbclb_register_styles');

?>