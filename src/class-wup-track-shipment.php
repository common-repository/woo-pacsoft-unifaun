<?php

namespace src;

class WUP_Track_Shipment
{

    const UNIFAUN_REST_INTERNATIONAL_TRACKING_LINK = 'https://www.unifaunonline.com/ext.uo.se.gb.track?key=';

    private static $trace_urls = array(
        WUP_Plugin::UNIFAUN_XML => 'https://www.unifaunonline.com/ext.uo.se.se.track?key=',
        WUP_Plugin::UNIFAUN_REST => 'https://www.unifaunonline.com/ext.uo.se.se.track?key=',
        WUP_Plugin::PACSOFT_XML => 'https://www.pacsoftonline.com/ext.po.se.se.track?key=',
        WUP_Plugin::PACSOFT_REST => 'https://www.pacsoftonline.com/ext.po.se.se.track?key='
    );

    /**
     * Builds order tracking url for specified order
     *
     * @param $order \WC_Order|int order object or order id
     *
     * @return false|string tracking url
     */
    public static function get_tracking_link($order){
        if ( is_int( $order ) || is_string( $order ) ) {
            $order = wc_get_order( $order );
        }
        if ( ! $order ) {
            return false;
        }
        $countries = new \WC_Countries();

        if ( $order->get_shipping_country() != $countries->get_base_country() && in_array( get_option( 'pacsoft_account_type' ), [
                WUP_Plugin::UNIFAUN_REST,
                WUP_Plugin::UNIFAUN_XML
            ] ) ) {
            $trace_link = self::UNIFAUN_REST_INTERNATIONAL_TRACKING_LINK . get_option( 'pacsoft_usern_unif' ) . '&order=' . apply_filters( 'woocommerce_order_number', $order->get_id(), $order );
        } else {
            $trace_link = self::$trace_urls[ get_option( 'pacsoft_account_type' ) ] . get_option( 'pacsoft_usern_unif' ) . '&order=' . apply_filters( 'woocommerce_order_number', $order->get_id(), $order );
        }

        return $trace_link;
    }

    /**
     * @param \WC_Order $wc_order
     *
     * @return mixed|void
     */
    public static function is_order_virtual( $wc_order ){

        $number_of_items = count( $wc_order->get_items() );

        $number_of_virtual_items = array_filter( $wc_order->get_items(), function( $item ){
            $product = $item->get_product();

            if ( ! $product ){
                return false;
            }
            return $product->is_virtual();

        });
        return count( $number_of_virtual_items ) == $number_of_items;
    }

    public static function echo_track_link( $order ){
        $trace_link = self::get_tracking_link($order);

        if ( ! $trace_link ){
            return;
        }

        $html = '<h2>' . __( 'Track your parcel', WUP_Plugin::TEXTDOMAIN ) . '</h2>';
        $html .= '<div style="display:block;margin:-10px 0 40px;">';
        $html .= '<a href="' . $trace_link . '">' .  $trace_link . '</a>';
        $html .= '</div>';

        echo apply_filters( 'woo_pacsoft_unifaun_tŕace_link', $html, $trace_link );
    }
}
