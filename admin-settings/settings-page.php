<?php

// Settings page callback
function woocommerce_sales_insights_settings()
{
    // Get the site's timezone from WordPress settings
    $site_timezone = get_option('timezone_string');

    // Set the timezone
    if ($site_timezone) {
        date_default_timezone_set($site_timezone);
    }

    // Save the settings if form is submitted
    if (isset($_POST['woocommerce_sales_insights_submit'])) {
        // Retrieve and sanitize the email addresses
        $email_addresses = isset($_POST['email_addresses']) ? sanitize_text_field($_POST['email_addresses']) : '';
        $email_addresses = preg_replace('/\s+/', '', $email_addresses); // Remove whitespace

        // Split the email addresses by comma
        $email_addresses = array_map('trim', explode(',', $email_addresses));

        // Filter out empty values
        $email_addresses = array_filter($email_addresses);

        // Convert the array of email addresses into a comma-separated string
        $email_string = implode(',', $email_addresses);

        $send_time = isset($_POST['send_time']) ? sanitize_text_field($_POST['send_time']) : '';

        // Update the option with the array of email addresses
        update_option('woocommerce_sales_insights_email_addresses', $email_string);
        update_option('woocommerce_sales_insights_send_time', $send_time);

        // Calculate the next send time
        $next_send_time = strtotime('first day of +1 month', strtotime('today ' . $send_time . ' ' . $site_timezone));
        $current_time = strtotime(current_time('Y-m-d H:i:s'));

        // Check if the next send time is in the past
        if ($next_send_time < $current_time) {
            // Add one day to the current date
            $next_send_time = strtotime('first day of +1 month', $current_time);

            // Set the time for the next send time
            $next_send_time = strtotime($send_time, $next_send_time);

            // Validate if the time was successfully set
            if ($next_send_time === false) {
                // Time format is invalid, handle the error
                $error_message = 'Invalid time format. Please enter a valid time.';
                woocommerce_sales_insights_log_event($error_message);
                echo '<div class="error notice"><p>' . $error_message . '</p></div>';
                return;
            }
        }

        // Check if the next send time is valid
        if ($next_send_time === false) {
            // Date or timezone is invalid, handle the error
            $error_message = 'Invalid date or timezone. Please check your settings.';
            woocommerce_sales_insights_log_event($error_message);
            echo '<div class="error notice"><p>' . $error_message . '</p></div>';
            return;
        }

        // Unschedule the existing event
        wp_clear_scheduled_hook('send_sales_email');

        // Schedule the event with the new send time
        wp_schedule_event($next_send_time, 'monthly', 'send_sales_email');

        // Format the next scheduled time
        $next_scheduled_time = wp_date(get_option('date_format') . ' ' . get_option('time_format'), $next_send_time);

        // Display success message
        $success_message = sprintf('Settings saved. Next scheduled email: %s', $next_scheduled_time);
        woocommerce_sales_insights_log_event($success_message);
        echo '<div class="updated notice"><p>' . $success_message . '</p></div>';
    }

    // Retrieve saved settings
    $email_addresses = get_option('woocommerce_sales_insights_email_addresses', '');
    $send_time = get_option('woocommerce_sales_insights_send_time', '12:00 am');
    $next_send_time = wp_next_scheduled('send_sales_email');
    $next_scheduled_time = $next_send_time ? wp_date(get_option('date_format') . ' ' . get_option('time_format'), $next_send_time) : '';

    // Display the settings page
?>
    <div class="wrap">
        <h1>WooCommerce Sales Insights</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">Email addresses you want reply to be sent to</th>
                    <td>
                        <input type="text" name="email_addresses" value="<?php echo esc_attr($email_addresses); ?>" class="regular-text">
                        <p class="description">Enter comma-separated email addresses.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Receive the report at (monthly)</th>
                    <td>
                        <input type="time" name="send_time" value="<?php echo esc_attr($send_time); ?>" class="regular-text">
                        <p class="description">Enter the preferred time to receive the sales report.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">The next email will be sent at</th>
                    <td>
                        <p><?php echo esc_html($next_scheduled_time); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Your website's current date and time is</th>
                    <td>
                        <p><?php echo wp_date('F j, Y g:i A'); ?></p>
                    </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="woocommerce_sales_insights_submit" class="button-primary" value="Save Settings"></p>
        </form>
    </div>
<?php
}
