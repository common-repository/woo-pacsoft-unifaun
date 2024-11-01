<?php

namespace src\api;

use DOMDocument;
use Exception;
use SimpleXMLElement;
use src\utils\WUP_Addons;
use src\WUP_Plugin;
use WC_Countries;
use WC_Order;
use WC_Shipping_Zones;

if ( ! defined( 'ABSPATH' ) ) exit;

class WUP_Payload_XML extends WUP_Payload{

    /**
     * Generate request XML
     *
     * @param int $order_id
     * @param string $service_id
     * @return SimpleXMLElement
     * @throws Exception
     */
    public static function generate( $order_id, $service_id = '' ){
        $order = wc_get_order( $order_id );
        $xml = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><data></data>' );

        $shipping = $order->get_items( 'shipping' );

        if( empty( $shipping ) && empty( $service_id ) )
            throw new Exception( __( "No shipping and service ID specified for the selected order.", WUP_Plugin::TEXTDOMAIN ) );

        $sender_quick_value = false;
        // Attempt to find a service ID //TODO
        if( empty( $service_id ) ) {
            $service = self::get_service( $shipping );
            $service_id = $service['service'];
            $sender_quick_value = $service['sender_quick_value'];
        }

        if( empty( $service_id ) )
            throw new Exception( __( "No service ID specified for the selected order.", WUP_Plugin::TEXTDOMAIN ) );

        // Test mode
        if( get_option( 'pacsoft_test_mode' ) ) {
            $meta = $xml->addChild( 'meta' );
            $val = $meta->addChild( 'val', "YES" );
            $val->addAttribute( 'n', "TEST" );
        }

        self::format_receiver( $xml, $order );

        if ( $pacsoft_favorite_value = get_option( 'pacsoft_favorite' ) ) {
            $meta = $xml->addChild( 'meta' );
            $val = $meta->addChild( 'val', $pacsoft_favorite_value );
            $val->addAttribute( 'n', "favorite" );
        }

        self::format_shipment( $xml, $order_id, $order, $service_id, $sender_quick_value );

        $doc = new DOMDocument( '1.0' );
        $doc->formatOutput = true;
        $docXML = $doc->importNode( dom_import_simplexml( $xml ), true );
        $docXML = $doc->appendChild( $docXML );

        return $doc->saveXML( $doc, LIBXML_NOEMPTYTAG );
    }

    /**
     * @param WC_Order $order
     * @param SimpleXMLElement $payload
     * @param $service_id
     */
    protected static function format_addons( $order, $payload, $service_id ){

        if( get_option( 'pacsoft_addon_notemail' ) ) {
            $addon = $payload->addChild('addon');
            $addon->addAttribute('adnid', "NOTEMAIL");

            $val = $addon->addChild('val', $order->get_billing_email());
            $val->addAttribute('n', "misc");

        }

        if( get_option( 'pacsoft_addon_sms' ) ) {

            $service_addon = WUP_Addons::get_addon_by_service_id_type( $service_id, 'sms_notification' );

            if( is_string( $service_addon ) ){

                $addon = $payload->addChild('addon' );
                $addon->addAttribute('adnid', $service_addon );

                $val = $addon->addChild('val', apply_filters( 'woo_pacsoft_unifaun_telephone_number', $order->get_billing_phone(), $order ) );
                $val->addAttribute('n', "misc" );

            }
            elseif ( is_array( $service_addon ) ){

                $addon = $payload->addChild('addon');
                $addon->addAttribute('adnid', $service_addon['value'] );

                $val = $addon->addChild('val', apply_filters( 'woo_pacsoft_unifaun_telephone_number', $order->get_billing_phone(), $order ) );
                $val->addAttribute('n', $service_addon['phonenumber_key'] );

                if( array_key_exists('addon', $service_addon ) && $service_addon['addon_key'] ){
                    $val = $addon->addChild('val', $service_addon['addon_key_value'] );
                    $val->addAttribute('n', $service_addon['addon_key'] );
                }

            }

        }

        if( get_option( 'pacsoft_addon_enot' ) ) {
            $service_addon = WUP_Addons::get_addon_by_service_id_type( $service_id, 'email_prenotification' );

            if( is_string( $service_addon ) ){

                $addon = $payload->addChild('addon');
                $addon->addAttribute('adnid', $service_addon );

                $val = $addon->addChild('val', $order->get_billing_email());
                $val->addAttribute('n', "misc");

            }

        }

        // MTDPLP EXTCOV (leverans i utomgeografi för MTD Postlådepaket)
        if ( $service_id == 'MTDPLP') {
            $addon = $payload->addChild( 'addon' );
            $addon->addAttribute( 'adnid', 'EXTCOV' );
        }

        do_action('woo_pacsoft_unifaun_order_xml_payload' , $payload );
    }

