<?php
//
// Recommended way to include parent theme styles.
// (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
//  

add_action('wp_enqueue_scripts', 'theme_enqueue_styles', 998);

function theme_enqueue_styles() {
    wp_enqueue_style('elessi-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('elessi-child-style', get_stylesheet_uri());
}

//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );



add_filter( 'woocommerce_product_tabs', 'my_remove_description_tab', 11 );
 
function my_remove_description_tab( $tabs ) {
  unset( $tabs['description'] );
  return $tabs;
}

add_filter( 'woocommerce_product_tabs', 'my_remove_reviews_tab' );
 
function my_remove_reviews_tab( $tabs ) {
  unset( $tabs['reviews'] );
  return $tabs;
}


//add_filter( 'wc_product_sku_enabled', '__return_false' );
//add_filter( 'woocommerce_is_purchasable', '__return_false');

//remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
//remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
//remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_images', 20 );

remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );

add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
/**
 * Override loop template and show quantities next to add to cart buttons
 * 
 */
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
 
    $html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
    $html .= woocommerce_quantity_input( array(), $product, false );
    $html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
    $html .= '</form>';
  
  return $html;
}


/*add_filter('woocommerce_is_purchasable', 'purchasable_course', 10, 2 );
function purchasable_course( $is_purchasable, $product ) {
  
  
  $allowed_user_roles = array('subscriber');

    $user = wp_get_current_user();

    if( array_intersect( $allowed_user_roles, $user->roles ) ) {
    
    
      return false;
    }
    else{
      return $is_purchasable;
    }
  }*/
    
    

/*add_filter( 'woocommerce_get_availability_text', 'bbloomer_custom_get_availability_text', 99, 2 );
  
function bbloomer_custom_get_availability_text( $availability, $product ) {

  $allowed_user_roles = array('subscriber');

    $user = wp_get_current_user();

    if( array_intersect( $allowed_user_roles, $user->roles ) ) {
      $stock = $product->get_stock_quantity();
   if ( $product->is_in_stock() && $product->managing_stock() ) $availability = $stock.'in hand';
   return $availability;
    }
    else{
      return $availability;
    }
   
}*/

add_action( 'woocommerce_thankyou', 'bbloomer_checkout_save_user_meta' );
 
function bbloomer_checkout_save_user_meta( $order_id ) 
{
   $order = wc_get_order( $order_id );
   $user_id = $order->get_user_id();

    $order_count = get_user_meta( $user_id, 'temp_count', true );

  $new_count = ($order_count) ? $order_count + 1 : 1; 
  update_user_meta( $user_id, 'temp_count', $new_count );
   
}
add_action( 'init', 'enable_catalog_mode' );

function enable_catalog_mode() {

  if ( is_admin() ) {
    return;
  }
  $user_id = get_current_user_id();
  $orders_capacity = get_user_meta( $user_id, 'allowed_order', true );
  $order_count = get_user_meta( $user_id, 'temp_count', true );
  
  if (!empty($order_count) && $orders_capacity == $order_count) {
    /*remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
*/
    add_filter( 'woocommerce_is_purchasable', '__return_false');

    wc_add_notice( __( 'We exceeded our capacity and we are sorry to say we can not take new orders for today!', 'woo-restrict-orders-per-days' ), 'notice' );

  }


}


/*add_action('woocommerce_before_order_notes', 'njengah_add_select_checkout_field');

function njengah_add_select_checkout_field( $checkout ) {

            echo '<h2>'.__('Next Day Delivery').'</h2>';

            woocommerce_form_field( 'daypart', array(

                'type'          => 'select',

                'class'         => array( 'njengah-drop' ),

                'label'         => __( 'Delivery options' ),

                'options'       => array(

                         'blank'             => __( 'Select a day part', 'njengah' ),

                    'morning' => __( 'In the morning', 'njengah' ),

                    'afternoon'           => __( 'In the afternoon', 'njengah' ),

                    'evening' => __( 'In the evening', 'njengah' )

                )

 ),

            $checkout->get_value( 'daypart' ));

}

//* Process the checkout

add_action('woocommerce_checkout_process', 'njengah_select_checkout_field_process');

function njengah_select_checkout_field_process() {

global $woocommerce;

// Check if set, if its not set add an error.

if ($_POST['daypart'] == "blank")

wc_add_notice( '<strong>Please select a day part under Delivery options</strong>', 'error' );

}
      
  
        
  //* Update the order meta with field value

add_action('woocommerce_checkout_update_order_meta', 'njengah_select_checkout_field_update_order_meta');

function njengah_select_checkout_field_update_order_meta( $order_id ) {

if ($_POST['daypart']) update_post_meta( $order_id, 'daypart', esc_attr($_POST['daypart']));

}
*/


//add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
add_action('woocommerce_after_checkout_shipping_form', 'njengah_add_select_checkout_field_sales_rep');

