<?php
/*
Plugin Name: WooCommerce Sales Insights
Plugin URI: https://www.winauthority.com/
Description: A plugin for sending WooCommerce monthly sales reports via email.
Author: Win Authority LLC
Author URI: https://www.winauthority.com/
Version: 1.5.0
*/

require_once(__DIR__ . '/supplier-settings/add-to-menu.php');
require_once(__DIR__ . '/queries/queries.php');
require_once(__DIR__ . '/admin-settings/settings-page.php');
require_once(__DIR__ . '/admin-settings/logs.php');
require_once(__DIR__ . '/admin-settings/email/send-email.php');

// Register the activation hook
register_activation_hook(__FILE__, 'woocommerce_sales_insights_activate');

// Register the deactivation hook
register_deactivation_hook(__FILE__, 'woocommerce_sales_insights_deactivate');

// Get the site's timezone from WordPress settings
$site_timezone = get_option('timezone_string');

// Set the timezone
if ($site_timezone) {
    date_default_timezone_set($site_timezone);
}

// Activation hook callback
function woocommerce_sales_insights_activate()
{
    // Create the log file
    $log_file = plugin_dir_path(__FILE__) . '/admin-settings/logs/sales-report-log.txt';
    if (!file_exists($log_file)) {
        file_put_contents($log_file, '');
    }

    // Write a test entry to the log file
    $test_message = 'Log file created and plugin activated.';
    woocommerce_sales_insights_log_event($test_message);

    // Schedule the sales report email
    if (!wp_next_scheduled('send_sales_email')) {
        $send_time = get_option('woocommerce_sales_insights_send_time', '12:00 am');
        $current_time = current_time('timestamp');

        // Calculate the next send time for the start of the following month based on the user-specified time
        $next_month = strtotime('first day of +1 month', $current_time);
        $next_send_time = strtotime('today ' . $send_time, $next_month);

        // Schedule the email
        wp_schedule_event($next_send_time, 'monthly', 'send_sales_email');
    }
}

// Deactivation hook callback
function woocommerce_sales_insights_deactivate()
{
    // Remove the scheduled daily sales report email
    wp_clear_scheduled_hook('send_sales_email');
}

// Schedule the sales report email
function woocommerce_sales_insights_schedule_email()
{
    $send_time = get_option('woocommerce_sales_insights_send_time', '12:00 am');

    // Calculate the next send time for the start of the following month based on the user-specified time
    $current_time = current_time('timestamp');
    $next_month = strtotime('first day of +1 month', $current_time);
    $next_send_time = strtotime('today ' . $send_time, $next_month);

    // Schedule the email
    wp_schedule_event($next_send_time, 'monthly', 'send_sales_email');
}
add_action('admin_menu', 'woocommerce_sales_insights_add_settings_page');


// Register settings
add_action('admin_init', 'woocommerce_sales_insights_register_settings');

// Add settings page
function woocommerce_sales_insights_add_settings_page()
{
    add_menu_page(
        'WooCommerce Sales Insights Settings',
        'WooCommerce Sales Insights',
        'manage_options',
        'custom-sales-report-settings',
        'woocommerce_sales_insights_settings',
        'dashicons-chart-bar',
        99
    );
}

// Register settings
function woocommerce_sales_insights_register_settings()
{
    register_setting('woocommerce_sales_insights_settings_group', 'woocommerce_sales_insights_email_addresses');
    register_setting('woocommerce_sales_insights_settings_group', 'woocommerce_sales_insights_send_time', 'woocommerce_sales_insights_validate_send_time');
}

// Validate the send time setting
function woocommerce_sales_insights_validate_send_time($send_time)
{
    // Check if the send time has changed
    $current_send_time = get_option('woocommerce_sales_insights_send_time', '12:00 am');
    if ($send_time !== $current_send_time) {
        // Update the scheduled event with the new send time
        wp_clear_scheduled_hook('send_sales_email');
        woocommerce_sales_insights_schedule_email();
    }

    return $send_time;
}

// Define a custom action to log error messages
function log_plugin_error($error_message)
{
    // You can customize this part to log the error message to a file or database, or take any other action you need.
    error_log('Plugin Error: ' . $error_message);
}

// Somewhere in your plugin code where you encounter an error, call the custom action with the error message
function handle_plugin_error()
{
    $error_message = 'An error occurred in the plugin WooCommerce Sales Insights.';
    do_action('custom_plugin_error', $error_message);
}

// Hook the custom action to log the error message
add_action('custom_plugin_error', 'log_plugin_error');

// Action hook to send sales report email
add_action('send_sales_email', 'send_sales_email');

// Action hook to log error and send email notification
add_action('wp_mail_failed', 'woocommerce_sales_insights_mail_failed', 10, 1);

// Log and notify on mail failure
function woocommerce_sales_insights_mail_failed($wp_error)
{
    if (strpos($wp_error->get_error_message(), 'An error occurred in the plugin WooCommerce Sales Insights.') !== false) {
        $error_message = $wp_error->get_error_message();
        woocommerce_sales_insights_log_errors($error_message);
    }
}
