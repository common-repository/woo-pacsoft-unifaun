<?php

namespace src\api;

use Exception;
use src\WUP_Plugin;


if ( ! defined( 'ABSPATH' ) ) exit;

abstract class WUP_Payload{

    abstract static protected function format_addons( $order, $payload, $service_id );

    abstract static protected function format_products( $order );

    abstract static protected function generate( $order_id, $service_id = '' );

    /** Extracts service from Unifaun settings
     * @param \WC_Order_Item_Shipping $shipping
     * @return array|bool
     * @throws Exception
     */
    protected static function get_service( $shipping ){

        if( ! is_a( $shipping, 'WC_Order_Item_Shipping' ) ){
            $shipping = reset( $shipping );
        }

        $services = get_option( 'pacsoft_services', [] );

        if( empty( $services ) )
            throw new Exception( __( "No services defined! Check your settings.", WUP_Plugin::TEXTDOMAIN ) );

        foreach( $services as $service ) {
            // Explode shipping_method_id and get method and instance
            $service_id = explode(":", $service["shipping_method_id"] );
            if( method_exists( $shipping, 'get_instance_id' )  ){
                if( intval( $shipping->get_instance_id() ) != 0 ){
                    if( $service_id[0] == $shipping->get_method_id() && intval( $service_id[2] ) == intval( $shipping->get_instance_id() ) ) {
                        return $service;
                    }
                }
                else{
                    if( $service_id[0] == $shipping->get_method_id() ) {
                        return $service;
                    }
                }
            }
            else{
                if( $service_id[0] == $shipping->get_method_id() ) {
                    return $service;
                }
            }
        }

        return false;

    }

    /**
     * @param \WC_Order_Item_Product $item
     * @return float|int|string
     */
    protected static function get_item_weight( $item ){
        $product = $item->get_product();
        $weight = $product->get_weight();
        $weight = floatval( str_replace(",", ".", $weight) );

        if( 'g' == get_option( 'woocommerce_weight_unit' ) )
            $weight = $weight / 1000;

        $weight *= $item->get_quantity();
        return $weight;
    }

    /**
     * @param \WC_Order_Item_Product $item
     * @return float|int|string
     */
    protected static function maybe_convert_dimension( $value ){
        $value =  floatval( $value );
        if( 'cm' == get_option( 'woocommerce_dimension_unit' ) )
            $value = $value / 100;

        return $value;
    }

    /**
     * @param \WC_Order $order
     * @return float|int
     */
    public static function get_order_total_weight( $order ){
        $weight =  array_reduce(
            $order->get_items(),
            function( $carry, $item ) {

                if( ! is_a( $item, 'WC_Order_Item_Product' ) ){
                    return $carry;
                }
                $product = $item->get_product();
                $weight = floatval( $product->get_weight() ) * $item->get_quantity();
                $weight = floatval( str_replace(",", ".", $weight) );
                return $carry + $weight;
            }
        );
		//TODO use automatic converter  wc_get_weight( WC()->cart->get_cart_contents_weight(),'kg',get_option( 'woocommerce_weight_unit' ) );
        if( 'g' == get_option( 'woocommerce_weight_unit' ) )
            $weight = $weight / 1000;

        if( $default_weight = get_option( 'pacsoft_default_minimum_order_weight' ) ) {
            $default_weight = intval( $default_weight )/ 1000;

            if ( $weight < $default_weight ){
                return $default_weight;
            }
        }

        return $weight;
    }


    /**
     * Define if we need to add customs declaration to the printable media request
     *
     * @param $order_id
     *
     * @return bool
     */
    public static function should_add_customs_declaration( $order_id ){
    	if( ! get_option( 'pacsoft_send_customs_declaration' ) ) return false;
        if( ! $order = wc_get_order( $order_id ) ) return false;
        $countries = new \WC_Countries();
        if( in_array( $order->get_shipping_country(), $countries->get_european_union_countries() ) ) return false;
        if( floatval( get_option( 'pacsoft_customs_declaration_cart_threshold' ) ) > $order->get_total( 'edit' ) ) return false;
        return true;
    }

}
