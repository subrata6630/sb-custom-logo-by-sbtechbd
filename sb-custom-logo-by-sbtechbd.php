<?php
/*
Plugin Name: Sb custom logo by sbtechbd
Plugin URI: https://wordpress.org/plugins/sb-custom-logo-by-sbtechbd
Description: A plugin to change the WordPress login logo with an option to upload it from the admin panel.
Version: 1.2
Author: subrata-deb-nath
Author URI: https://subrata6630.github.io/
Text Domain: sb-custom-logo-by-sbtechbd
License: GPLv2
Domain Path: /languages
*/

/**
*
* Exit if accessed directly
*
**/
if (!defined('ABSPATH')) {
    exit;
}

/* Defining Constant */
define("SBCL_VERSION", '1.2');

/* Add body class for options page */
function sbcl_admin_body_class($classes) {
    global $pagenow;
    $screen = get_current_screen(); 

    if (in_array($pagenow, array('options-general.php'), true) && $screen->id === 'settings_page_change_login_logo') {
        $classes .= ' sbcl-option-page';
    }

    return $classes;
}

add_filter('admin_body_class', 'sbcl_admin_body_class');

/* Adding Styles for the option page */
function sbcl_styles_option_page() {
    global $pagenow;
    $screen = get_current_screen(); 

    if (in_array($pagenow, array('options-general.php'), true) && $screen->id === 'settings_page_change_login_logo') {
        ?>
        <style type="text/css">
            .sbcl-option-page table.form-table tbody {
                background-color: #fff;
            }
            .sbcl-option-page table.form-table tbody tr:not(:last-child) {
                border-bottom: 1px solid #eee;
            }
            .sbcl-option-page table.form-table tbody th {
                padding: 15px 10px;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'sbcl_styles_option_page');

/* Settings to manage WP login logo */
function sbcl_register_custom_logo_settings() {
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_logo_url');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_set_bg');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_bg_color');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_bg_img_url');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_logo_link');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_link_color');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_link_hover_color');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_logo_width');
    register_setting('sbcl_change_login_options_group', 'sbcl_wp_logo_height');
}
add_action('admin_init', 'sbcl_register_custom_logo_settings');

function sbcl_register_login_logo_setting_page() {
    add_options_page('Custom Login Logo', 'Custom Login Logo', 'manage_options', 'change_login_logo', 'sbcl_change_wordpress_login_logo');
}
add_action('admin_menu', 'sbcl_register_login_logo_setting_page');

function sbcl_change_wordpress_login_logo() {
    wp_enqueue_script('jquery');
    wp_enqueue_media();

    $cur_logo = esc_attr(get_option('sbcl_wp_logo_url', ''));
    $cur_bg_img = esc_attr(get_option('sbcl_wp_bg_img_url', ''));

    do_action('sbcl_settings_start');

    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Custom Login Logo Settings', 'sb-custom-logo-by-sbtechbd'); ?></h1>
        <p><?php echo esc_html__('Change the default WordPress logo and set your own site logo.', 'sb-custom-logo-by-sbtechbd'); ?></p>
        <form method="post" action="options.php">
            <?php settings_fields('sbcl_change_login_options_group'); ?>
            <?php do_settings_sections('sbcl_change_login_options_group'); ?>
            <table class="form-table">
                <!-- Logo Section -->
                <?php if ($cur_logo !== ""): ?>
                <tr valign="top">
                    <th>Current Logo</th>
                    <td>
                        <img class="sbcl_current_logo" src="<?php echo esc_url(get_option('sbcl_wp_logo_url')); ?>" alt="<?php echo esc_html__('Current Logo', 'sb-custom-logo-by-sbtechbd') ?>" width="220">
                    </td>
                </tr>
                <?php endif; ?>
                
                <!-- Add more form elements here -->

            </table>
            <p class="submit submitbox change_login_logo-setting-btn">
                <?php 
                    submit_button(__('Save Settings', 'sb-custom-logo-by-sbtechbd'), 'primary', 'change_login_logo-save-settings', false);
                ?>
            </p>
        </form>
    </div>
    <?php
}

/* Adding Backend Scripts */
function sbcl_backend_scripts() {
    $screen = get_current_screen(); 
    if ($screen->id === 'settings_page_change_login_logo') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('sbcl-backend', plugins_url('/assets/backend.js', __FILE__), array('jquery'), SBCL_VERSION, 'true');
        $values = array(
            'bg_type' => get_option('sbcl_wp_set_bg', 'color')
        );
        wp_localize_script('sbcl-backend', 'sbcl_admin', $values);
    }
}
add_action('admin_enqueue_scripts', 'sbcl_backend_scripts');

/* Custom WordPress admin login header logo */
function sbcl_wordpress_custom_login_logo() {
    // Add your logo customization code
}
add_action('login_head', 'sbcl_wordpress_custom_login_logo');

/* Add action links to plugin list */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sbcl_add_change_wordpress_login_logo_action_links');
function sbcl_add_change_wordpress_login_logo_action_links($links) {
    $settings_link = array(
         '<a href="' . admin_url('options-general.php?page=change_login_logo') . '">Logo Settings</a>'
    );
    return array_merge($links, $settings_link);
}

/* Reset the settings */
function sbcl_reset_settings() {
    if (isset($_GET['action']) && 'reset' === $_GET['action']) {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'change_login_logo-settings')) {
            die(esc_html__('Security check', 'sb-custom-logo-by-sbtechbd')); 
        } else {
            delete_option('sbcl_wp_logo_url');
            delete_option('sbcl_wp_set_bg');
            delete_option('sbcl_wp_bg_color');
            delete_option('sbcl_wp_bg_img_url');
            delete_option('sbcl_wp_logo_link');
            delete_option('sbcl_wp_link_color');
            delete_option('sbcl_wp_link_hover_color');
            delete_option('sbcl_wp_logo_width');
            delete_option('sbcl_wp_logo_height');
            wp_safe_redirect(admin_url('options-general.php?page=change_login_logo'));
            exit();
        }
    }
}
add_action('sbcl_settings_start', 'sbcl_reset_settings');

function sbcl_add_class_login_page($classes) {
    $classes[] = "sbcl_loaded";
    return $classes;
}

add_filter('login_body_class', 'sbcl_add_class_login_page');
?>
