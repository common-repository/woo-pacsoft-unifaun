<?php

/**
 * Plugin Name: Pacsoft/Unifaun integration for WooCommerce
 * Description: Ship your WooCommerce orders with over 150 major freight companies around the world. Book transport and print packing slips with Pacsoft/Unifaun.
 * Version: 2.99.1
 * Author: Wetail
 * Author URI: https://wetail.se/
 * License: GPL2
 * WC requires at least: 3.0.0
 * WC tested up to: 8.3.1
**/

require_once "autoload.php";

define( 'WOOCOMMERCE_PACSOFT_UNIFAUN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMMERCE_PACSOFT_UNIFAUN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCOMMERCE_PACSOFT_UNIFAUN_VERSION', '2.99.1' );

use src\utils\WUP_Credentials;
use src\WUP_Ajax;
use src\WUP_KSS_processor;
use src\WUP_Order_Controller;
use src\WUP_Plugin;
use src\WUP_Track_Shipment;

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

/**
 * admin_init
 */
add_action( 'admin_init', function() {

    if( wp_doing_ajax() ){
        return;
    }

    add_action( 'load-post.php', [ "src\WUP_Plugin", "add_meta_boxes" ] );
    add_action( 'load-post-new.php', [ "src\WUP_Plugin", "add_meta_boxes" ] );
    add_action( 'save_post', [ "src\WUP_Plugin", "save_unifaun_customs_code" ] );
    add_action( 'woocommerce_shop_order_list_table_custom_column', [ 'src\admin\orders\WUP_Admin_Template_Table', 'get_column_content' ], 10, 2 );
    add_filter( 'woocommerce_shop_order_list_table_columns', [ 'src\admin\orders\WUP_Admin_Template_Table', 'add_columns' ] );
    add_action( 'admin_head', [ 'src\admin\orders\WUP_Admin_Template_Table', 'add_templates' ] );
    if ( isset( $_GET['page'] ) && $_GET['page'] == 'woocommerce-pacsoft' || is_options_page( $_SERVER['REQUEST_URI'] ) ){
        WUP_Plugin::add_settings();
    }

	WUP_Plugin::add_admin_scripts();

} );


function is_options_page( $request_uri ){
    $arr = explode('/', $request_uri );

    if( 'wp-admin' === $arr[count($arr) - 2] && 'options.php' === $arr[count($arr) - 1] ){
        return true;
    }
    return false;
}

/**
 * Add settings page
 */
add_action( 'admin_menu', function() {
	WUP_Plugin::add_settings_page();
} );

/**
 * Check API
 */
add_filter( 'pacsoft_check_license', function() {
	return WUP_Credentials::check_license();
} );

/**
 * Edit view
 */
add_action( 'load-edit.php', function() {
	WUP_Plugin::on_post_edit_view();
} );


add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( WUP_Plugin::TEXTDOMAIN, FALSE, basename( dirname( __FILE__ ) ) . '/languages/'  );
});

/**
 * Auto sync
 */
if( get_option( 'pacsoft_on_order_status' ) && ! get_option( 'pacsoft_sync_with_options' ) ) {
	add_action( 'woocommerce_order_status_' . get_option( 'pacsoft_on_order_status' ), function( $order_id ) {
		try {// Its here!!!!! When you click 'sync order' in Woocommerce
            if( ! get_post_meta( $order_id, '_pacsoft_order_synced', true ) ){
                WUP_Order_Controller::sync( $order_id );
            }
		}
		catch( Exception $error ) {
			error_log($error->getMessage());
		}
	} );
}

/**
 * Track link in email
 */
if( get_option( 'pacsoft_track_link_on_order_status' ) ) {
    add_action( 'woocommerce_email_order_meta',  function( $order ) {
        if ( get_option( 'pacsoft_track_link_on_order_status' ) == $order->get_status() ) {
            WUP_Track_Shipment::echo_track_link( $order );
        }
    }, 10, 1);
}


/**
 * AJAX sync
 */
add_action( 'wp_ajax_pacsoft_sync_order', function() {
    WUP_Ajax::sync_order();
} );

/**
 * AJAX print
 */
add_action( 'wp_ajax_pacsoft_print_order', function() {
    WUP_Ajax::print_order_ajax();
} );


/**
 * Activate
 */
register_activation_hook( __FILE__, function() {
	WUP_Plugin::maybe_clear_pdfs_files_from_uploads();
} );

/**
 * Update
 */
add_action( 'upgrader_process_complete', function ( $upgrader_object, $options ) {
	WUP_Plugin::wp_update_completed( $upgrader_object, $options );
}, 10, 2 );

/**
 * Deactivate
 */
register_deactivation_hook( __FILE__, function() {
	WUP_Plugin::deactivate();
} );

add_filter( 'pre_update_option_pacsoft_services', [ "src\WUP_Plugin", "filter_services_settings" ], 10, 2 );

if ( ! function_exists('wetail_pacsoft_write_log')) {
    function wetail_pacsoft_write_log ( $log ) {
        if ( class_exists( 'WooCommerce' ) && get_option( 'pacsoft_logfile_activated' ) ) {
            $logger = wc_get_logger();
            $context = array( 'source' => 'wetail_pacsoft' );
            if ( is_array( $log ) || is_object( $log ) ) {
                $logger->debug( print_r( $log, true ), $context );
            } else {
                $logger->debug( $log, $context );
            }
        }
    }
}

add_action( 'upgrader_process_complete', 'pacsoft_track_link_migrate', 10, 2);

function pacsoft_track_link_migrate( $upgrader_object, $options ) {
    if( get_option( 'pacsoft_track_link_in_email' ) ) {
        delete_option('pacsoft_track_link_in_email' );
        update_option( 'pacsoft_track_link_on_order_status' , 'completed' );
    }

}

add_action( 'init', function() {
    // Set sequential order number
    add_action( 'woocommerce_checkout_update_order_meta', [ 'src\WUP_Plugin', "set_sequential_order_number" ], 10, 2 );
    add_action( 'woocommerce_process_shop_order_meta', [ 'src\WUP_Plugin', "set_sequential_order_number" ], 10, 2 );
    add_action( 'woocommerce_api_create_order', [ 'src\WUP_Plugin', "set_sequential_order_number" ], 10, 2 );
    add_action( 'woocommerce_deposits_create_order', [ 'src\WUP_Plugin', "set_sequential_order_number" ], 10, 2 );

    // Get sequential order number
    add_filter('woocommerce_order_number', ['src\WUP_Plugin', "get_sequential_order_number"], 10, 2);
});


WUP_KSS_processor::init();



