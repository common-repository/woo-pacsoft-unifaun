<?php

namespace src;

use src\admin\orders\WUP_Admin_Order_Processing;
use src\admin\orders\WUP_Admin_Template_Table;
use src\admin\views\WUP_Customs_Settings;
use src\admin\views\WUP_General_Settings;
use src\admin\views\WUP_Order_Settings;
use src\admin\WUP_Admin_Template;
use src\admin\WUP_Settings;
use src\fortnox\WF_Utils;
use src\utils\WUP_Utils;
use WC_Countries;

if ( ! defined( 'ABSPATH' ) ) exit;

class WUP_Plugin
{
	const TEXTDOMAIN = 'woo-pacsoft-unifaun';
	const PACSOFT_XML = 'po_SE';
	const UNIFAUN_XML = 'ufo_SE';
	const UNIFAUN_REST = 'unifaun_rest';
	const PACSOFT_REST = 'pacsoft_rest';
	const UNIFAUN_REST_INTERNATIONAL_TRACKING_LINK = 'https://www.unifaunonline.com/ext.uo.se.gb.track?key=';

    private static $trace_urls = array(
        self::UNIFAUN_XML => 'https://www.unifaunonline.com/ext.uo.se.se.track?key=',
        self::UNIFAUN_REST => 'https://www.unifaunonline.com/ext.uo.se.se.track?key=',
        self::PACSOFT_XML => 'https://www.pacsoftonline.com/ext.po.se.se.track?key=',
        self::PACSOFT_REST => 'https://www.pacsoftonline.com/ext.po.se.se.track?key='
    );

	/**
	 * Activate
	 *
	 * @since 2.90 we removing any files under freight_labels under uploads folders
	 *        See https://www.wrike.com/open.htm?id=850553109
	 */

