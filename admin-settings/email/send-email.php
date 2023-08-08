
<?php
// Send sales report email
function send_sales_email()
{
    try {

        $site_timezone = get_option('timezone_string');
        // Set the timezone
        if ($site_timezone) {
            date_default_timezone_set($site_timezone);
        }

        $email_addresses = get_option('woocommerce_sales_insights_email_addresses', '');
        $email_addresses = explode(',', $email_addresses); // Split the email addresses by comma
        $email_addresses = array_map('trim', $email_addresses); // Trim whitespace from each email address
        $send_time = get_option('woocommerce_sales_insights_send_time', '8:00 am');
        $woocommerce_currency_symbol = get_woocommerce_currency_symbol();

        $next_month_timestamp = strtotime('first day of next month');

        // Calculate the next send time
        // $next_send_time = strtotime('today ' . $send_time . ' ' . $site_timezone);
        // $current_time = strtotime(current_time('Y-m-d H:i:s'));

        // Check if the next send time is in the past
        // if ($next_send_time < $current_time) {
        //     // Add one day to the current date
        //     $next_send_time = strtotime('+1 day', $current_time);

        //     // Set the time for the next send time
        //     $next_send_time = strtotime($send_time, $next_send_time);
        // }

        // Calculate the time difference between the next scheduled send time and the current time
        // $time_difference = $next_send_time - $current_time;

        // Remove this line, as we will calculate the next send time based on the user's timezone
        // $next_send_time = strtotime('today ' . $send_time . ' ' . $site_timezone);

        // Calculate the current time in the user's timezone
        $current_time = new DateTime('now', new DateTimeZone($site_timezone));

        // Parse the time from the settings (e.g., '8:00 am') to a DateTime object
        $send_time_parts = date_parse($send_time);
        $send_time_datetime = new DateTime();
        $send_time_datetime->setTime($send_time_parts['hour'], $send_time_parts['minute'], 0);

        // Calculate the next send time
        // If the current time is after or equal to the set send time, schedule it for the next month
        $next_month = new DateTime('first day of next month', new DateTimeZone($site_timezone));
        // Calculate the next send time for the start of the next month based on the user-specified time and site timezone
        $next_send_time = strtotime('first day of +1 month', strtotime('today ' . $send_time . ' ' . $site_timezone));

        // Schedule the next email based on the calculated time difference
        wp_schedule_event($next_send_time, 'monthly', 'send_sales_email');


        // Schedule the next email based on the calculated time difference
        // wp_schedule_event($next_send_time, 'daily', 'send_sales_email');

        // Calculate the previous day's date for the sales report
        $sales_report_date = wp_date('F j, Y', strtotime('first day of last month')) . ' - ' . wp_date('F j, Y', strtotime('last day of last month'));

        $total_sales = 0;
        $total_order_amount = 0;
        $count = 0;
        $total_commission = 0;
        $total_price_after_commission = 0;

        $sales_report = '<!doctype html>
        <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
                <!-- NAME: 1 COLUMN -->
                <!--[if gte mso 15]>
                <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG/>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
                <![endif]-->
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Monthly Sales Report for ' . $sales_report_date . '</title>
                
            <style type="text/css">
                p{
                    margin:10px 0;
                    padding:0;
                }
                table{
                    border-collapse:collapse;
                }
                h1,h2,h3,h4,h5,h6{
                    display:block;
                    margin:0;
                    padding:0;
                }
                img,a img{
                    border:0;
                    height:auto;
                    outline:none;
                    text-decoration:none;
                }
                body,#bodyTable,#bodyCell{
                    height:100%;
                    margin:0;
                    padding:0;
                    width:100%;
                    justify-content: center;
                    align-items: center;
                }
                .mcnPreviewText{
                    display:none !important;
                }
                #outlook a{
                    padding:0;
                }
                img{
                    -ms-interpolation-mode:bicubic;
                }
                table{
                    mso-table-lspace:0pt;
                    mso-table-rspace:0pt;
                }
                .ReadMsgBody{
                    width:100%;
                }
                .ExternalClass{
                    width:100%;
                }
                p,a,li,td,blockquote{
                    mso-line-height-rule:exactly;
                }
                a[href^=tel],a[href^=sms]{
                    color:inherit;
                    cursor:default;
                    text-decoration:none;
                }
                p,a,li,td,body,table,blockquote{
                    -ms-text-size-adjust:100%;
                    -webkit-text-size-adjust:100%;
                }
                .ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
                    line-height:100%;
                }
                a[x-apple-data-detectors]{
                    color:inherit !important;
                    text-decoration:none !important;
                    font-size:inherit !important;
                    font-family:inherit !important;
                    font-weight:inherit !important;
                    line-height:inherit !important;
                }
                #bodyCell{
                    padding:10px;
                }
                .templateContainer{
                    max-width:100% !important;
                }
                a.mcnButton{
                    display:block;
                }
                .mcnImage,.mcnRetinaImage{
                    vertical-align:bottom;
                }
                .mcnTextContent{
                    word-break:break-word;
                }
                .mcnTextContent img{
                    height:auto !important;
                }
                .mcnDividerBlock{
                    table-layout:fixed !important;
                }
                body,#bodyTable{
                    background-color:#FAFAFA;
                }
                #bodyCell{
                    border-top:0;
                }
                .templateContainer{
                    border:0;
                }
                h1{
                    color:#202020;
                    font-family:Helvetica;
                    font-size:26px;
                    font-style:normal;
                    font-weight:bold;
                    line-height:125%;
                    letter-spacing:normal;
                    text-align:left;
                }
                h2{
                    color:#202020;
                    font-family:Helvetica;
                    font-size:22px;
                    font-style:normal;
                    font-weight:bold;
                    line-height:125%;
                    letter-spacing:normal;
                    text-align:left;
                }
                h3{
                    color:#202020;
                    font-family:Helvetica;
                    font-size:20px;
                    font-style:normal;
                    font-weight:bold;
                    line-height:125%;
                    letter-spacing:normal;
                    text-align:left;
                }
                h4{
                    color:#202020;
                    font-family:Helvetica;
                    font-size:18px;
                    font-style:normal;
                    font-weight:bold;
                    line-height:125%;
                    letter-spacing:normal;
                    text-align:left;
                }
                #templatePreheader{
                    background-color:#FAFAFA;
                    background-image:none;
                    background-repeat:no-repeat;
                    background-position:center;
                    background-size:cover;
                    border-top:0;
                    border-bottom:0;
                    padding-top:9px;
                    padding-bottom:9px;
                }
                #templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
                    color:#656565;
                    font-family:Helvetica;
                    font-size:12px;
                    line-height:150%;
                    text-align:left;
                }
                #templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
                    color:#656565;
                    font-weight:normal;
                    text-decoration:underline;
                }
                #templateHeader{
                    background-color:#FFFFFF;
                    background-image:none;
                    background-repeat:no-repeat;
                    background-position:center;
                    background-size:cover;
                    border-top:0;
                    border-bottom:0;
                    padding-top:9px;
                    padding-bottom:0;
                }
                #templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
                    color:#202020;
                    font-family:Helvetica;
                    font-size:16px;
                    line-height:150%;
                    text-align:left;
                }
                #templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
                    color:#007C89;
                    font-weight:normal;
                    text-decoration:underline;
                }
                #templateBody{
                    background-color:#FFFFFF;
                    background-image:none;
                    background-repeat:no-repeat;
                    background-position:center;
                    background-size:cover;
                    border-top:0;
                    border-bottom:2px solid #EAEAEA;
                    padding-top:0;
                    padding-bottom:9px;
                }
                #templateBody .mcnTextContent,#templateBody .mcnTextContent p{
                    color:#202020;
                    font-family:Helvetica;
                    font-size:16px;
                    line-height:150%;
                    text-align:left;
                }
                #templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
                    color:#007C89;
                    font-weight:normal;
                    text-decoration:underline;
                }
                #templateFooter{
                    background-color:#FAFAFA;
                    background-image:none;
                    background-repeat:no-repeat;
                    background-position:center;
                    background-size:cover;
                    border-top:0;
                    border-bottom:0;
                    padding-top:9px;
                    padding-bottom:9px;
                }
                #templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
                    color:#656565;
                    font-family:Helvetica;
                    font-size:12px;
                    line-height:150%;
                    text-align:center;
                }
                #templateFooter .mcnTextContent a,#templateFooter .mcnTextContent p a{
                    color:#656565;
                    font-weight:normal;
                    text-decoration:underline;
                }

                table {
                    border-collapse: collapse;
                    border-spacing: 0;
                    width: 100%;
                  }
                  
                  th, td {
                    text-align: center;
                    padding: 8px;
                  }

                  .even-row{
                    background-color: #f2f2f2;
                  }

            @media only screen and (min-width:768px){
                .templateContainer{
                    width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                body,table,td,p,a,li,blockquote{
                    -webkit-text-size-adjust:none !important;
                }
        
        }	@media only screen and (max-width: 480px){
                body{
                    width:100% !important;
                    min-width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnRetinaImage{
                    max-width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImage{
                    width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer,.mcnImageCardLeftImageContentContainer,.mcnImageCardRightImageContentContainer{
                    max-width:100% !important;
                    width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnBoxedTextContentContainer{
                    min-width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImageGroupContent{
                    padding:9px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
                    padding-top:9px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImageCardTopImageContent,.mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
                    padding-top:18px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImageCardBottomImageContent{
                    padding-bottom:9px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImageGroupBlockInner{
                    padding-top:0 !important;
                    padding-bottom:0 !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImageGroupBlockOuter{
                    padding-top:9px !important;
                    padding-bottom:9px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnTextContent,.mcnBoxedTextContentColumn{
                    padding-right:18px !important;
                    padding-left:18px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
                    padding-right:18px !important;
                    padding-bottom:0 !important;
                    padding-left:18px !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcpreview-image-uploader{
                    display:none !important;
                    width:100% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                h1{
                    font-size:22px !important;
                    line-height:125% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                h2{
                    font-size:20px !important;
                    line-height:125% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                h3{
                    font-size:18px !important;
                    line-height:125% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                h4{
                    font-size:16px !important;
                    line-height:150% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                .mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
                    font-size:14px !important;
                    line-height:150% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                #templatePreheader{
                    display:block !important;
                }
        
        }	@media only screen and (max-width: 480px){
                #templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
                    font-size:14px !important;
                    line-height:150% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                #templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
                    font-size:16px !important;
                    line-height:150% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                #templateBody .mcnTextContent,#templateBody .mcnTextContent p{
                    font-size:16px !important;
                    line-height:150% !important;
                }
        
        }	@media only screen and (max-width: 480px){
                #templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
                    font-size:14px !important;
                    line-height:150% !important;
                }
        
        }</style></head>
    <body>
        <!--*|IF:MC_PREVIEW_TEXT|*-->
        <!--[if !gte mso 9]><!----><span class="mcnPreviewText"
            style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">Monthly Sales Report for ' . $sales_report_date . '</span><!--<![endif]-->
        <!--*|END:IF|*-->
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <!--[if (gte mso 9)|(IE)]>
                            <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                            <tr>
                            <td align="center" valign="top" width="100%" style="width:100%;">
                            <![endif]-->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
                            <tr>
                                <td valign="top" id="templatePreheader"></td>
                            </tr>
                            <tr>
                                <td valign="top" id="templateHeader"></td>
                            </tr>
                            <tr>
                                <td valign="top" id="templateBody">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock"
                                        style="min-width:100%;">
                                        <tbody class="mcnTextBlockOuter">
                                            <tr>
                                                <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                                    <!--[if mso]>
                    <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                    <tr>
                    <![endif]-->
    
                                                    <!--[if mso]>
                    <td valign="top" width="100%" style="width:100%;">
                    <![endif]-->
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                        style="max-width:100%; min-width:100%;" width="100%"
                                                        class="mcnTextContentContainer">
                                                        <tbody>
                                                            <tr>
    
                                                                <td valign="top" class="mcnTextContent"
                                                                    style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
    
                                                                    <p style="text-align: justify;">This email was sent from
                                                                        your website ' . get_site_url() . ' and is a summary of
                                                                        the monthly sales on your website for between ' . $sales_report_date . '.</p>
    
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <!--[if mso]>
                    </td>
                    <![endif]-->
    
                                                    <!--[if mso]>
                    </tr>
                    </table>
                    <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock"
                                        style="min-width:100%;">
                                        <tbody class="mcnTextBlockOuter">
                                            <tr>
                                                <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                                    <!--[if mso]>
                    <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                    <tr>
                    <![endif]-->
    
                                                    <!--[if mso]>
                    </tr>
                    </table>
                    <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
            <div style="overflow-x:auto;">
  <table style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;border-spacing: 0;width: 100%;">
                        <thead>
                            <tr>
                                <td colspan="8"><p style="font-size: large;">Sales between ' . $sales_report_date . '</p></td>
                            </tr>
                            <tr style="border: 1px solid #f2f2f2;">
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Product Name</th>
                                <th>Product Category</th>
                                <th>Supplier Name</th>
                                <th>Order Amount</th>
                                <th>Commission</th>
                                <th>Total Amount After Commission</th>
                            </tr>
                            </thead>
                            <tbody>
                            ';
        $completed_sales_query = get_completed_sales_data();
        if ($completed_sales_query->have_posts()) {
            while ($completed_sales_query->have_posts()) {
                $completed_sales_query->the_post();
                $order = wc_get_order(get_the_ID());

                foreach ($order->get_items() as $item_id => $item) {
                    $row_class = ($count % 2 == 0) ? 'even-row' : 'odd-row';
                    $product = $item->get_product();
                    $product_id = $item->get_product_id();
                    $product_name = $product->get_name();
                    $product_url = $product->get_permalink();
                    $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
                    $product_commission = 0;
                    $total_price = 0;

                    // Check if 'FROK' is in the product categories
                    if (in_array('FROK', $product_categories)) {
                        // $product_commission = 0;
                        try {
                            $total_price = $item->get_total();
                            $product_commission = $total_price * (60 / 100);
                        } catch (Exception $e) {
                            woocommerce_sales_insights_log_errors('Exception: ' . $e);
                        }
                    }

                    $supplier_name = '';
                    $supplier_email = '';
                    // Retrieve the Supplier Name from the product
                    if ($supplier = get_field('supplier', $product_id, true)) {
                        $supplier_name = $supplier->name;
                    } else {
                        $supplier_name = 'No supplier found'; // No Supplier Name available
                    }

                    $product_commission_final = null;

                    try {
                        if ($product_commission !== 0) {
                            $product_commission_final = get_option('woocommerce_currency') . '' . wc_price($product_commission);

                            $total_commission += $product_commission;
                            $total_price_after_commission += $item->get_total() - $product_commission;
                        } else {
                            $product_commission_final = 'This is not a FROK product';
                        }
                    } catch (Exception $e) {
                        woocommerce_sales_insights_log_errors('Exception: ' . $e);
                    }

                    // $supplier = "Not available";

                    $sales_report .= '<tr class="' . $row_class . '">';
                    $sales_report .= '<td>' . $count += 1 . '</td>';
                    $sales_report .= '<td><a href="' . esc_url($order->get_edit_order_url()) . '">' . get_the_ID() . '</a></td>';
                    $sales_report .= '<td><a href="' . esc_url($product_url) . '">' . $product_name . '</a></td>';
                    $sales_report .= '<td>' . implode(', ', $product_categories) . '</td>';
                    $sales_report .= '<td>' . $supplier_name . '</br>' . $supplier_email . '</td>';
                    $sales_report .= '<td>' . get_option('woocommerce_currency') . '' . wc_price($item->get_total()) . '</td>';
                    $sales_report .= '<td>' . $product_commission_final . '</td>';
                    $sales_report .= '<td>' . get_option('woocommerce_currency') . '' . wc_price($item->get_total() - $product_commission) . '</td>';
                    $sales_report .= '</tr>';

                    $total_sales += $item->get_quantity();
                    $total_order_amount += $item->get_total();
                }
            }
            $sales_report .= '<tr><td colspan="8"></td></tr>
                            <tr>
                                <td colspan="5">Totals</td>
                                <td>' . get_option('woocommerce_currency') . $woocommerce_currency_symbol . '' . $total_order_amount . '</td>
                                <td>' . get_option('woocommerce_currency') . $woocommerce_currency_symbol . '' . $total_commission . '</td>
                                <td>' . get_option('woocommerce_currency') . $woocommerce_currency_symbol . '' . $total_price_after_commission . '</td>
                            </tr>';
            wp_reset_postdata();
        } else {
            $sales_report .= '<tr><td colspan="7">No completed orders for the month.</td></tr>';
        }
        $sales_report .= '
    
                                                                
                                                            </tbody>
                                                        </table>
                                                        </div>
                                                    
    
                                                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock"
                                        style="min-width:100%;">
                                        <tbody class="mcnTextBlockOuter">
                                            <tr>
                                                <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                                    <!--[if mso]>
                    <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                    <tr>
                    <![endif]-->
    
                                                    <!--[if mso]>
                    <td valign="top" width="100%" style="width:100%;">
                    <![endif]-->
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                        style="max-width:100%; min-width:100%;" width="100%"
                                                        class="mcnTextContentContainer">
                                                        <tbody>
                                                            <tr>
    
                                                                <td valign="top" class="mcnTextContent"
                                                                    style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
    
                                                                    <p style="text-align: center;"><small>This report was
                                                                            generated automatically by WooCommerce Sales Insights plugin developed by Win Authority LLC on '. wp_date('F j, Y', strtotime('today')) .'.</small></p>
    
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <!--[if mso]>
                    </td>
                    <![endif]-->
    
                                                    <!--[if mso]>
                    </tr>
                    </table>
                    <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" id="templateFooter"></td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
    
    </html>';

        // Send the email
        $subject = 'Monthly Sales Report - ' . $sales_report_date;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message = $sales_report;
        wp_mail($email_addresses, $subject, $message, $headers);

        // Filter out empty values
        $email_addresses = array_filter($email_addresses);

        // Convert the array of email addresses into a comma-separated string
        $email_string = implode(',', $email_addresses);

        // Log the event
        $log_message = 'Sales report email sent to: ' . $email_string;
        woocommerce_sales_insights_log_event($log_message);
    } catch (Exception $e) {
        woocommerce_sales_insights_log_event($e);
    }
}
