<?php
/*
Plugin Name: Custom WooCommerce Sales Report
Plugin URI: https://www.winauthority.com/
Description: A custom plugin for sending daily sales reports via email.
Author: Win Authority LLC
Author URI: https://www.winauthority.com/
Version: 1.0
*/

require_once(__DIR__ . '/supplier-settings/add-to-menu.php');
require_once(__DIR__ . '/queries/queries.php');
require_once(__DIR__ . '/admin-settings/settings-page.php');
require_once(__DIR__ . '/admin-settings/logs.php');
require_once(__DIR__ . '/admin-settings/email/send-email.php');

// date_default_timezone_set(get_option('timezone_string'));

// Register the activation hook
register_activation_hook(__FILE__, 'custom_sales_report_activate');

// Register the deactivation hook
register_deactivation_hook(__FILE__, 'custom_sales_report_deactivate');

// Get the site's timezone from WordPress settings
$site_timezone = get_option('timezone_string');

// Set the timezone
if ($site_timezone) {
    date_default_timezone_set($site_timezone);
}

// Activation hook callback
function custom_sales_report_activate()
{
    // Create the log file
    $log_file = plugin_dir_path(__FILE__) . '/admin-settings/logs/sales-report-log.txt';
    if (!file_exists($log_file)) {
        file_put_contents($log_file, '');
    }

    // Write a test entry to the log file
    $test_message = 'Log file created and plugin activated.';
    custom_sales_report_log_event($test_message);

    // Schedule the daily sales report email
    if (!wp_next_scheduled('send_sales_email')) {
        $send_time = get_option('custom_sales_report_send_time', '12:00 am');
        $next_send_time = strtotime('today ' . $send_time);
        if ($next_send_time < time()) {
            $next_send_time = strtotime('+1 day', $next_send_time);
        }


        wp_schedule_event($next_send_time, 'daily', 'send_sales_email');
    }

    add_custom_menu_item();
}

// Deactivation hook callback
function custom_sales_report_deactivate()
{
    // Remove the scheduled daily sales report email
    wp_clear_scheduled_hook('send_sales_email');
}

// Schedule the daily sales report email
function custom_sales_report_schedule_email()
{
    $send_time = get_option('custom_sales_report_send_time', '12:00 am');

    // Calculate the next send time based on the user-specified time
    $next_send_time = strtotime('today ' . $send_time);
    if ($next_send_time < time()) {
        $next_send_time = strtotime('+1 day', $next_send_time);
    }

    // Schedule the email
    wp_schedule_event($next_send_time, 'daily', 'send_sales_email');
}

// Add settings page to the admin menu
add_action('admin_menu', 'custom_sales_report_add_settings_page');

// Register settings
add_action('admin_init', 'custom_sales_report_register_settings');

// Add settings page
function custom_sales_report_add_settings_page()
{
    add_menu_page(
        'Custom WooCommerce Sales Report Settings',
        'Custom WooCommerce Sales Report',
        'manage_options',
        'custom-sales-report-settings',
        'custom_sales_report_settings'
    );
}

// Register settings
function custom_sales_report_register_settings()
{
    register_setting('custom_sales_report_settings_group', 'custom_sales_report_email_addresses');
    register_setting('custom_sales_report_settings_group', 'custom_sales_report_send_time', 'custom_sales_report_validate_send_time');
}

// Validate the send time setting
function custom_sales_report_validate_send_time($send_time)
{
    // Check if the send time has changed
    $current_send_time = get_option('custom_sales_report_send_time', '12:00 am');
    if ($send_time !== $current_send_time) {
        // Update the scheduled event with the new send time
        wp_clear_scheduled_hook('send_sales_email');
        custom_sales_report_schedule_email();
    }

    return $send_time;
}

// Action hook to send sales report email
add_action('send_sales_email', 'send_sales_email');

// Action hook to log error and send email notification
add_action('wp_mail_failed', 'custom_sales_report_mail_failed', 10, 1);

// Log and notify on mail failure
function custom_sales_report_mail_failed($wp_error)
{
    $error_message = $wp_error->get_error_message();
    custom_sales_report_log_errors($error_message);
}