    /**
     * @param WC_Order $order
     * @param SimpleXMLElement $shipment
     */
    protected static function format_products( $order, $shipment=null ){

        foreach( $order->get_items() as $item ) {
            // FIX: Skip bundled items (Mix & Match)
            if( ! isset( $item['mnm_config'] ) && isset( $item['mnm_container'] ) ) {
                continue;
            }

            if ( ! is_a( $item, 'WC_Order_Item_Product' ) ){
                continue;
            }

            if( get_option( 'pacsoft_single_package_per_order' ) )
                continue;

            // Send order items in separate parcels
            $container = $shipment->addChild( 'container' );
            $container->addAttribute( 'type', "parcel" );

            $val = $container->addChild( 'val', get_option( 'pacsoft_print_freight_label_per_item' ) ? $item->get_quantity() : 1 );
            $val->addAttribute( 'n', "copies" );

            $val = $container->addChild( 'val', self::get_item_weight( $item ) );
            $val->addAttribute( 'n', "weight" );

            $val = $container->addChild( 'val', 'KG' );
            $val->addAttribute( 'n', "units" );

            $val = $container->addChild( 'val', "PC" );
            $val->addAttribute( 'n', "packagecode" );

            $val = $container->addChild( 'val', apply_filters( 'woo_pacsoft_unifaun_package_contents', get_option( 'pacsoft_default_product_type', "Varor" ), $order, $item ) );
            $val->addAttribute( 'n', "contents" );

            $val = $container->addChild( 'val', apply_filters( 'woo_pacsoft_unifaun_package_contents', get_option( 'pacsoft_default_product_type', "Varor" ), $order, $item ) );
            $val->addAttribute( 'n', "goodsdescription" );
        }

        // Send order in one parcel
        if( apply_filters( 'woo_pacsoft_unifaun_single_package_per_order', get_option( 'pacsoft_single_package_per_order' ), $order ) ) {
            $container = $shipment->addChild( 'container' );
            $container->addAttribute( 'type', "parcel" );

            $val = $container->addChild( 'val', 1 );
            $val->addAttribute( 'n', "copies" );

            $val = $container->addChild( 'val', apply_filters( 'woo_pacsoft_unifaun_custom_order_weight', self::get_order_total_weight( $order ) ) );
            $val->addAttribute( 'n', "weight" );

            $val = $container->addChild( 'val', "PC" );
            $val->addAttribute( 'n', "packagecode" );

            $val = $container->addChild( 'val', apply_filters( 'woo_pacsoft_unifaun_package_contents', get_option( 'pacsoft_default_product_type', "Varor" ), $order, $item ) );
            $val->addAttribute( 'n', "contents" );
        }
    }

    /** Formats receiver XML
     * @param SimpleXMLElement $xml
     * @param int $order_id
     * @param WC_Order $order
     * @param int $service_id
     * @param int $sender_quick_value
     */
    public static function format_shipment( $xml, $order_id, $order, $service_id, $sender_quick_value ){
        $shipment = $xml->addChild( 'shipment' );
        $shipment->addAttribute( 'orderno', apply_filters( 'woocommerce_order_number', $order_id, $order ) );

        if( empty( $sender_quick_value ) )
            $sender_quick_value = empty( get_option( 'pacsoft_default_sender_quick_id') ) ? 1 : get_option( 'pacsoft_default_sender_quick_id' );


	    if ( ! get_option( 'pacsoft_favorite' ) ) {
            $val = $shipment->addChild('val', $sender_quick_value);
            $val->addAttribute('n', "from");
        }

        $val = $shipment->addChild( 'val', "6565" );
        $val->addAttribute( 'n', "to" );

        $val = $shipment->addChild( 'val', "OrderID: " . apply_filters( 'woocommerce_order_number', $order_id, $order ) );
        $val->addAttribute( 'n', "reference" );

        if( get_option( 'pacsoft_addon_enot' ) ) {
            $ufonline = $shipment->addChild( 'ufonline' );

            $option = $ufonline->addChild( 'option' );
            $option->addAttribute( 'optid', "enot" );

            $val = $option->addChild( 'val', get_option( 'admin_email' ) );
            $val->addAttribute( 'n', "from" );

            $val = $option->addChild( 'val', $order->get_billing_email() );
            $val->addAttribute( 'n', "to" );

            $val = $option->addChild( 'val', get_option( 'admin_email' ) );
            $val->addAttribute( 'n', "errorto" );
        }

        // Service
        $service = $shipment->addChild( 'service' );
        $service->addAttribute( 'srvid', $service_id );

        // Return labels
        if( get_option( 'pacsoft_print_return_labels' ) ) {
            $return_label = $service->addChild('val', 'both');
            $return_label->addAttribute('n', 'returnlabel');
        }

        self::format_addons( $order, $service, $service_id );
        self::format_products( $order, $shipment );

        $countries = new WC_Countries();
        $should_add_customs_declaration = ( get_option( 'pacsoft_send_customs_declaration' )
            && ! in_array( $order->get_shipping_country(), $countries->get_european_union_countries() )
            && floatval( get_option( 'pacsoft_customs_declaration_cart_threshold' ) ) <= $order->get_total() );

        wetail_pacsoft_write_log( "Should add customs declaration " . $should_add_customs_declaration );
        if( apply_filters( 'pacsoft_should_add_customs_declaration',  $should_add_customs_declaration, $order ) ){
            self::add_customs_declaration( $shipment, $order );
        }
    }