function njengah_add_select_checkout_field_sales_rep( $checkout ) {
  $user_id = get_current_user_id();
  $cust_number = get_user_meta( $user_id, 'Customer No', true );
  global $wpdb;
    $customer_Number = $wpdb->get_results("SELECT `SLSMAN`,`SLS_NAME` FROM `sales_rep` WHERE `CUST_NO` = '".$cust_number."'",ARRAY_A);
    ?>
    <p>Select Your Sales Rep</p>
    <select name="customer_sales_rep" id="sales_rep">
    <?php
    foreach ($customer_Number as $cn ) {?>
      
      
        <option value="<?php echo $cn['SLSMAN'];?>"><?php echo $cn['SLS_NAME'];?></option>
       

      <?php
    }
    ?>
    </select> 
    <?php

}      

 add_action('woocommerce_after_checkout_shipping_form', 'njengah_add_select_checkout_field');

function njengah_add_select_checkout_field( $checkout ) {
  $S_country = WC()->customer->get_shipping_country();
  $B_country = WC()->customer->get_billing_country();
  //echo $S_country;
?>
<p>Select Your Shipping Method</p>
<?php
  if($S_country == 'US'){
    global $wpdb;
    $ship_method = $wpdb->get_results("SELECT `shiping_method`,`code` FROM `Shipping_Method` WHERE `country` = 'US'",ARRAY_A);
    //print_r($ship_method);?>
    <select name="shipping_user_method" id="ship_method0">
    <?php
    foreach ($ship_method as $sm ) {?>
      
      
        <option value="<?php echo $sm['code'];?>"><?php echo $sm['shiping_method'];?></option>
       

      <?php
    }
    ?>
    </select> 
    <?php

    /*echo '<h2>'.__('Shipping Method').'</h2>';
        

            woocommerce_form_field( 'shipping_user_method', array(

                'type'          => 'select',

                'class'         => array( 'njengah-drop' ),

                'label'         => __( '' ),

                'options'       => array(

                    
                  'blank'   => __( 'Select a Method', 'njengah' ),
                    'U11' => __( 'U11', 'njengah' ),

                    'STD'  => __( 'STD', 'njengah' ),

                    'U43' => __( 'U43', 'njengah' ),
                    'U01' => __( 'U01', 'njengah' ),
                    'U21' => __( 'U21', 'njengah' ),
                    'F01' => __( 'F01', 'njengah' ),
                    'F06' => __( 'F06', 'njengah' ),
                    'F18' => __( 'F18', 'njengah' ),
                    'F11' => __( 'F11', 'njengah' ),
                    'F14' => __( 'F14', 'njengah' ),
                    'R02' => __( 'R02', 'njengah' ),
                    'CNT' => __( 'CNT', 'njengah' )

                )

 ),
     $checkout->get_value( 'shipping_user_method' ));*/

     
}
else if($S_country == 'CA')
{

  global $wpdb;
    $ship_method = $wpdb->get_results("SELECT `shiping_method`,`code` FROM `Shipping_Method` WHERE `country` = 'Canada'",ARRAY_A);
    //print_r($ship_method);?>
    <select name="shipping_user_method1" id="ship_method0">
    <?php
    foreach ($ship_method as $sm ) {?>
      
      
        <option value="<?php echo $sm['code'];?>"><?php echo $sm['shiping_method'];?></option>
       

      <?php
    }
    ?>
    </select> 
    <?php
  

}
else{

  global $wpdb;
    $ship_method = $wpdb->get_results("SELECT `shiping_method`,`code` FROM `Shipping_Method` WHERE `country` = 'International'",ARRAY_A);
    //print_r($ship_method);?>
    <select name="shipping_user_method2" id="ship_method0">
    <?php
    foreach ($ship_method as $sm ) {?>
      
      
        <option value="<?php echo $sm['code'];?>"><?php echo $sm['shiping_method'];?></option>
       

      <?php
    }
    ?>
    </select> 
    <?php
  
}
}
add_action('woocommerce_checkout_process', 'njengah_select_checkout_field_process');

function njengah_select_checkout_field_process() {

global $woocommerce;

// Check if set, if its not set add an error.

if ($_POST['shipping_user_method'] == "blank")

wc_add_notice( '<strong>Please select a shipping method </strong>', 'error' );

}
add_action('woocommerce_checkout_update_order_meta', 'njengah_select_checkout_field_update_order_meta_sales_rep');

function njengah_select_checkout_field_update_order_meta_sales_rep( $order_id ) {
  if ( isset( $_POST['customer_sales_rep'] ) ) {
  $val = $_POST['customer_sales_rep'];
        
        update_post_meta( $order_id, 'customer_sales_rep', $val);
        

        // Save the custom checkout field value as user meta data
        
    }

  }
add_action('woocommerce_checkout_update_order_meta', 'njengah_select_checkout_field_update_order_meta');

