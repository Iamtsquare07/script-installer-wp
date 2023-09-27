<?php
/*
Plugin Name: Son Scripts Installer
Description: Son Scripts Installer is a powerful and user-friendly WordPress plugin that puts the control of your website's custom code in your hands. Take charge of your site's appearance and functionality by easily adding custom code snippets to the header, body, and footer sections without any hassle.

Key Features:

Seamless Customization: With Son Scripts Installer, you can effortlessly inject custom code into your website's header, body, or footer. Tailor your site to your unique vision and unleash its full potential.

No Coding Required: You don't need to be a coding wizard to enhance your website. The intuitive settings page allows even non-tech-savvy users to manage custom code efficiently.

Header Enrichment: Add meta tags, external stylesheets, JavaScript libraries, and more to the <head> section for improved SEO, integration with third-party services, and enhanced site performance.

Body Magic: Inject custom scripts, tracking codes, analytics, and dynamic content right into the <body> section. Take control of user interactions and enrich their experience.

Footer Enhancements: Enhance your website's footer with custom scripts, copyright notices, social media widgets, and more.

Safe and Secure: Son Scripts Installer prioritizes security, ensuring that only authorized users can modify the site's custom code.

Effortless Installation: Quickly integrate Son Scripts Installer into your WordPress site and start customizing within minutes. No technical expertise required.

Compatibility & Performance: The plugin is lightweight, optimized for performance, and works seamlessly with any theme or plugin.

Stay Up-to-Date: Benefit from regular updates and enjoy compatibility with the latest WordPress versions and standards.

Transform your website into a tailored masterpiece with Son Scripts Installer. Unlock limitless customization possibilities and elevate your online presence like never before. Take control of your WordPress site's destiny today!
Version: 1.3
Author: Tyavbee Victor
Author URI: https://www.iamtsquare07.com
Text Domain: son-scripts-installer
*/

// Allow specific HTML tags and attributes for custom code
function son_scripts_kses_allowed_tags($tags) {
    $tags['script'] = array(
        'type' => true,
        'src' => true,
        'async' => true,
        'defer' => true,
    );

    // Allow additional harmless HTML elements like div, span, p, etc.
    $tags['div'] = array(
        'class' => true,
        'id' => true,
        'style' => true,
    );
    $tags['span'] = array(
        'class' => true,
        'id' => true,
        'style' => true,
    );
    $tags['p'] = array(
        'class' => true,
        'id' => true,
        'style' => true,
    );
    // Add more elements as needed...

    return $tags;
}
add_filter('wp_kses_allowed_html', 'son_scripts_kses_allowed_tags', 10, 2);

// Add settings page for the plugin
function son_scripts_add_settings_page() {
    add_options_page(
        'Son Scripts Installer',
        'Son Scripts Installer',
        'manage_options',
        'son-scripts',
        'son_scripts_render_settings_page'
    );
}
add_action('admin_menu', 'son_scripts_add_settings_page');

// Sanitize and save header, body, and footer code
function son_scripts_save_custom_code() {
    if (isset($_POST['submit'])) {
        check_admin_referer('son_scripts_settings');

        // Sanitize and save header, body, and footer code using wp_unslash
        $header_code = wp_unslash($_POST['header_code']);
        $body_code = wp_unslash($_POST['body_code']);
        $footer_code = wp_unslash($_POST['footer_code']);

        update_option('son_scripts_header_code', $header_code);
        update_option('son_scripts_body_code', $body_code);
        update_option('son_scripts_footer_code', $footer_code);

        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
}

// Render the settings page content
function son_scripts_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    son_scripts_save_custom_code(); // Call the save function here to save the code when the form is submitted.

    // Get saved header, body, and footer code
    $header_code = get_option('son_scripts_header_code', '');
    $body_code = get_option('son_scripts_body_code', '');
    $footer_code = get_option('son_scripts_footer_code', '');
    ?>
    <div class="wrap">
        <h1>Son Scripts Installer Settings</h1>
        <form method="post">
            <?php
            // Add nonce for security
            wp_nonce_field('son_scripts_settings');
            ?>

            <h2>Header Code</h2>
            <textarea name="header_code" rows="6" style="width: 100%;"><?php echo esc_textarea($header_code); ?></textarea>

            <h2>Body Code</h2>
            <textarea name="body_code" rows="6" style="width: 100%;"><?php echo esc_textarea($body_code); ?></textarea>

            <h2>Footer Code</h2>
            <textarea name="footer_code" rows="6" style="width: 100%;"><?php echo esc_textarea($footer_code); ?></textarea>

            <p>
                <input type="submit" name="submit" value="Save Settings" class="button-primary">
            </p>
        </form>
    </div>
    <?php
}

// Output header code
function son_scripts_output_header_code() {
    $header_code = get_option('son_scripts_header_code', '');
    echo wp_kses_post($header_code);
}
add_action('wp_head', 'son_scripts_output_header_code');

// Output body code
function son_scripts_output_body_code() {
    $body_code = get_option('son_scripts_body_code', '');
    echo wp_kses_post($body_code);
}
add_action('wp_footer', 'son_scripts_output_body_code');

// Output footer code
function son_scripts_output_footer_code() {
    $footer_code = get_option('son_scripts_footer_code', '');
    echo wp_kses_post($footer_code);
}
add_action('wp_footer', 'son_scripts_output_footer_code');

// Cleanup the plugin data on plugin deactivation (optional)
function son_si_deactivation() {
    // Optionally, additional cleanup tasks specific for the plugin here
    // This function will be executed when the plugin is deactivated, but not during deletion
    // For example, you might want to remove any custom database tables created by the plugin
}
register_deactivation_hook(__FILE__, 'son_si_deactivation');

// Cleanup the plugin files on plugin deletion
function son_si_delete_plugin() {
    // Check if the deletion is being triggered by WordPress, not directly
    if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete-selected') {
        // Delete the plugin files when the user clicks the "Delete" plugin button
        $plugin_path = plugin_dir_path(__FILE__);
        $plugin_files = array('son-scripts-installer.php'); // Add other plugin files if needed

        foreach ($plugin_files as $file) {
            $file_path = $plugin_path . $file;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
}
add_action('delete_plugin', 'son_si_delete_plugin');


?>