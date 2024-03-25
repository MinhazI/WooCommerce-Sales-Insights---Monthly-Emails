<?php
function get_completed_sales_data()
{
    try {
        // Calculate the start and end date of the previous month
        $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
        $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

        $completed_orders = wc_get_orders(array(
            'status' => 'wc-completed',
            'limit'  => -1,
            'date_query' => array(
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ),
        ));
        return $completed_orders;
    } catch (Exception $e) {
        woocommerce_sales_insights_log_errors($e);
    }
}


// Get processing order data from WooCommerce
function get_processing_sales_data()
{
    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    $processing_sales_query = wc_get_orders(array(
        'status' => 'wc-processing',
        'limit'  => -1,
        'date_query' => array(
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ),
    ));
    return $processing_sales_query;
}

// Get on-hold order data from WooCommerce
function get_on_hold_sales_data()
{

    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    $on_hold_sales_query = wc_get_orders(array(
        'status' => 'wc-on-hold',
        'limit'  => -1,
        'date_query' => array(
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ),
    ));
    return $on_hold_sales_query;
}

// Get cancelled order data from WooCommerce
function get_cancelled_sales_data()
{
    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    $on_hold_sales_query = wc_get_orders(array(
        'status' => 'wc-on-hold',
        'limit'  => -1,
        'date_query' => array(
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ),
    ));
    return $on_hold_sales_query;
}

// Get pending order data from WooCommerce
function get_pending_sales_data()
{

    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    $pending_sales_query = wc_get_orders(array(
        'status' => 'wc-pending',
        'limit'  => -1,
        'date_query' => array(
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ),
    ));
    return $pending_sales_query;
}

// Get refunded order data from WooCommerce
function get_refunded_sales_data()
{
    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    $refunded_sales_query = wc_get_orders(array(
        'status' => 'wc-refunded',
        'limit'  => -1,
        'date_query' => array(
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ),
    ));
    return $refunded_sales_query;
}

// Get pending order data from WooCommerce
function get_failed_sales_data()
{

    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    $failed_sales_query = wc_get_orders(array(
        'status' => 'wc-failed',
        'limit'  => -1,
        'date_query' => array(
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ),
    ));
    return $failed_sales_query;
}

function get_count_of_completed_orders()
{
    // Calculate the start and end of yesterday
    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    // Query completed orders within yesterday's date range
    $completed_orders = wc_get_orders(array(
        'status' => 'wc-completed',
        'date_query' => array(
            'after'     => date('Y-m-d H:i:s', $start_date),
            'before'    => date('Y-m-d H:i:s', $end_date),
            'inclusive' => true,
        ),
    ));

    // Get the count of completed orders
    $count = count($completed_orders);

    return $count;
}


function get_count_of_individual_order_status($status)
{

    // Calculate the start and end of yesterday
    $start_date = date('Y-m-d H:i:s', strtotime("first day of last month", current_time('timestamp')));
    $end_date = date('Y-m-t 23:59:59', strtotime("last day of last month", current_time('timestamp')));

    // Query completed orders within yesterday's date range
    $completed_orders = wc_get_orders(array(
        'status' => 'wc-' . $status,
        'date_query' => array(
            'after'     => date('Y-m-d H:i:s', $start_date),
            'before'    => date('Y-m-d H:i:s', $end_date),
            'inclusive' => true,
        ),
    ));

    // Get the count of completed orders
    $count = count($completed_orders);

    return $count;
}