function njengah_select_checkout_field_update_order_meta( $order_id ) {
 if ( isset( $_POST['shipping_user_method'] ) ) {
  $val = $_POST['shipping_user_method'];
        
        update_post_meta( $order_id, 'shipping_custom_method', $val);
        

        // Save the custom checkout field value as user meta data
        
    }
    if ( isset( $_POST['shipping_user_method1'] ) ) {
  $val = $_POST['shipping_user_method1'];
        
        update_post_meta( $order_id, 'shipping_custom_method', $val);
        

        // Save the custom checkout field value as user meta data
        
    }
    if ( isset( $_POST['shipping_user_method2'] ) ) {
  $val = $_POST['shipping_user_method2'];
        
        update_post_meta( $order_id, 'shipping_custom_method', $val);
        

        // Save the custom checkout field value as user meta data
        
    }


}

//add_filter( 'woocommerce_cart_needs_payment', '__return_false' );

 

add_action('woocommerce_after_checkout_shipping_form', 'njengah_add_select_checkout_field_extra');

function njengah_add_select_checkout_field_extra( $checkout ) {
  $user_id = get_current_user_id();
  $user_class = get_user_meta( $user_id, 'CUST_ALLOC_TYPE', true );
      if($user_class == 'DLR' || $user_class == 'REP' )
      {
        

        echo '<div id="custom_checkout_field"><h2>' . __('Third Party Account Number') . '</h2>';

        ?>
        <!-- <input type="text" name="third_party" placeholder="enter your account no" required> -->
        <?php

        echo '</div>';

    }
    /*else if($user_class == 'REP')
      {
        

        echo '<div id="custom_checkout_field"><h2>' . __('Third Party Account Number') . '</h2>';

        ?>
        <input type="text" name="third_party" placeholder="enter your account no" required>
        <?php

        echo '</div>';

    }*/

  }

  add_action('woocommerce_checkout_process', 'njengah_select_checkout_field_process_third');

function njengah_select_checkout_field_process_third() {

global $woocommerce;
 $user_id = get_current_user_id();
$user_class = get_user_meta( $user_id, 'CUST_ALLOC_TYPE', true );



// Check if set, if its not set add an error.

if ($_POST['third_party'] == "")

wc_add_notice( '<strong>Please enter third party account number </strong>', 'error' );

}

  add_action('woocommerce_after_checkout_shipping_form', 'njengah_add_select_checkout_field_text');

function njengah_add_select_checkout_field_text( $checkout ) {
  ?>
  <p> Shipping Policy</p>
 
<p>The standard policy of KnollTextiles is to ship all packages via UPS ground.
KnollTextiles will pay UPS ground charges on all sample orders
shipped to the United States, Canada and Puerto Rico.</p>

<p>Reps may use the drop down menu to select an alternate shipping transit time,
while being conscious of increased costs that will be incurred by KnollTextiles.</p>

<p>Customers must provide their UPS/Fedex number for all
expedited shipments and international shipments with the
exception of Canada and Puerto Rico.</p>

<p>A Fedex number is required for all FedEx shipments.</p>
  <?php

  }


add_action('woocommerce_checkout_update_order_meta', 'njengah_select_checkout_field_update_order_meta1');

function njengah_select_checkout_field_update_order_meta1( $order_id ) {
 if ( isset( $_POST['third_party'] ) ) {
  $val1 = $_POST['third_party'];
        
        update_post_meta( $order_id, 'third_party', $val1);
        

        // Save the custom checkout field value as user meta data
        
    }
   /* if ( isset( $_POST['third_party1'] ) ) {
  $val2 = $_POST['third_party1'];
        
        update_post_meta( $order_id, 'third_party1', $val2);
        

        // Save the custom checkout field value as user meta data
        
    }*/
    


}


add_action('woocommerce_after_checkout_shipping_form', 'njengah_add_select_checkout_checkbox_extra');

function njengah_add_select_checkout_checkbox_extra( $checkout ) {
  $user_id = get_current_user_id();?>

  <input type="checkbox" id="address_1" name="address_book" value="yes">
    <label for="address_book"> Save this Address</label><br>

 <?php     
  }

// On cart page
add_action( 'woocommerce_cart_collaterals', 'remove_cart_totals', 9 );
function remove_cart_totals(){
    // Remove cart totals block
    remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

    // Add back "Proceed to checkout" button (and hooks)
    echo '<div class="cart_totals">';
    do_action( 'woocommerce_before_cart_totals' );

    echo '<div class="wc-proceed-to-checkout">';
    do_action( 'woocommerce_proceed_to_checkout' );
    echo '</div>';

    do_action( 'woocommerce_after_cart_totals' );
    echo '</div><br clear="all">';
}
// On checkout page
/*add_action( 'woocommerce_checkout_order_review', 'remove_checkout_totals', 1 );
function remove_checkout_totals(){
    // Remove cart totals block
    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
}*/


/*remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 ); */
add_filter( 'woocommerce_get_price_html', function( $price ) {
  

  return '';
} );


add_filter( 'woocommerce_cart_item_price', '__return_false' );
add_filter( 'woocommerce_cart_item_subtotal', '__return_false' );
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );

