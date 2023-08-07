<?php
function get_completed_sales_data()
{
    try {
        // Calculate the start and end date of the previous month
        $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
        $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

        $args = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => 'wc-completed',
            'date_query'     => array(
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ),
        );

        return $completed_sales_query = new WP_Query($args);
    } catch (Exception $e) {
        woocommerce_sales_insights_log_errors($e);
    }
}


// Get processing order data from WooCommerce
function get_processing_sales_data()
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-processing',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    return $processing_sales_query = new WP_Query($args);
}

// Get on-hold order data from WooCommerce
function get_on_hold_sales_data()
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-on-hold',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    return $on_hold_sales_query = new WP_Query($args);
}

// Get cancelled order data from WooCommerce
function get_cancelled_sales_data()
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-cancelled',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    return $cancelled_sales_query = new WP_Query($args);
}

// Get pending order data from WooCommerce
function get_pending_sales_data()
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-pending',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    return $pending_sales_query = new WP_Query($args);
}

// Get refunded order data from WooCommerce
function get_refunded_sales_data()
{
    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday

    $args = array(
        'post_type'      => 'shop_order',
        'posts_per_page' => -1,
        'post_status'    => 'wc-refunded',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    return $refunded_sales_query = new WP_Query($args);
}

// Get pending order data from WooCommerce
function get_failed_sales_data()
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-failed',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    return $failed_sales_query = new WP_Query($args);
}

function get_count_of_completed_orders()
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $count = 0;
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-completed',
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    $orders = new WP_Query($args);
    $count += $orders->found_posts;

    return $count;
}

function get_count_of_individual_order_status($status)
{

    $start_of_day = strtotime('midnight', strtotime('yesterday')); // Timestamp of the start of yesterday
    $end_of_day = strtotime('tomorrow', $start_of_day) - 1; // Timestamp of the end of yesterday
    $count = 0;
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => 'wc-' . $status,
        'date_query'     => array(
            'after'     => date('Y-m-d H:i:s', $start_of_day),
            'before'    => date('Y-m-d H:i:s', $end_of_day),
            'inclusive' => true,
        ),
    );

    $orders = new WP_Query($args);
    $count += $orders->found_posts;

    return $count;
}
