<?php

namespace src\utils;

use Automattic\WooCommerce\Utilities\OrderUtil;

class WUP_Utils{


    /**
     * @param $product
     * @return float
     */
    public static function get_product_weight( $product ){
        $weight = $product->get_weight();
        return floatval( str_replace(",", ".", $weight) );
    }

    /**
     * @param $product
     * @return float
     */
    public static function get_product_id( $product ){
        if ( is_a( $product, 'WC_Product_Variation' ) ) {
            return $product->get_parent_id();
        }
        else{
            return $product->get_id();
        }
    }

    /** Fetches meta data from order. If HPOS is not available then it reads from postmeta table
     * @param $wc_order_id
     * @param $meta_key
     * @return array|mixed|string
     */
    public static function get_order_meta_compat( $wc_order_id, $meta_key ){

        if( 0 === $wc_order_id ){
            return;
        }

        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $wc_order = wc_get_order( $wc_order_id );
            if( ! $wc_order ){
                return;
            }
            return $wc_order->get_meta( $meta_key );
        } else {
            return get_post_meta( $wc_order_id, $meta_key, true );
        }
    }
    /** Deletes meta data from order. If HPOS is not available then it reads from postmeta table
     * @param $wc_order_id
     * @param $meta_key
     * @return array|mixed|string
     */
    public static function delete_order_meta_compat( $wc_order_id, $meta_key ){

        if( 0 === $wc_order_id ){
            return;
        }

        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $wc_order = wc_get_order( $wc_order_id );
            if( ! $wc_order ){
                return;
            }
            $wc_order->delete_meta_data( $meta_key );
            $wc_order->save();
        } else {
            return delete_post_meta( $wc_order_id, $meta_key );
        }
    }

}