add_filter('woocommerce_billing_fields','wpb_custom_billing_fields');
// remove some fields from billing form
// ref - https://docs.woothemes.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
function wpb_custom_billing_fields( $fields = array() ) {
  unset($fields['billing_first_name']);
  unset($fields['billing_last_name']);
  unset($fields['billing_email']);
  unset($fields['billing_company']);
  unset($fields['billing_address_1']);
  unset($fields['billing_address_2']);
  unset($fields['billing_state']);
  unset($fields['billing_city']);
  unset($fields['billing_phone']);
  unset($fields['billing_postcode']);
  unset($fields['billing_country']);

  return $fields;
}
add_filter( 'gettext', 'wc_billing_field_strings', 20, 3 );

function wc_billing_field_strings( $translated_text, $text, $domain ) {
switch ( $translated_text ) {
case 'Billing Address' :
$translated_text = __( 'Shipping Details', 'woocommerce' );
break;
case 'Billing details' :
$translated_text = __( 'Shipping Details', 'woocommerce' );
break;
case 'Ship to a different address?' :
$translated_text = __( 'Ship to this address', 'woocommerce' );
break;

case 'Wishlist' :
$translated_text = __( 'Favorites', 'woocommerce' );
break;

case 'Notes about your order, e.g. special notes for delivery.' :
$translated_text = __( 'Notes For Recipient', 'woocommerce' );
break;
}


return $translated_text;
}

add_action( 'wp_footer', 'bbloomer_add_jscript_checkout', 9999 );
 
function bbloomer_add_jscript_checkout() {
   global $wp;
   if ( is_checkout()) { ?>
  <script>
    
    //jQuery('#custom_checkout_field').hide();
    
    jQuery('#ship_method0').change(function(){
      var value_ship = jQuery('#ship_method0 :selected').text();
      
      if(value_ship == 'FedEx - Priority OvrN' || value_ship == 'FedEx - Standard OvrN' 
        || value_ship == 'FedEx - 1st Overnight' || value_ship == 'FedEx - Second Day' || value_ship == 'FedEx - Express Saver' || value_ship == 'FedEx - Intnl Priority'  || value_ship == 'FedEx - Intnl Economy' ){
         jQuery('#custom_checkout_field').show();
        jQuery('#custom_checkout_field').html('<input type="text" name="third_party" placeholder="enter your account no">');
      }
      else{
        jQuery('#custom_checkout_field').html('<input type="text" name="third_party" value="NULL" placeholder="enter your account no">');
        jQuery('#custom_checkout_field').hide();
      }
      
      jQuery("label[for*='shipping_method_0_free_shipping52']").html(value_ship);

    });
    jQuery('.user-name').html('test');
    //jQuery('.nasa-tit-wishlist nasa-sidebar-tit text-center').html('Favourites');
    /*jQuery(document).on('click', '.nasa-icon cart-icon icon-nasa-cart-3', function (event) {
               event.preventDefault();
      jQuery('.woofc-icon-cart7').click();
});*/
    
    //jQuery('#c_ship').html(value_ship);
//jQuery('.order').html('Reference Number');
    /*jQuery('.product-total').hide();
      jQuery('.cart-subtotal').hide();
      jQuery('.order-total').hide();*/
      

</script>;
  <?php
   }
   if ( is_account_page() ){?>
    <script>
      var cuna = jQuery('.cu_na').html();
    jQuery('.user-name').html(cuna);
    </script>
    <?php

   }
}

add_action( 'woocommerce_thankyou', 'bbloomer_checkout_generate_mail_user' );
 
