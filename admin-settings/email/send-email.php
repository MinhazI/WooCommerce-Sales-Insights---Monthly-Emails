
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

        woocommerce_sales_insights_log_event('Preparing to send sales report email...');
        $sales_report = '<!doctype html>
    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
        xmlns:o="urn:schemas-microsoft-com:office:office">
    
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
            p {
                margin: 10px 0;
                padding: 0;
            }
    
            table {
                border-collapse: collapse;
            }
    
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                display: block;
                margin: 0;
                padding: 0;
            }
    
            img,
            a img {
                border: 0;
                height: auto;
                outline: none;
                text-decoration: none;
            }
    
            body,
            #bodyTable,
            #bodyCell {
                height: 100%;
                margin: 0;
                padding: 0;
                width: 100%;
            }
    
            .mcnPreviewText {
                display: none !important;
            }
    
            #outlook a {
                padding: 0;
            }
    
            img {
                -ms-interpolation-mode: bicubic;
            }
    
            table {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }
    
            .ReadMsgBody {
                width: 100%;
            }
    
            .ExternalClass {
                width: 100%;
            }
    
            p,
            a,
            li,
            td,
            blockquote {
                mso-line-height-rule: exactly;
            }
    
            a[href^=tel],
            a[href^=sms] {
                color: inherit;
                cursor: default;
                text-decoration: none;
            }
    
            p,
            a,
            li,
            td,
            body,
            table,
            blockquote {
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }
    
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass td,
            .ExternalClass div,
            .ExternalClass span,
            .ExternalClass font {
                line-height: 100%;
            }
    
            a[x-apple-data-detectors] {
                color: inherit !important;
                text-decoration: none !important;
                font-size: inherit !important;
                font-family: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
            }
    
            #bodyCell {
                padding: 10px;
            }
    
            .templateContainer {
                max-width: 600px !important;
            }
    
            a.mcnButton {
                display: block;
            }
    
            .mcnImage,
            .mcnRetinaImage {
                vertical-align: bottom;
            }
    
            .mcnTextContent {
                word-break: break-word;
            }
    
            .mcnTextContent img {
                height: auto !important;
            }
    
            .mcnDividerBlock {
                table-layout: fixed !important;
            }
    
            .table table {
                border-collapse: collapse;
                border-spacing: 0;
                width: 100%;
                border: 0px solid #ddd;
            }
    
            .table th,
            .table td {
                text-align: center;
                padding: 8px;
            }
    
            .table tr:nth-child(even) {
                background-color: #f2f2f2
            }

            .even-row{
                background-color: #f2f2f2 !important
            }
    
            /*
        @tab Page
        @section Background Style
        */
            body,
            #bodyTable {
                /*@editable*/
                background-color: #FAFAFA;
            }
    
            /*
        @tab Page
        @section Background Style
        */
            #bodyCell {
                /*@editable*/
                border-top: 0;
            }
    
            /*
        @tab Page
        @section Email Border
        @tip Set the border for your email.
        */
            .templateContainer {
                /*@editable*/
                border: 0;
            }
    
            /*
        @tab Page
        @section Heading 1
        @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
        @style heading 1
        */
            h1 {
                /*@editable*/
                color: #202020;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 26px;
                /*@editable*/
                font-style: normal;
                /*@editable*/
                font-weight: bold;
                /*@editable*/
                line-height: 125%;
                /*@editable*/
                letter-spacing: normal;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Page
        @section Heading 2
        @tip Set the styling for all second-level headings in your emails.
        @style heading 2
        */
            h2 {
                /*@editable*/
                color: #202020;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 22px;
                /*@editable*/
                font-style: normal;
                /*@editable*/
                font-weight: bold;
                /*@editable*/
                line-height: 125%;
                /*@editable*/
                letter-spacing: normal;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Page
        @section Heading 3
        @tip Set the styling for all third-level headings in your emails.
        @style heading 3
        */
            h3 {
                /*@editable*/
                color: #202020;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 20px;
                /*@editable*/
                font-style: normal;
                /*@editable*/
                font-weight: bold;
                /*@editable*/
                line-height: 125%;
                /*@editable*/
                letter-spacing: normal;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Page
        @section Heading 4
        @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
        @style heading 4
        */
            h4 {
                /*@editable*/
                color: #202020;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 18px;
                /*@editable*/
                font-style: normal;
                /*@editable*/
                font-weight: bold;
                /*@editable*/
                line-height: 125%;
                /*@editable*/
                letter-spacing: normal;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Preheader
        @section Preheader Style
        @tip Set the background color and borders for your email\'s preheader area.
        */
            #templatePreheader {
                /*@editable*/
                background-color: #FAFAFA;
                /*@editable*/
                background-image: none;
                /*@editable*/
                background-repeat: no-repeat;
                /*@editable*/
                background-position: center;
                /*@editable*/
                background-size: cover;
                /*@editable*/
                border-top: 0;
                /*@editable*/
                border-bottom: 0;
                /*@editable*/
                padding-top: 9px;
                /*@editable*/
                padding-bottom: 9px;
            }
    
            /*
        @tab Preheader
        @section Preheader Text
        @tip Set the styling for your email\'s preheader text. Choose a size and color that is easy to read.
        */
            #templatePreheader .mcnTextContent,
            #templatePreheader .mcnTextContent p {
                /*@editable*/
                color: #656565;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 12px;
                /*@editable*/
                line-height: 150%;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Preheader
        @section Preheader Link
        @tip Set the styling for your email\'s preheader links. Choose a color that helps them stand out from your text.
        */
            #templatePreheader .mcnTextContent a,
            #templatePreheader .mcnTextContent p a {
                /*@editable*/
                color: #656565;
                /*@editable*/
                font-weight: normal;
                /*@editable*/
                text-decoration: underline;
            }
    
            /*
        @tab Header
        @section Header Style
        @tip Set the background color and borders for your email\'s header area.
        */
            #templateHeader {
                /*@editable*/
                background-color: #FFFFFF;
                /*@editable*/
                background-image: none;
                /*@editable*/
                background-repeat: no-repeat;
                /*@editable*/
                background-position: center;
                /*@editable*/
                background-size: cover;
                /*@editable*/
                border-top: 0;
                /*@editable*/
                border-bottom: 0;
                /*@editable*/
                padding-top: 9px;
                /*@editable*/
                padding-bottom: 0;
            }
    
            /*
        @tab Header
        @section Header Text
        @tip Set the styling for your email\'s header text. Choose a size and color that is easy to read.
        */
            #templateHeader .mcnTextContent,
            #templateHeader .mcnTextContent p {
                /*@editable*/
                color: #202020;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 16px;
                /*@editable*/
                line-height: 150%;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Header
        @section Header Link
        @tip Set the styling for your email\'s header links. Choose a color that helps them stand out from your text.
        */
            #templateHeader .mcnTextContent a,
            #templateHeader .mcnTextContent p a {
                /*@editable*/
                color: #007C89;
                /*@editable*/
                font-weight: normal;
                /*@editable*/
                text-decoration: underline;
            }
    
            /*
        @tab Body
        @section Body Style
        @tip Set the background color and borders for your email\'s body area.
        */
            #templateBody {
                /*@editable*/
                background-color: #FFFFFF;
                /*@editable*/
                background-image: none;
                /*@editable*/
                background-repeat: no-repeat;
                /*@editable*/
                background-position: center;
                /*@editable*/
                background-size: cover;
                /*@editable*/
                border-top: 0;
                /*@editable*/
                border-bottom: 2px solid #EAEAEA;
                /*@editable*/
                padding-top: 0;
                /*@editable*/
                padding-bottom: 9px;
            }
    
            /*
        @tab Body
        @section Body Text
        @tip Set the styling for your email\'s body text. Choose a size and color that is easy to read.
        */
            #templateBody .mcnTextContent,
            #templateBody .mcnTextContent p {
                /*@editable*/
                color: #202020;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 16px;
                /*@editable*/
                line-height: 150%;
                /*@editable*/
                text-align: left;
            }
    
            /*
        @tab Body
        @section Body Link
        @tip Set the styling for your email\'s body links. Choose a color that helps them stand out from your text.
        */
            #templateBody .mcnTextContent a,
            #templateBody .mcnTextContent p a {
                /*@editable*/
                color: #007C89;
                /*@editable*/
                font-weight: normal;
                /*@editable*/
                text-decoration: underline;
            }
    
            /*
        @tab Footer
        @section Footer Style
        @tip Set the background color and borders for your email\'s footer area.
        */
            #templateFooter {
                /*@editable*/
                background-color: #FAFAFA;
                /*@editable*/
                background-image: none;
                /*@editable*/
                background-repeat: no-repeat;
                /*@editable*/
                background-position: center;
                /*@editable*/
                background-size: cover;
                /*@editable*/
                border-top: 0;
                /*@editable*/
                border-bottom: 0;
                /*@editable*/
                padding-top: 9px;
                /*@editable*/
                padding-bottom: 9px;
            }
    
            /*
        @tab Footer
        @section Footer Text
        @tip Set the styling for your email\'s footer text. Choose a size and color that is easy to read.
        */
            #templateFooter .mcnTextContent,
            #templateFooter .mcnTextContent p {
                /*@editable*/
                color: #656565;
                /*@editable*/
                font-family: Helvetica;
                /*@editable*/
                font-size: 12px;
                /*@editable*/
                line-height: 150%;
                /*@editable*/
                text-align: center;
            }
    
            /*
        @tab Footer
        @section Footer Link
        @tip Set the styling for your email\'s footer links. Choose a color that helps them stand out from your text.
        */
            #templateFooter .mcnTextContent a,
            #templateFooter .mcnTextContent p a {
                /*@editable*/
                color: #656565;
                /*@editable*/
                font-weight: normal;
                /*@editable*/
                text-decoration: underline;
            }
    
            @media only screen and (min-width:768px) {
                .templateContainer {
                    width: 600px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                body,
                table,
                td,
                p,
                a,
                li,
                blockquote {
                    -webkit-text-size-adjust: none !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                body {
                    width: 100% !important;
                    min-width: 100% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnRetinaImage {
                    max-width: 100% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnImage {
                    width: 100% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                .mcnCartContainer,
                .mcnCaptionTopContent,
                .mcnRecContentContainer,
                .mcnCaptionBottomContent,
                .mcnTextContentContainer,
                .mcnBoxedTextContentContainer,
                .mcnImageGroupContentContainer,
                .mcnCaptionLeftTextContentContainer,
                .mcnCaptionRightTextContentContainer,
                .mcnCaptionLeftImageContentContainer,
                .mcnCaptionRightImageContentContainer,
                .mcnImageCardLeftTextContentContainer,
                .mcnImageCardRightTextContentContainer,
                .mcnImageCardLeftImageContentContainer,
                .mcnImageCardRightImageContentContainer {
                    max-width: 100% !important;
                    width: 100% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnBoxedTextContentContainer {
                    min-width: 100% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnImageGroupContent {
                    padding: 9px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                .mcnCaptionLeftContentOuter .mcnTextContent,
                .mcnCaptionRightContentOuter .mcnTextContent {
                    padding-top: 9px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                .mcnImageCardTopImageContent,
                .mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent,
                .mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent {
                    padding-top: 18px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnImageCardBottomImageContent {
                    padding-bottom: 9px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnImageGroupBlockInner {
                    padding-top: 0 !important;
                    padding-bottom: 0 !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcnImageGroupBlockOuter {
                    padding-top: 9px !important;
                    padding-bottom: 9px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                .mcnTextContent,
                .mcnBoxedTextContentColumn {
                    padding-right: 18px !important;
                    padding-left: 18px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                .mcnImageCardLeftImageContent,
                .mcnImageCardRightImageContent {
                    padding-right: 18px !important;
                    padding-bottom: 0 !important;
                    padding-left: 18px !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
                .mcpreview-image-uploader {
                    display: none !important;
                    width: 100% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Heading 1
        @tip Make the first-level headings larger in size for better readability on small screens.
        */
                h1 {
                    /*@editable*/
                    font-size: 22px !important;
                    /*@editable*/
                    line-height: 125% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Heading 2
        @tip Make the second-level headings larger in size for better readability on small screens.
        */
                h2 {
                    /*@editable*/
                    font-size: 20px !important;
                    /*@editable*/
                    line-height: 125% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Heading 3
        @tip Make the third-level headings larger in size for better readability on small screens.
        */
                h3 {
                    /*@editable*/
                    font-size: 18px !important;
                    /*@editable*/
                    line-height: 125% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Heading 4
        @tip Make the fourth-level headings larger in size for better readability on small screens.
        */
                h4 {
                    /*@editable*/
                    font-size: 16px !important;
                    /*@editable*/
                    line-height: 150% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Boxed Text
        @tip Make the boxed text larger in size for better readability on small screens. We recommend a font size of at least 16px.
        */
                .mcnBoxedTextContentContainer .mcnTextContent,
                .mcnBoxedTextContentContainer .mcnTextContent p {
                    /*@editable*/
                    font-size: 14px !important;
                    /*@editable*/
                    line-height: 150% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Preheader Visibility
        @tip Set the visibility of the email\'s preheader on small screens. You can hide it to save space.
        */
                #templatePreheader {
                    /*@editable*/
                    display: block !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Preheader Text
        @tip Make the preheader text larger in size for better readability on small screens.
        */
                #templatePreheader .mcnTextContent,
                #templatePreheader .mcnTextContent p {
                    /*@editable*/
                    font-size: 14px !important;
                    /*@editable*/
                    line-height: 150% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Header Text
        @tip Make the header text larger in size for better readability on small screens.
        */
                #templateHeader .mcnTextContent,
                #templateHeader .mcnTextContent p {
                    /*@editable*/
                    font-size: 16px !important;
                    /*@editable*/
                    line-height: 150% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Body Text
        @tip Make the body text larger in size for better readability on small screens. We recommend a font size of at least 16px.
        */
                #templateBody .mcnTextContent,
                #templateBody .mcnTextContent p {
                    /*@editable*/
                    font-size: 16px !important;
                    /*@editable*/
                    line-height: 150% !important;
                }
    
            }
    
            @media only screen and (max-width: 480px) {
    
                /*
        @tab Mobile Styles
        @section Footer Text
        @tip Make the footer content text larger in size for better readability on small screens.
        */
                #templateFooter .mcnTextContent,
                #templateFooter .mcnTextContent p {
                    /*@editable*/
                    font-size: 14px !important;
                    /*@editable*/
                    line-height: 150% !important;
                }
    
            }
        </style>
    </head>
    
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
                            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                            <tr>
                            <td align="center" valign="top" width="600" style="width:600px;">
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
                    <td valign="top" width="600" style="width:600px;">
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
                <div style="overflow-x:auto;" class="table">
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="9"><p style="font-size: large;">Completed orders between ' . $sales_report_date . '</p></td>
                            </tr>
                            <tr style="border: 1px solid lightgray;">
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Product Name</th>
                                <th>Product Category</th>
                                <th>Order Quantity</th>
                                <th>Supplier\'s Name</th>
                                <th>Order Amount</th>
                                <th>Commission</th>
                                <th>Total Amount After Commission</th>
                            </tr>
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
                    $sales_report .= '<td>' . $item->get_quantity() . '</td>';
                    $sales_report .= '<td>' . $supplier_name . '</br>' . $supplier_email . '</td>';
                    $sales_report .= '<td>' . get_option('woocommerce_currency') . '' . wc_price($item->get_total()) . '</td>';
                    $sales_report .= '<td>' . $product_commission_final . '</td>';
                    $sales_report .= '<td>' . get_option('woocommerce_currency') . '' . wc_price($item->get_total() - $product_commission) . '</td>';
                    $sales_report .= '</tr>';

                    $total_sales += $item->get_quantity();
                    $total_order_amount += $item->get_total();
                }
            }
            $sales_report .= '<tr>
                                <td colspan="6">Total</td>
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
                    <td valign="top" width="600" style="width:600px;">
                    <![endif]-->
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                        style="max-width:100%; min-width:100%;" width="100%"
                                                        class="mcnTextContentContainer">
                                                        <tbody>
                                                            <tr>
    
                                                                <td valign="top" class="mcnTextContent"
                                                                    style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
    
                                                                    <p style="text-align: center;"><small>This report was
                                                                            generated automatically by the WooCommerce Sales Insights plugin developed by 
                                                                            <br/>Win Authority LLC.</small></p>
    
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
        $subject = 'Daily Sales Report - ' . $sales_report_date;
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