    /** Formats receiver
     * @param SimpleXMLElement $xml
     * @param WC_Order $order
     */
    public static function format_receiver( $xml, $order ){

        $name               = $order->get_shipping_first_name() ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : "{$order->get_billing_first_name()} {$order->get_billing_last_name()}";
        $billing_company    = $order->get_billing_company();
        $address            = $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $order->get_billing_address_1();
        $address2           = $order->get_shipping_address_2() ? $order->get_shipping_address_2() : $order->get_billing_address_2();
        $zipcode            = $order->get_shipping_postcode() ? $order->get_shipping_postcode() : $order->get_billing_postcode();
        $city               = $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city();
        $country            = $order->get_shipping_country() ? $order->get_shipping_country() : $order->get_billing_country();

        $receiver = $xml->addChild( 'receiver' );
        $receiver->addAttribute( 'rcvid', "6565" );

        // Name
        $val = $receiver->addChild( 'val', $billing_company ? htmlspecialchars( $billing_company ): htmlspecialchars( $name ) );
        $val->addAttribute( 'n', "name" );

        $val = $receiver->addChild( 'val', htmlspecialchars( $address ) );
        $val->addAttribute( 'n', "address1" );

        $val = $receiver->addChild( 'val', htmlspecialchars( $address2 ) );
        $val->addAttribute( 'n', "address2" );

        if ( $order->get_shipping_country() == 'US' ){
            $val = $receiver->addChild( 'val', $order->get_shipping_state() );
            $val->addAttribute( 'n', "state" );
        }

        $val = $receiver->addChild( 'val', $zipcode );
        $val->addAttribute( 'n', "zipcode" );

        $val = $receiver->addChild( 'val', htmlspecialchars( $city ) );
        $val->addAttribute( 'n', "city" );

        $val = $receiver->addChild( 'val', $country );
        $val->addAttribute( 'n', "country" );

        $val = $receiver->addChild( 'val', $order->get_billing_email() );
        $val->addAttribute( 'n', "email" );

        $val = $receiver->addChild( 'val', $order->get_billing_phone() );
        $val->addAttribute( 'n', "phone" );

        $val = $receiver->addChild( 'val', $order->get_billing_phone() );
        $val->addAttribute( 'n', "sms" );

        $val = $receiver->addChild( 'val', $name );
        $val->addAttribute( 'n', "contact" );
    }

    /** Adds Customs Declaration
     * @param SimpleXMLElement $shipment
     * @param \WC_Order $order
     */
    public static function add_customs_declaration( &$shipment, $order ){

        $customs_declaration = $shipment->addChild( 'customsdeclaration' );
        $customs_declaration->addAttribute( 'documents', get_option( 'pacsoft_customs_declaration_document_number', true ) );

        $val = $customs_declaration->addChild( 'val',  empty( get_option( 'pacsoft_customs_import_export_type' ) ) ? 'OTHER' : get_option( 'pacsoft_customs_import_export_type' ) );
        $val->addAttribute( 'n', "impexptype" );

        $val = $customs_declaration->addChild( 'val', $order->get_currency() );
        $val->addAttribute( 'n', "customsunit" );

        foreach( $order->get_items() as $item ) {
            // FIX: Skip bundled items (Mix & Match)
            if( ! isset( $item['mnm_config'] ) && isset( $item['mnm_container'] ) ) {
                continue;
            }

            $product = $item->get_product();
            $weight = $product->get_weight();
            $weight = floatval( str_replace(",", ".", $weight) );
            if( 'g' == get_option( 'woocommerce_weight_unit' ) )
                $weight = $weight / 1000;

            if ( is_a( $product, 'WC_Product_Variation' ) ) {
                $product_id = $product->get_parent_id();
            }
            else{
                $product_id = $product->get_id();
            }

            // Send order items in separate parcels
            $line = $customs_declaration->addChild( 'line' );

            $val = $line->addChild( 'val', $item->get_quantity() );
            $val->addAttribute( 'n', "units" );

            $val = $line->addChild( 'val', $item->get_quantity() );
            $val->addAttribute( 'n', "quantity" );

            $val = $line->addChild( 'val', get_post_meta( $product_id, 'unifaun_customs_code', true ) );
            $val->addAttribute( 'n', "statno" );

            $val = $line->addChild( 'val', get_option( 'woocommerce_default_country' ) );
            $val->addAttribute( 'n', "sourcecountry" );

            $val = $line->addChild( 'val', $weight );
            $val->addAttribute( 'n', "netweight" );

            $val = $line->addChild( 'val', floatval($item->get_total() )/floatval($item->get_quantity() ) );
            $val->addAttribute( 'n', "customsvalue" );

            $val = $line->addChild( 'val', htmlspecialchars( $product->get_name() ) );
            $val->addAttribute( 'n', "contents" );

            do_action( 'pacsoft_customs_declarations_line', $line );
        }
        do_action( 'pacsoft_customs_declarations_document', $customs_declaration );
    }
}