function bbloomer_checkout_generate_mail_user( $order_id ) 
{
   $order = wc_get_order( $order_id );
   //$con_Number = '600000';
   
   $user_id = get_current_user_id();
   $control_number = $ncn;
   $user_id = $order->get_user_id();
   $timezone = date('Y-m-d H:i:s');
   $mode = "P";
   $client_id = $user_class = get_user_meta( $user_id, 'TENANT_ID', true );
   $accont_ship_name = "Knoll Textiles";
   $filer1 = '';
   $doc_type  = "OR";
   $doc_version = "250";
   $d_code = "D";
   $new_con_number = get_post_meta(600000,'con_number',true);
   $c_number = $new_con_number + 1;
   update_post_meta(600000,'con_number',$c_number);
   $F2 = '';
   $Filler_3 = 'N';
   $con_number = $new_con_number + 1;
   $cust_no = get_user_meta( $user_id, 'Customer No', true );
   $Sold_to_att = append_whitespace('',30);
   $sold_to_phone = append_whitespace('',30);
   /*$sold_FName = $order->get_shipping_first_name();
   $sold_LName = $order->get_shipping_last_name();
   $sold_Name = $sold_FName . $sold_LName;*/
   $SLN = get_user_meta($user_id,'SHORT_NAME',true);
   $sold_to_name = append_whitespace($SLN,30);
   //$sold_to_name = append_whitespace($sold_to_name,30);
   $sold_to_add = append_whitespace($order->get_shipping_address_1(),30);
   $sold_to_add2 = append_whitespace($order->get_shipping_address_2(),30);
   $sold_to_city = append_whitespace($order->get_shipping_city(),20);
   $sold_to_state = $order->get_shipping_state();
   $sold_to_zip = append_whitespace($order->get_shipping_postcode(),10);
   $sold_to_country = append_whitespace($order->get_shipping_country(),3);
   $filer4 = '';
   $ini = 'WEB';
   $reason_code = 'W';
   $hold_code = '';
   $po_number = '';
   $crd_type = '';
   $card_number = '';
   $cc_e_date = '';
   $cc_e_code = '';
   $cc_name = '';
   $cc_app_code = '';
   $total_cc = '';
   $pn_ref = '';
   $sales_loc = 'ZM';
   $date_req = '';
   $filler5='';
   $ship_to = 'DROP';
   $add_save = 'Y';
   $sold_FName = $order->get_shipping_first_name();
   $sold_LName = $order->get_shipping_last_name();
   $sold_Name = $sold_FName .' '. $sold_LName;
   $attention = $sold_Name;
   $rec_phone = append_whitespace('',30);
   $com_name = append_whitespace($order->get_shipping_company(),30);
   $add_line1 = $order->get_shipping_address_1();
   $add_line2 = $order->get_shipping_address_2();
   $city = append_whitespace($order->get_shipping_city(),20);
   $state = $order->get_shipping_state();
   $zip = append_whitespace($order->get_shipping_postcode(),10);
   $country = append_whitespace($order->get_shipping_country(),3);
   $ba_rec_type = append_whitespace('',6);
   $web_ser_cust_class = append_whitespace('',6);
   $filer6 = 'N';
   //$ba_address = '';
   $ba_attention = '';
   $ba_rec_phn ='';
   $ba_name = '' ;
   $ba_add1 = '';
   $ba_ad2 = '';
   $ba_city = '';
   $ba_state = ''; 
   $ba_zip = '';
   $ba_country = '';
   $ship_via = get_post_meta($order_id,'shipping_custom_method',true);
   $ship_via_account = get_post_meta($order_id,'third_party',true);
   if($ship_via_account == 'NULL'){
    $ship_via_account  = '';

   }
   
   $email_fax = 'M';
   $email_fax_no = get_post_meta($order_id,'customer_email',true);
   $email_cc = get_post_meta($order_id,'customer_email_cc',true);
   $filer7 = 'N';
   $note = '';
   $sales_rep =get_post_meta($order_id,'customer_sales_rep',true);;


   $control_no = $c_number;
   $quantity = '';
   $product_name = '';
   $item_sku = '';
   $item_det = array();
   $ord_lim = array();

   //print_r($order->get_items());
   foreach ( $order->get_items() as $item_id => $item ) {
    
   $quantity = $item->get_quantity();
   //$product_name = $item->get_name();
   //$sku = $item->get_sku();
    $product = wc_get_product($item->get_product_id());
    $item_sku = $product->get_sku();

    //$item_sku = str_replace('&', '&amp;', $item_skuu); 

    $item_det[] = array(
                    'quantity' => $quantity,
                    'sku' => $item_sku,
                    
                );

    $ord_lim[] = array(
                    'id' => $item->get_product_id(),
                    'quan' => $quantity,
                    'item_no' => $item_sku,
                    
                );
}
//print_r($item_det);


   $item_no = $item_sku;
   $filer8 = '';
   $item_qty = $quantity;
   $qty_assigned = '';
   $qty_unassigned = '';
   $dis_per = '';
   $price_ovd = 'N';
   $filer9 = '';
   $ware_id = '10';
   $line_note = '';
   $item_details = '';
foreach($item_det as $value){
$item_details.= $control_no.'~'.$value['sku'].'~'.$filer8.'~'.$value['quantity'].'~'.$qty_assigned.'~'.$qty_unassigned.'~'.$dis_per.'~'.$price_ovd.'~'.$filer9.'~'.$ware_id.'~'.$line_note.PHP_EOL;
}
global $wpdb;
$cust_TYPE = get_user_meta( $user_id, 'CUST_ALLOC_TYPE', true );
foreach($ord_lim as $oli){
$id = $oli['id'];
$quan = $oli['quan'];
$sku = $oli['item_no'];
  $sql = "INSERT INTO `Order_limit`(`ID`,`USER_ID`, `ITEM_NO`, `CUSTOMER_TYPE`, `QUANTITY`) VALUES ('".$id."', '".$user_id."','".$sku."', '".$cust_TYPE."', '".$quan."')";

$wpdb->query($sql);
  }




    $myfile = fopen("/var/www/html/txtfiles/order.txt", "w") or die("Unable to open file!");
    $txt = $timezone.'~'.$mode.'~'.$client_id.'~'.$accont_ship_name.'~'.$filer1.'~'.$doc_type.'~'.$doc_version.'~'.$d_code.'~'.$c_number.'~'.$F2.'~'.$Filler_3.PHP_EOL;
    fwrite($myfile, $txt);
    $txt = $con_number.'~'.$cust_no.'~'.$Sold_to_att.'~'.$sold_to_phone.'~'.$sold_to_name.'~'.$sold_to_add.'~'.$sold_to_add2.'~'.$sold_to_city.'~'.$sold_to_state.'~'.$sold_to_zip.'~'.$sold_to_country.'~'.$filer4.'~'.$ini.'~'.$reason_code.'~'.$hold_code.'~'.$po_number.'~'.$crd_type.'~'.$card_number.'~'.$cc_e_date.'~'.$cc_e_code.'~'.$cc_name.'~'.$cc_app_code.'~'.$total_cc.'~'.$pn_ref.'~'.$sales_loc.'~'.$date_req.'~'.$filler5.'~'.$ship_to.'~'.$add_save.'~'.$attention.'~'.$rec_phone.'~'.$com_name.'~'.$add_line1.'~'.$add_line2.'~'.$city.'~'.$state.'~'.$zip.'~'.$country.'~'.$ba_rec_type.'~'.$web_ser_cust_class.'~'.$filer6.'~'.$ba_attention.'~'.$ba_rec_phn.'~'.$ba_name.'~'.$ba_add1.'~'.$ba_ad2.'~'.$ba_city.'~'.$ba_state.'~'.$ba_zip.'~'.$ba_country.'~'.$ship_via.'~'.$ship_via_account.'~'.$email_fax.'~'.$email_cc.'~'.$email_fax_no.'~'.$filer7.'~'.$note.'~'.$sales_rep.PHP_EOL;
    fwrite($myfile, $txt);
    //$txt = $control_no.'~'.$item_no.'~'.$filer8.'~'.$item_qty.'~'.$qty_assigned.'~'.$qty_unassigned.'~'.$dis_per.'~'.$price_ovd.'~'.$filer9.'~'.$ware_id.'~'.$line_note.PHP_EOL;
    $txt = $item_details;
    fwrite($myfile, $txt);
    fclose($myfile);






        $directory = $_SERVER['DOCUMENT_ROOT'].'/txtfiles';
        $o_name = 'order.txt';
        $subject_admin = "Order Template";
        //$email_admin = 'sidharthm@suyogindia.com';
        $email_admin = 'scottmurphy@thbred.com,sidharthm@suyogindia.com,danjones@thbred.com';
        $attach_url =  $directory . '/' .$o_name;
        /*echo $attach_url;
        exit;*/
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message_admin = '<div style="width:600px; text-align:center; background:#F30; display:inline-block; padding:15px;">
<p style=" background:#FFF; padding-top: 10px; padding-bottom: 10px; margin-top:0; margin-bottom:0;"><img src="http://20.124.249.167/wp_multisite/knoll/wp-content/uploads/sites/2/2021/12/logo-knol.png" /></p>
<p style="text-align: center; font-size: 23px; text-transform: uppercase; background: #808080; color: #FFF; padding-top: 10px; padding-bottom: 10px; margin-top: 0; margin-bottom: 0;">Item Order Format</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" style="text-align:left; background:#FFF;">
  <tr>
    <th style="width:50%; border:1px solid #ccc; padding:5px;">Name</th>
    <th style="width:50%; border:1px solid #ccc; padding:5px;">Format</th>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Time Stamp</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$timezone.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Mode</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$mode.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">ClientId</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$client_id.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Account Ship Name</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$accont_ship_name.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 1</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filer1.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Doc Type</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$doc_type.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Doc Version</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$doc_version.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">D Code</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$d_code.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Control Number</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$c_number.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 2</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$F2.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 3</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$Filler_3.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Control Number</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$con_number.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">CUST_NO</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$cust_no.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To Attention</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$Sold_to_att.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To Phone</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_phone.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To Name</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_name.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To Address</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_add.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To Address 2</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_add2.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To City</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_city.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To ZIP</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_zip.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sold To Country</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sold_to_country.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 4</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filer4.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Initials</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ini.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sales Reason Code</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$reason_code.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Hold Code</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$hold_code.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">PO Number</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$po_number.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Card Type</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$crd_type.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Card Number</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$card_number.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">CC Exp Date</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$cc_e_date.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">CC Sec. Code</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$cc_e_code.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">CC Name</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$cc_name.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">CC Approval Code</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$cc_app_code.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Total CC</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$total_cc.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">PN Ref</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$pn_ref.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sales Location</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sales_loc.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Date Required</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$date_req.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 5</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filler5.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Ship To</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ship_to.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Address Save</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$add_save.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Attention</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$attention.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Recipient Phone</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$rec_phone.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Company Name</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$com_name.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Address Line 1</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$add_line1.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Address Line 2</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$add_line2.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">State</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$state.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Zip</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$zip.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Country</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$country.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Recipient Type</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_rec_type.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Web Service Customer Class</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$web_ser_cust_class.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 6</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filer6.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Address</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_address.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Attention</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_attention.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Recip Phone</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_rec_phn.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Name</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_name.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Address 1</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_add1.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Address 2</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_ad2.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA City</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_city.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA State</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_state.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Zip</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_zip.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">BA Country</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ba_countery.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Ship via</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ship_via.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Ship via Account</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ship_via_account.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Email Fax</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$email_fax.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Email Fax No</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$email_fax_no.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Email CC</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$email_cc.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 7</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filer7.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Note</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$note.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Sales Rep</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$sales_rep.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Line Break</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">&nbsp;</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Control Number</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$control_no.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Item no</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$item_no.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 8</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filer8.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Item Qty</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$item_qty.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Qty Assigned</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$qty_assigned.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Qty Unassigned</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$qty_unassigned.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Discount Percent</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$dis_per.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Price Override</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$price_ovd.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Filler 9</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$filer9.'</td>
  </tr>
  <tr style="background:#E8E8E8">
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Warehouse ID</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$ware_id.'</td>
  </tr>
  <tr>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">Line Note</td>
    <td style="width:50%; border:1px solid #ccc; padding:5px;">'.$line_note.'</td>
  </tr>
</table>
</div>';
        wp_mail( $email_admin, $subject_admin, $message_admin,$headers,$attach_url);
       WC()->cart->empty_cart();
   
}
function append_whitespace($string,$max){
  $length = strlen($string);
  if($length < $max){
    $res = $max - $length;
    $string.= str_repeat(' ',$res);

  }
  return $string;

}

