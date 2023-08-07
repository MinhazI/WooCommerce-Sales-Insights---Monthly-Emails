<?php // Add menu item for "Suppliers" section
function add_custom_menu_item()
{
    add_submenu_page(
        'sales-report-settings',   // Parent menu slug
        'Suppliers',               // Page title
        'Suppliers - Custom WooCommerce Sales Report',               // Menu title
        'manage_options',          // Capability required to access the page
        'sales-report-suppliers',  // Menu slug
        'render_suppliers'    // Callback function to render the page
    );
}
add_action('admin_menu', 'add_custom_menu_item');


function render_suppliers()
{
    // Check if the supplier form is submitted
    if (isset($_POST['submit_supplier'])) {
        // Get the submitted supplier name and email
        $supplier_name = sanitize_text_field($_POST['supplier_name']);
        $supplier_email = sanitize_email($_POST['supplier_email']);

        // Validate the data
        if (!empty($supplier_name) && !empty($supplier_email)) {
            // Create a new supplier array
            $supplier_data = array(
                'supplier_name' => $supplier_name,
                'supplier_email' => $supplier_email,
            );

            // Store the supplier data in the database
            add_option('custom_supplier_data', $supplier_data);

            // Show success message or perform any other action
            echo '<div class="notice notice-success"><p>Supplier added successfully!</p></div>';
        } else {
            // Show error message or perform any other action
            echo '<div class="notice notice-error"><p>Please enter both supplier name and email.</p></div>';
        }
    }

    // Display the supplier form
?>
    <div class="wrap">
        <h1>Add Supplier</h1>
        <form method="post" action="">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" name="supplier_name" id="supplier_name" required>

            <label for="supplier_email">Supplier Email</label>
            <input type="email" name="supplier_email" id="supplier_email" required>

            <button type="submit" name="submit_supplier" class="button button-primary">Add Supplier</button>
        </form>
    </div>
<?php
}
