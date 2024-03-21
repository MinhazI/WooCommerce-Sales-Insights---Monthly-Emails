<?php
// Log an event
function woocommerce_sales_insights_log_event($message)
{
    $log_file = plugin_dir_path(__FILE__) . 'sales-report-log.txt';
    $log_entry = '[' . wp_date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Log errors
function woocommerce_sales_insights_log_errors($message)
{
    $log_file = plugin_dir_path(__FILE__) . 'sales-report-log.txt';
    $log_entry = '[Error] [' . wp_date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

    $admin_email = 'minhaz@winauthority.com'; // Change this to the desired email address
    $subject = 'Sales Report Error';
    $message = 'An error occurred while sending the sales report email on the website ' . site_url() . '. Please check the log file for more details.';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail($admin_email, $subject, $message, $headers);
}