/*add_filter( 'woocommerce_loop_add_to_cart_link', 'replace_loop_add_to_cart_button', 10, 2 );
function replace_loop_add_to_cart_button( $button, $product  ) {

    $CUSTOMER_TYPE = get_user_meta( $user_id, 'CUST_ALLOC_TYPE', true );
    $PRODUCT_ID = $product->get_id();
    global $wpdb;
    $LIMIT =  $wpdb->get_results("SELECT `ORDER_LIMIT` FROM `customer_allocation` WHERE `ID` = '".$PRODUCT_ID."' AND `CUSTOMER_TYPE` = '".$CUSTOMER_TYPE."'",ARRAY_A);
    $ORDERED_PRODUCT = get_results("SELECT SUM(`QUANTITY`)AS QUAN FROM `Order_limit` WHERE `USER_ID` = '".$user_id."' AND `CUSTOMER_TYPE` = '".$CUSTOMER_TYPE."' AND `ID` = '".$PRODUCT_ID."';",ARRAY_A);

    if( $ORDERED_PRODUCT['QUAN']  == $LIMIT['ORDER_LIMIT'] ){
        $button_text = __( "View product", "woocommerce" );
        $button = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
    }

    return $button;
}*/
add_action('woocommerce_before_checkout_shipping_form', 'njengah_add_select_checkout_field_email');

