<?php

namespace src\admin\orders;

use src\admin\WUP_Settings;
use src\utils\WUP_Utils;
use src\WUP_Plugin;
use WC_Order;
use Exception;

class WUP_Admin_Template_Table {
	/**
	 * Modify table
	 */
	static public function modify(){
		// Add ThickBox
		add_thickbox();
		
		// Add templates

		
		// Modify columns
		add_filter( 'manage_shop_order_posts_columns', [ __CLASS__, 'add_columns' ] );
		
		// Get column content
		add_filter( 'manage_posts_custom_column', [ __CLASS__, 'get_column_content' ], 10, 2 );
	}
	
	/**
	 * Add templates to <head>
	 */
	static public function add_templates(){
		$data = [
			'i18n' => [
				'selectPacsoftService' => __( "Select Pacsoft/Unifaun service", WUP_Plugin::TEXTDOMAIN ),
				'syncOrder' => __( "Send order", WUP_Plugin::TEXTDOMAIN ),
				'selectedServiceIndicator' => __( "Choosen service:", WUP_Plugin::TEXTDOMAIN ),
				'noServiceSelected' => __( "No service selected, please select one in the list below.", WUP_Plugin::TEXTDOMAIN ),
				'placeHolderSearchServices' => __( "Search services", WUP_Plugin::TEXTDOMAIN )
			],
			'services' => WUP_Settings::get_services()
		];
		$i18n = [
			'Sync order %d to Pacsoft/Unifaun' => __( "Sync order %d to Pacsoft/Unifaun", WUP_Plugin::TEXTDOMAIN ),
			'Print Pacsoft/Unifaun order' => __( "Print Pacsoft/Unifaun order", WUP_Plugin::TEXTDOMAIN ),
				'No service selected, please select one in the list below.' => 
			__( "No service selected, please select one in the list below.", WUP_Plugin::TEXTDOMAIN )
		];
		$mustache = WUP_Settings::get_mustache();

        print '<script>window.pacsoftSyncOptionsDialog=\'' . preg_replace( "/\r|\n/", "",$mustache->render( 'admin/table/pacsoft-sync-options', $data ) ) . '\';pacsoftI18n=' . json_encode( $i18n ) . '</script>';	}
	
	/**
	 * Add columns
	 *
	 * @param array $columns
	 */
	static public function add_columns( $columns = [] ) 
	{
		$columns['pacsoft_order'] = __( "Pacsoft/Unifaun", WUP_Plugin::TEXTDOMAIN );
		return $columns;
	}
	
	/**
	 * Get column content
	 *
	 * @param $column
	 * @param $order_id
	 */
	static public function get_column_content( $column, $order_id )
	{
        if( 'pacsoft_order' != $column )
            return;

        if ( !is_int( $order_id ) && 'Automattic\WooCommerce\Admin\Overrides\Order' === get_class( $order_id ) )  {
            $order_id = $order_id->get_id();
        }

        $mustache = WUP_Settings::get_mustache();
        $data = [
            'syncButton' => [
                'href' => "#",
                'title' => __( "Sync order to Pacsoft/Unifaun", WUP_Plugin::TEXTDOMAIN )
            ],
            'printButton' => [
                'href' => "#",
                'title' => __( "Print Pacsoft/Unifaun order", WUP_Plugin::TEXTDOMAIN )
            ],
            'orderId' => $order_id,
            'serviceId' => ( get_option( 'pacsoft_sync_with_options' ) ? '' : self::get_order_service( $order_id ) ),
            'isSynced' => WUP_Utils::get_order_meta_compat( $order_id, '_pacsoft_order_synced' ),
            'isKSS' => ! empty( WUP_Utils::get_order_meta_compat( $order_id, '_kco_kss_reference' ) ),
        ];
        print $mustache->render( 'admin/table/pacsoft-column', $data );
	}

    /**
     * Get order service ID
     *
     * @param int $order_id
     * @return bool
     */
	public static function get_order_service( $order_id )
	{
		$order = wc_get_order( $order_id );
		$services = get_option( 'pacsoft_services' );
		$shipping = $order->get_items( 'shipping' );
		$shipping = reset( $shipping );
		
		if( empty( $services ) )
			//throw new Exception( __( "No services defined! Check your settings.", WUP_Plugin::TEXTDOMAIN ) );
			return false;
		
		foreach( $services as $service ){
            if( isset( $service['shipping_method_id'] ) && $service['shipping_method_id'] == $shipping['method_id'] ){
                return $service['service'];
            }
        }

        return false;
	}
}
