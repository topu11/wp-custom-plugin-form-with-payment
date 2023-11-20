<?php
/*
 * Plugin Name:       EncoderIT Custom Form
 * Plugin URI:        https://encoderit.net/
 * Description:       Handle customized form with the plugin.
 * Version:           1.0.0
 */

 define('ENCODER_IT_CUSTOM_FORM_SUBMIT', time());
 define('ENCODER_IT_STRIPE_PK',"pk_test_51OD1o3HXs2mM51TXR04wpLYzxxWNpOQWZr8Y84oV0Bp5aP1sB0gVic7JqBdrOgQmqYAwT7a9TOfq4UBG5ioifu9F00VwcHhkCb");
 define('ENCODER_IT_STRIPE_SK',"sk_test_51OD1o3HXs2mM51TXAPMu48pbSpxilR2QjxiXEipq60TE8y96wg51zs9qPSDZomhDtYGcmwIFPboEgFaHi1SINsNZ00FZ8b7i8R");
 define('ENCODER_IT_PAYPAL_CLIENT','AVT1TGV_xT-FR1XRXZdKgsyoXIhHf_N4-j26F0W6bYXgLcv4r2jJLu7Bsa1aabiU-0pVGrDFUIdOpvrQ');

 require_once( dirname( __FILE__ ).'/includes/create_custom_tables.php' );
 require_once( dirname( __FILE__ ).'/includes/admin_functionalities.php' );
 require_once( dirname( __FILE__ ).'/includes/user_functionalities.php' );
 require_once( dirname( __FILE__ ).'/includes/ajax_endpoint.php' );
 require_once( dirname( __FILE__ ).'/stripe-php-library/init.php' );
 
 


 register_activation_hook(__FILE__, array( 'encoderit_create_custom_table', 'create_custom_tables' ));

 add_action( 'admin_menu', 'admin_menu' );

 function admin_menu()
 {
    add_menu_page('Custom Services', 'Custom Services', 'manage_options', 'encoderit-custom-services',array( 'encoderit_admin_functionalities', 'get_service_list' ), 'dashicons-admin-generic', 4);

    add_submenu_page('options.php', 'Service Update', 'Service Update', 'manage_options', 'encoderit-custom-service-update', array( 'encoderit_admin_functionalities', 'update_service' ));

    add_submenu_page('encoderit-custom-services', 'Add new Service', 'Add new Service', 'manage_options', 'encoderit-custom-service-create', array( 'encoderit_admin_functionalities', 'add_new_service' ));

    add_menu_page('Cases', 'Cases', 'read', 'encoderit-custom-cases-user',array( 'encoderit_user_functionalities', 'get_all_case_by_user' ), 'dashicons-admin-generic', 4);

    //add_submenu_page('', 'Service Update', 'Service Update', 'manage_options', 'encoderit-custom-service-update', 'encoderit_details_subscriber');
    add_submenu_page('encoderit-custom-cases-user', 'Add New Case', 'Add New Case', 'read', 'encoderit-custom-cases-user-create', array( 'encoderit_user_functionalities', 'add_new_case_by_user' ));

    add_submenu_page('options.php', 'Case View', 'Case View', 'read', 'encoderit-custom-cases-user-view', array( 'encoderit_user_functionalities', 'view_single_case' ));

 }


 
function admin_enqueue_scripts_load()
{


	//enqueue js
   

   wp_register_script('encoderit_custom_form_sweet_alert_admin', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), ENCODER_IT_CUSTOM_FORM_SUBMIT, true);
   
   wp_register_script('encoderit_custom_form_stripe_admin', 'https://js.stripe.com/v3/', array(), ENCODER_IT_CUSTOM_FORM_SUBMIT);

   //$paymal_url="https://www.paypal.com/sdk/js?client-id=".ENCODER_IT_PAYPAL_CLIENT;

   wp_register_script('encoderit_custom_form_js_admin', plugins_url('assets/js/main.js',__FILE__ ), array(), ENCODER_IT_CUSTOM_FORM_SUBMIT);

   wp_register_script('encoderit_custom_form_js_zs_zip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',array(),ENCODER_IT_CUSTOM_FORM_SUBMIT);

  // wp_enqueue_style('encoderit_admin_custom_plugin_css');

   
   wp_enqueue_script('encoderit_custom_form_stripe_admin');
   wp_enqueue_script('encoderit_custom_form_sweet_alert_admin');
   wp_enqueue_script('encoderit_custom_form_js_zs_zip');
   wp_enqueue_script('encoderit_custom_form_js_admin');

	wp_enqueue_media();

	
}
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_load');


/***********Ajax Functionalities ************/
add_action('wp_ajax_enoderit_custom_form_submit', array('encoderit_ajax_endpoints','enoderit_custom_form_submit'));
add_action('wp_ajax_enoderit_custom_form_admin_submit', array('encoderit_admin_functionalities','enoderit_custom_form_admin_submit'));