function njengah_add_select_checkout_field_email( $checkout ) {
  ?>
   <input type="email" name="email1" placeholder="Enter your email address">
   <input type="email" name="email2" placeholder="Enter additional email address">
   <?php
}

add_action('woocommerce_checkout_update_order_meta', 'njengah_select_checkout_field_update_order_meta_email');

function njengah_select_checkout_field_update_order_meta_email( $order_id ) {
  if ( isset( $_POST['email1'] ) ) {
  $email_primary = $_POST['email1'];
  $email_cc = $_POST['email2'];
        
        update_post_meta( $order_id, 'customer_email', $email_primary);
        update_post_meta( $order_id, 'customer_email_cc', $email_cc);
        

        // Save the custom checkout field value as user meta data
        
    }
}

add_filter( 'woocommerce_add_to_cart_validation', 'so_validate_add_cart_item', 10, 5 );
function so_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
    global $wpdb;
    if ( is_user_logged_in() ) {
        $limitOrder = 0;
        $limitDays = 0;
        $totalOrderedQty = 0;
        $pid = $product_id;
        $qty = $quantity;
        $userID = get_current_user_id();
        $userType = get_user_meta($userID,'CUST_ALLOC_TYPE',true);
        $userTypeNew = get_user_meta($userID,'CUST_CLASS',true);
        if($userType == ''){
            $userType = $userTypeNew;
        }
        $currentItemOrderQty = 0;
        if($userType == 'ADM'){
            return $passed;
        }
       //$currentItemOrderQty = 0;

        $query = "SELECT * FROM `customer_allocation` WHERE `CUSTOMER_TYPE`='$userType' AND `ID`='$pid' LIMIT 1";
        $fetchData = $wpdb->get_results($query);
        if(count($fetchData)){
            $limitOrder = $fetchData[0]->ORDER_LIMIT;
            $limitDays = $fetchData[0]->LIMIT_DAYS;
            $from = date("Y-m-d"); // current date
            $from = strtotime('-'.$limitDays.' day', strtotime($from));
            $from = date('Y-m-d',$from);
            if($limitDays > 0){
                $queryforcheckuserorder = "SELECT SUM(`QUANTITY`) AS QTY FROM `Order_limit` WHERE `ID`='$pid' AND `USER_ID`='$userID' AND `CUSTOMER_TYPE`='$userType' AND DATE_FORMAT(DATE, '%Y-%m-%d') >= '$from'";
            }else{
                $queryforcheckuserorder = "SELECT SUM(`QUANTITY`) AS QTY FROM `Order_limit` WHERE `ID`='$pid' AND `USER_ID`='$userID' AND `CUSTOMER_TYPE`='$userType'";
            }
            $getUserorderQty = $wpdb->get_results($queryforcheckuserorder);
            if(count($getUserorderQty) > 0){
                $totalOrderedQty = $getUserorderQty[0]->QTY;
                if($totalOrderedQty <= $limitOrder){
                    $limitOrder =  $limitOrder - $totalOrderedQty;
                }
            }
            if($totalOrderedQty >= $limitOrder){
                $passed = false;
                wc_add_notice( __( 'Your order limit for this item has been exceed.', 'textdomain' ), 'error' );
            }
            if($qty > $limitOrder){
                $passed = false;
                wc_add_notice( __( 'You can order maximum qty is '.$limitOrder, 'textdomain' ), 'error' );
            }
            
        }else{
            $passed = false;
            wc_add_notice( __( 'You are not allowed to order this item.', 'textdomain' ), 'error' );
        }
    } else {
        $passed = false;
        wc_add_notice( __( 'Please login first to order products.', 'textdomain' ), 'error' );
    }
    return $passed;
}