	public static function maybe_clear_pdfs_files_from_uploads() {
		$hp = trailingslashit( wp_upload_dir()['basedir'] ) . 'freight_labels/';
		if ( is_dir( $hp ) ) {
			array_map( 'unlink', glob( "$hp/*.*" ) );
			rmdir( $hp );
		}
	}
	public static function wp_update_completed( $upgrader_object, $options ) {

		if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			foreach( $options['plugins'] as $plugin ) {
				if( $plugin == 'woo-pacsoft-unifaun/plugin.php' ) {
					self::maybe_clear_pdfs_files_from_uploads();
				}
			}
		}
	}


    /**
     * Set sequential order
     *
     * @param $order_id
     * @param $post
     */
    public static function set_sequential_order_number( $order_id, $post ){
        if( ! get_option( 'pacsoft_order_number_prefix' ) )
            return;

        if( is_array( $post ) || is_null( $post ) || ( 'shop_order' === $post->post_type && 'auto-draft' !== $post->post_status ) ) {
            $order_id = is_a( $order_id, "WC_Order" ) ? $order_id->get_id() : $order_id;
            $order_number = WUP_Utils::get_order_meta_compat( $order_id, '_order_number' );
            $wc_order = wc_get_order( $order_id );
            $wc_order->update_meta_data( '_order_number', get_option( 'pacsoft_order_number_prefix' ) . $order_number );
            $wc_order->save();
        }
    }

    /**
     * Get sequential order number
     *
     * @param $order_number
     * @param \WC_Order $order
     * @return mixed|string
     */
    public static function get_sequential_order_number( $order_number, $order )
    {
        if( $order instanceof \WC_Subscription ){
            return $order_number;
        }

        if( WUP_Utils::get_order_meta_compat( $order->get_id(), '_order_number_formatted' ) ){
            return WUP_Utils::get_order_meta_compat( $order->get_id(), '_order_number_formatted' );
        }

        if( ! get_option( 'pacsoft_order_number_prefix' ) ){
            return $order_number;
        }


        return get_option( 'pacsoft_order_number_prefix' ) . $order_number;
    }

    /**
	 * Deactivate
	 */
	public static function deactivate() {}

    /**
     * Get plugin path
     *
     * @param string $path
     * @return string
     */
	public static function get_path( $path = '' ){
		return plugin_dir_path( dirname(  __FILE__ ) ) . ltrim( $path, '/' );
	}

    /**
     * Get plugin URL
     *
     * @param string $path
     * @return string
     */
	public static function get_url( $path = '' ){
		return plugins_url( $path, dirname( __FILE__ ) );
	}

	/**
	 * Admin post table view
	 */
	public static function on_post_edit_view(){
		if ( isset( $_REQUEST['post_type'] ) && "shop_order" == $_REQUEST['post_type'] ) {
			WUP_Admin_Template_Table::modify();
			WUP_Admin_Order_Processing::init();
		}

	}

	/**
	 * Add settings
	 *
	 * @hook action 'admin_init'
	 */
	public static function add_settings(){
        WUP_General_Settings::init_settings();
        WUP_Order_Settings::init_settings();
        WUP_Customs_Settings::init_settings();
	}

	/**
	 * Filter services settings before we save it
	 *
	 * @param $new
	 * @param $old
     * @return array
	 */
	public static function filter_services_settings( $new, $old = [] ){
		$settings = [];
		$services = WUP_Settings::get_services();

		if( ! isset( $new['shipping_method_id'] ) )
			return $new;

		foreach( $new as $setting => $values ) {
			foreach( $values as $x => $value ) {
				if( ! isset( $settings[ $x ] ) )
					$settings[ $x ] = [];

				$settings[ $x ][ $setting ] = $value;

				if( 'service' == $setting ) {
					foreach( $services as $service ) {
						if( $service['code'] == $value ) {
							$settings[ $x ]['from'] = $service['from'];
							$settings[ $x ]['to'] = $service['to'];
						}
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Add settings page
	 *
	 * @hook 'admin_menu'
	 */
	public static function add_settings_page()
	{
		$page = WUP_Admin_Template::add_page( [
			'slug' => "woocommerce-pacsoft",
			'title' => __( "WooCommerce Pacsoft/Unifaun integration", self::TEXTDOMAIN ),
			'menu' => __( "Pacsoft/Unifaun", self::TEXTDOMAIN )
		] );
	}

	/**
	 * Add scripts
	 */
	public static function add_admin_scripts()
	{
		wp_register_script( 'pacsoft-chosen', self::get_url( 'assets/scripts/chosen.jquery.min.js' ), [ 'jquery' ], 1 );
		wp_register_script( 'pacsoft', self::get_url( 'assets/scripts/admin.js' ), [ 'jquery', 'mustache', 'pacsoft-chosen' ], WOOCOMMERCE_PACSOFT_UNIFAUN_VERSION );

		$mustache = WUP_Settings::get_mustache();

		wp_localize_script( 'pacsoft', 'pacsoft', [
			'row' => $mustache->render( 'admin/settings/table-row', [
				'columns' => [
					[
						'column' => [
							'name' => 'shipping_method_id',
							'content' => WUP_Settings::get_shipping_methods_dropdown()
						]
					],
					[
						'column' => [
							'name' => 'service',
							'content' => WUP_Settings::get_services_dropdown()
						]
					],
					[
						'column' => [
							'name' => 'sender-quick-value',
							'content' => '<input type="text" name="pacsoft_services[sender_quick_value][]" value="" placeholder="1">  <a href="#" class="dashicons dashicons-dismiss removeRow"></a>'
						]
					]
				]
			] ),
            'notice'                            => $mustache->getLoader()->load( 'admin/notice' ),
            'choosen_base_country'              => get_option('pacsoft_base_country'),
            'pacsoft_account_type'              => get_option('pacsoft_account_type'),
            'pacsoft_username'                  => get_option('pacsoft_usern_unif'),
            'pacsoft_password'                  => get_option('pacsoft_pass_unif'),
            'pacsoft_api_id'                    => get_option('pacsoft_api_id'),
            'pacsoft_api_secret_id'             => get_option('pacsoft_api_secret_id'),
            'pacsoft_send_customs_declaration'  => get_option('pacsoft_send_customs_declaration'),
		] );

		wp_enqueue_script( 'pacsoft-chosen' );
		wp_enqueue_script( 'mustache', self::get_url( 'assets/scripts/mustache.js' ) );
		wp_enqueue_script( 'pacsoft', $ver=WOOCOMMERCE_PACSOFT_UNIFAUN_VERSION );
		wp_enqueue_style( 'pacsoft', self::get_url( 'assets/styles/admin.css' ) );
		wp_enqueue_style( 'pacsoft-chosen', self::get_url( 'assets/styles/chosen.min.css' ) );
               wp_enqueue_script( 'jquery-tiptip'  );
        	wp_enqueue_script('thickbox');
        	wp_enqueue_style('thickbox');
	}

	/**
	 * Get translated strings (i18n)
	 */
	public static function get_translated_strings(){
		$strings = [
			'Save changes' => __( "Save changes", self::TEXTDOMAIN ),
		];

		return $strings;
	}

    /**
     * Add meta boxes to Edit Product and Order views
     */
    public static function add_meta_boxes(){
        add_meta_box(
            'unifaun_product_meta_box',
            __( "Unifaun Customs Code", self::TEXTDOMAIN ),
            [ __CLASS__, 'render_product_meta_box' ],
            "product",
            "side",
            "high"
        );

    }

    /**
     * Render Product meta box
     */
    public static function render_product_meta_box(){
        global $post;
        print '<p><label><input type="text" name="unifaun_customs_code" value="' . get_post_meta( $post->ID, 'unifaun_customs_code', true ) . '"> ' . __( 'Taricnummer. Ett 8 siffrors nummer som fås av tullen.' ) . '</label></p>';
    }

    /** Save customs code
     * @param $post_id
     */
    public static function save_unifaun_customs_code( $post_id ){
        if( isset( $_POST['unifaun_customs_code'] ) ){
            if( get_post_meta( $post_id, 'unifaun_customs_code', FALSE ) ) {
                update_post_meta( $post_id, 'unifaun_customs_code', $_POST['unifaun_customs_code'] );
            } else {
                add_post_meta( $post_id, 'unifaun_customs_code', $_POST['unifaun_customs_code'] );
            }
        }
    }
}
