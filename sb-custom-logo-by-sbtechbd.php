<?php
/*
Plugin Name: sb custom logo by sbtechbd
Plugin URI: https://github.com/subrata6630/sb-custom-logo-by-sbtechbd
Description: A plugin to change the WordPress login logo with an option to upload it from the admin panel.
Version: 1.2
Author: subrata-deb-nath
Author URI: https://subrata6630.github.io/
Text Domain: sb-custom-logo-by-sbtechbd
License: GPLv2
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Load plugin text domain for translations.
function sbclb_load_textdomain()
{
    load_plugin_textdomain('sb-custom-logo-by-sbtechbd', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'sbclb_load_textdomain');

// Add settings page.
function sbclb_add_admin_menu()
{
    add_options_page(
        __('Custom Logo Settings', 'sb-custom-logo-by-sbtechbd'),
        __('Login Logo', 'sb-custom-logo-by-sbtechbd'),
        'manage_options',
        'sbclb-custom-logo',
        'sbclb_settings_page'
    );
}
add_action('admin_menu', 'sbclb_add_admin_menu');

// Register settings and add sanitization.
function sbclb_register_settings()
{
    register_setting('sbclb_settings', 'sbclb_options', 'sbclb_options_sanitize');
}
add_action('admin_init', 'sbclb_register_settings');

// Sanitize settings input.
function sbclb_options_sanitize($input)
{
    $output = [];
    // Sanitize Logo Image URL
    $output['logo_image'] = esc_url_raw($input['logo_image'] ?? '');
    // Sanitize Logo Width
    $output['logo_width'] = intval($input['logo_width'] ?? 84);
    // Sanitize Logo Height
    $output['logo_height'] = intval($input['logo_height'] ?? 84);
    // Sanitize Bottom Margin
    $output['bottom_margin'] = intval($input['bottom_margin'] ?? 20);
    // Sanitize Logo Link URL
    $output['logo_link'] = esc_url_raw($input['logo_link'] ?? home_url());
    return $output;
}

// Enqueue WordPress media uploader scripts.
function sbclb_admin_scripts($hook)
{
    if ('settings_page_sbclb-custom-logo' !== $hook) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script(
        'sbclb-admin-script',
        plugin_dir_url(__FILE__) . '/assets/js/admin.js',
        ['jquery'],
        '1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'sbclb_admin_scripts');

// Settings page HTML.
function sbclb_settings_page()
{
    $options = get_option('sbclb_options');
?>
    <div class="wrap">
        <h1><?php esc_html_e('Custom Login Logo Settings', 'sb-custom-logo-by-sbtechbd'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('sbclb_settings');
            do_settings_sections('sbclb_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Logo Image', 'sb-custom-logo-by-sbtechbd'); ?></th>
                    <td>
                        <input type="url" id="sbclb_logo_image" name="sbclb_options[logo_image]"
                            value="<?php echo esc_url($options['logo_image'] ?? ''); ?>" class="regular-text">
                        <button type="button" class="button"
                            id="sbclb_upload_button"><?php esc_html_e('Upload Image', 'sb-custom-logo-by-sbtechbd'); ?></button>
                        <p><?php esc_html_e('Enter the URL or upload the image below.', 'sb-custom-logo-by-sbtechbd'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Logo Width (px)', 'sb-custom-logo-by-sbtechbd'); ?></th>
                    <td><input type="number" name="sbclb_options[logo_width]"
                            value="<?php echo esc_attr($options['logo_width'] ?? '84'); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Logo Height (px)', 'sb-custom-logo-by-sbtechbd'); ?></th>
                    <td><input type="number" name="sbclb_options[logo_height]"
                            value="<?php echo esc_attr($options['logo_height'] ?? '84'); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Bottom Margin (px)', 'sb-custom-logo-by-sbtechbd'); ?>
                    </th>
                    <td><input type="number" name="sbclb_options[bottom_margin]"
                            value="<?php echo esc_attr($options['bottom_margin'] ?? '20'); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Logo Link URL', 'sb-custom-logo-by-sbtechbd'); ?></th>
                    <td><input type="url" name="sbclb_options[logo_link]"
                            value="<?php echo esc_url($options['logo_link'] ?? home_url()); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Add custom styles to the login page.
function sbclb_styles()
{
    $options = get_option('sbclb_options');
    $logo_url = esc_url($options['logo_image'] ?? '');
    $logo_width = esc_attr($options['logo_width'] ?? '84');
    $logo_height = esc_attr($options['logo_height'] ?? '84');
    $bottom_margin = esc_attr($options['bottom_margin'] ?? '20');

    if ($logo_url) {
        echo "<style>
            #login h1 a {
                background-image: url('{$logo_url}');
                background-size: contain;
                width: {$logo_width}px;
                height: {$logo_height}px;
                margin-bottom: {$bottom_margin}px;
            }
        </style>";
    }
}
add_action('login_enqueue_scripts', 'sbclb_styles');

// Change the login logo URL.
function sbclb_logo_url()
{
    $options = get_option('sbclb_options');
    return esc_url($options['logo_link'] ?? home_url());
}
add_filter('login_headerurl', 'sbclb_logo_url');

// Change the login logo link text (for accessibility).
function sbclb_logo_text()
{
    return get_bloginfo('name');
}
add_filter('login_headertext', 'sbclb_logo_text');