add_filter('woocommerce_update_cart_validation','on_update_cart_limit_qty',10,4);

function on_update_cart_limit_qty( $passed, $cart_item_key, $values, $quantity ) {

   global $wpdb;
    if ( is_user_logged_in() ) {
        $limitOrder = 0;
        $limitDays = 0;
        $totalOrderedQty = 0;
        $pid = $values['product_id'];
        $qty = $quantity;
        $userID = get_current_user_id();
        $userType = get_user_meta($userID,'CUST_ALLOC_TYPE',true);
        $userTypeNew = get_user_meta($userID,'CUST_CLASS',true);
        if($userType == ''){
            $userType = $userTypeNew;
        }
        $currentItemOrderQty = 0;
        if($userType == 'ADM'){
            return $passed;
        }
        $query = "SELECT * FROM `customer_allocation` WHERE `CUSTOMER_TYPE`='$userType' AND `ID`='$pid' LIMIT 1";
        $fetchData = $wpdb->get_results($query);
        if(count($fetchData)){
            $limitOrder = $fetchData[0]->ORDER_LIMIT;
            $limitDays = $fetchData[0]->LIMIT_DAYS;
            $from = date("Y-m-d"); // current date
            $from = strtotime('-'.$limitDays.' day', strtotime($from));
            $from = date('Y-m-d',$from);
            if($limitDays > 0){
                $queryforcheckuserorder = "SELECT SUM(`QUANTITY`) AS QTY FROM `Order_limit` WHERE `ID`='$pid' AND `USER_ID`='$userID' AND `CUSTOMER_TYPE`='$userType' AND DATE_FORMAT(DATE, '%Y-%m-%d') >= '$from'";
            }else{
                $queryforcheckuserorder = "SELECT SUM(`QUANTITY`) AS QTY FROM `Order_limit` WHERE `ID`='$pid' AND `USER_ID`='$userID' AND `CUSTOMER_TYPE`='$userType'";
            }
            $getUserorderQty = $wpdb->get_results($queryforcheckuserorder);
            if(count($getUserorderQty) > 0){
                $totalOrderedQty = $getUserorderQty[0]->QTY;
                if($totalOrderedQty <= $limitOrder){
                    $limitOrder =  $limitOrder - $totalOrderedQty;
                }
            }
            if($totalOrderedQty >= $limitOrder){
                $passed = false;
                wc_add_notice( __( 'You order limit for this item has been exceed.', 'textdomain' ), 'error' );
            }
            if($qty > $limitOrder){
                $passed = false;
                wc_add_notice( __( 'You can order maximum qty is '.$limitOrder, 'textdomain' ), 'error' );
            }
            
        }else{
            $passed = false;
            wc_add_notice( __( 'You are not allowed to order this item.', 'textdomain' ), 'error' );
        }
    } else {
        $passed = false;
        wc_add_notice( __( 'Please login first to order products.', 'textdomain' ), 'error' );
    }
    return $passed;
}
  //return validate_product_qty( $values['product_id'], $quantity, 'update' );

add_action( 'woocommerce_before_shop_loop_item', 'action_woocommerce_before_shop_loop'); 
function action_woocommerce_before_shop_loop() { 
    if ( is_user_logged_in() ) {
        global $product;
        global $wpdb;
        $userID = get_current_user_id();
        $pid = $product->get_id();
        $userType = get_user_meta($userID,'CUST_ALLOC_TYPE',true);
        $userTypeNew = get_user_meta($userID,'CUST_CLASS',true);
        if($userType == ''){
            $userType = $userTypeNew;
        }
        if($userType != ''){
            if($userType != 'ADM'){
                $query = "SELECT `ID` FROM `customer_allocation` WHERE `CUSTOMER_TYPE`='$userType' AND `ID`='$pid' LIMIT 1";
                $fetchData = $wpdb->get_results($query);
                if(count($fetchData) == 0){ ?>
                <style>
                    <?php echo '.post-'.$pid.' {display:none;}'?>
                </style>
                <?php }
            }
        }
    }
}; 