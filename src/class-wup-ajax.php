<?php
namespace src;

use Exception;
use src\admin\orders\WUP_Admin_Order_Processing;
use src\utils\WUP_Utils;

class WUP_Ajax {
	/**
	 * Send AJAX response
	 *
	 * @param array $data
	 */
	public static function respond( $data = [] )
	{
		$defaults = [
			'error' => false
		];
		$data = array_merge( $defaults, $data );
		
		die( json_encode( $data ) );
	}
	
	/**
	 * Send AJAX error
	 *
	 * @param string $message
	 */
	public static function error( $message ){
		self::respond( [ 'error' => true, 'message' => $message ] );
	}
	
	/**
	 * Sync order
	 */
	public static function sync_order(){
		if( empty( $_REQUEST['order_id'] ) )
			self::error( __( "Missing order ID.", WUP_Plugin::TEXTDOMAIN ) );

        $order_id = $_REQUEST['order_id'];
        $service_id = null;
		
		if( ! empty( $_REQUEST['service_id'] ) )
            $service_id = $_REQUEST['service_id'];

		try {
            WUP_Order_Controller::sync( $order_id, $service_id, true );
		}
		catch( \Exception $error ) {
			self::error( $error->getMessage() );
		}
		
		self::respond( [ 'message' => __( "Order successfully synchronised.", WUP_Plugin::TEXTDOMAIN ) ] );
	}
	
	/**
	 * Print order
	 */
	public static function print_order_ajax() {
		if( empty( $_REQUEST['order_id'] ) )
			self::error( __( "Missing order ID.", WUP_Plugin::TEXTDOMAIN ) );

        if( ! WUP_Utils::get_order_meta_compat( $_REQUEST['order_id'], '_pacsoft_order_synced' ) ){
            try {
                WUP_Order_Controller::sync( $_REQUEST['order_id'], null, true );
            }
            catch( \Exception $error ) {
                self::error( $error->getMessage() );
            }
        }

        if( in_array( get_option( 'pacsoft_account_type' ), [ WUP_Plugin::PACSOFT_XML, WUP_Plugin::UNIFAUN_XML] ) ){
            $message = WUP_Admin_Order_Processing::print_order_onlineconnect_ajax( $_REQUEST['order_id'] );
        }
        else{
            try{
                $message = WUP_Admin_Order_Processing::print_order_pdfs_apiconnect_ajax( $_REQUEST['order_id'] );
            }
            catch( Exception $error ) {
                self::error( $error->getMessage() );
            }
        }

		die( json_encode( $message ) );
	}
}

