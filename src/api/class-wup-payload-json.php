<?php

namespace src\api;

use Exception;
use src\utils\WUP_Addons;
use src\utils\WUP_Utils;
use src\WUP_Plugin;
use WC_Shipping_Zones;

if ( ! defined( 'ABSPATH' ) ) exit;

class WUP_Payload_Json extends WUP_Payload {

    /**
     * @param \WC_Order $order
     * @param $payload
     * @param $service_id
     * @return mixed
     */
    protected static function format_addons( $order, $service_id, $payload=null ){

        if( get_option( 'pacsoft_addon_sms' ) ) {

            $addon = WUP_Addons::get_addon_by_service_id_type( $service_id, 'sms_notification' );

            if( is_string( $addon ) ){

                $payload['service']['addons'] = [
                    [
                        "id"    => $addon,
                        "misc"  => apply_filters( 'woo_pacsoft_unifaun_telephone_number', $order->get_billing_phone(), $order )
                    ]
                ];
            }
            elseif ( is_array( $addon ) ){
                if( array_key_exists('addon', $addon ) && $addon['addon_key'] ){
                    $payload['service']['addons'] = [
                        [
                            "id"                        => $addon['value'],
                            $addon['phonenumber_key']   => apply_filters( 'woo_pacsoft_unifaun_telephone_number', $order->get_billing_phone(), $order ),
                            $addon['addon_key']         => $addon['addon_key_value']
                        ]
                    ];
                }
                else{
                    $payload['service']['addons'] = [
                        [
                            "id"                        => $addon['value'],
                            $addon['phonenumber_key']   => apply_filters( 'woo_pacsoft_unifaun_telephone_number', $order->get_billing_phone(), $order ),
                        ]
                    ];
                }
            }
        }

        if( get_option( 'pacsoft_addon_enot' ) ) {

            $addon = WUP_Addons::get_addon_by_service_id_type( $service_id, 'email_prenotification' );
            if( array_key_exists( 'addons', $payload['service']) && null !== $addon ){
                array_push( $payload['service']['addons'],
                    [
                        "id"    => $addon,
                        "misc"  => $order->get_billing_email()
                    ]
                );
            }
            elseif( null !== $addon ){
                $payload['service']['addons'] = [
                    [
                        "id"    => $addon,
                        "misc"  => $order->get_billing_email()
                    ]
                ];
            }

        }

        return $payload;
    }

    /**
     * @param \WC_Order $order
     * @return array
     */
    protected static function format_products( $order ){

        $products = [];
        // Container
        foreach( $order->get_items() as $item ) {
            // FIX: Skip bundled items (Mix & Match)
            if( ! isset( $item['mnm_config'] ) && isset( $item['mnm_container'] ) )
                continue;

            if ( ! is_a( $item, 'WC_Order_Item_Product' ) ){
                continue;
            }

            if( get_option( 'pacsoft_single_package_per_order' ) )
                continue;

            // Send order items in separate parcels
            $products[] = [
                "copies"        => get_option( 'pacsoft_print_freight_label_per_item' ) ? $item->get_quantity() : 1,
                "weight"        => self::get_item_weight( $item ),
                "packageCode"   => "PC",
                "contents"      => apply_filters( 'woo_pacsoft_unifaun_package_contents', get_option( 'pacsoft_default_product_type', "Varor" ), $order, $item )
            ];
        }

        // Send order in one parcel
        if( get_option( 'pacsoft_single_package_per_order' ) ) {
            $products[] = [
                "copies"        => 1,
                "weight"        => apply_filters( 'woo_pacsoft_unifaun_custom_order_weight', self::get_order_total_weight( $order ) ),
                "packageCode"   => "PC",
                "contents"      => apply_filters( 'woo_pacsoft_unifaun_package_contents', get_option( 'pacsoft_default_product_type', "Varor" ), $order, $item )
            ];
        }
        return $products;
    }

    /**
     * @param \WC_Order $order
     * @return array
     */
    protected static function format_receiver( $order ){
        $name = $order->get_shipping_first_name() ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : "{$order->get_billing_first_name()} {$order->get_billing_last_name()}";

        $address = [
            'name'          => $order->get_billing_company() ? $order->get_billing_company() : $name,
            'address1'      => $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $order->get_billing_address_1(),
            'address2'      => $order->get_shipping_address_2() ? $order->get_shipping_address_2() : $order->get_billing_address_2(),
            'zipcode'       => $order->get_shipping_postcode() ? $order->get_shipping_postcode() : $order->get_billing_postcode(),
            'city'          => $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city(),
            'country'       => $order->get_shipping_country() ? $order->get_shipping_country() : $order->get_billing_country(),
            'email'         => $order->get_billing_email(),
            'phone'         => $order->get_billing_phone(),
            'mobile'        => $order->get_billing_phone(),
            'sms'           => $order->get_billing_phone(),
            'contact'       => $name,
        ];

        if( 'US' === $order->get_shipping_country() ){
            $address['state'] = $order->get_shipping_state();
        }

        return $address;
    }

    /**
     * Generate request Json
     * @throws Exception
     * @param int $order_id
     * @param string $service_id
     * @return mixed
     */
    public static function generate( $order_id, $service_id = '' )
    {
        $order = wc_get_order( $order_id );

        $shipping = $order->get_items( 'shipping' );
        $shipping = reset( $shipping );

        if( empty( $shipping ) && empty( $service_id ) )
            throw new Exception( __( "No shipping and service ID specified for the selected order.", WUP_Plugin::TEXTDOMAIN ) );

        // Attempt to find a service ID //TODO
        if( empty( $service_id ) ) {
            $service = self::get_service( $shipping );
            $service_id = $service['service'];
            $sender_quick_value = $service['sender_quick_value'];
        }

        if( empty( $service_id ) )
            throw new Exception( __( "No service ID specified for the selected order.", WUP_Plugin::TEXTDOMAIN ) );

        if( empty( $sender_quick_value ) )
            $sender_quick_value = empty( get_option( 'pacsoft_default_sender_quick_id') ) ? 1 : get_option( 'pacsoft_default_sender_quick_id' );

        $payload = array(
            'receiver'          => self::format_receiver( $order ),
            'orderNo'           => apply_filters( 'woocommerce_order_number', $order_id, $order ),
            'senderReference'   => "OrderID: " . apply_filters( 'woocommerce_order_number', $order_id, $order ),
            'sender'            => [
                'quickId' => $sender_quick_value
            ],
            'service'           => [
                'id' => $service_id,
                'normalShipment' => true,
            ],
            'goodsDescription' => 'test'
        );

	    if ( $pacsoft_favorite_value = get_option( 'pacsoft_favorite' ) ) {
	    	$payload['favorite'] = $pacsoft_favorite_value;
	    	unset($payload['sender']);
	    }

        if( get_option( 'pacsoft_addon_enot' ) ) {
            $payload["options"] = [
                [
                    'id'   => 'ENOT',
                    'from'  => get_option( 'woocommerce_email_from_address' ),
                    'to'    => $order->get_billing_email()
                ]
            ];
        }

        $payload = self::format_addons( $order, $service_id, $payload );
        if ( get_option( 'pacsoft_print_return_labels' ) ) {
            $payload["service"]["returnShipment"] = true;
        }

        $payload["parcels"] = self::format_products( $order );


        $should_add_customs_declaration = self::should_add_customs_declaration( $order->get_id() );
        if( apply_filters( 'pacsoft_should_add_customs_declaration',  $should_add_customs_declaration, $order ) ){
            $payload['customsDeclaration'] = self::add_customs_declaration( $order );
        }

        return apply_filters( 'woo_pacsoft_unifaun_order_json_payload', $payload, $order );
    }

    /**
     * @param $payload
     * @param $order
     * @return mixed
     */
    public static function add_customs_declaration( $order ){

        $customs_declaration = [
            'printSet' => [ get_option( 'pacsoft_customs_declaration_document_number', true )],
            'importExportType' => empty( get_option( 'pacsoft_customs_import_export_type' ) ) ? 'OTHER' : get_option( 'pacsoft_customs_import_export_type' ),
            'currencyCode' => $order->get_currency()
        ];

        $lines = [];
        foreach( $order->get_items() as $item ) {
            if( ! isset( $item['mnm_config'] ) && isset( $item['mnm_container'] ) ) {
                continue;
            }

            $product = $item->get_product();

            $weight = WUP_Utils::get_product_weight( $product );
            if( 'g' == get_option( 'woocommerce_weight_unit' ) )
                $weight = $weight / 1000;

            $product_id = WUP_Utils::get_product_id( $product );
			$stat_no = get_post_meta( $product_id, 'unifaun_customs_code', true );

            $line = [
                "valuesPerItem" => true,
				"statNo" =>  $stat_no ? $stat_no : '123456',
				"copies" => $item->get_quantity(),
				"netWeight" => $weight,
				"otherUnit" => get_option( 'pacsoft_customs_other_unit' ),
				"value" => floatval( $item->get_total() )/floatval( $item->get_quantity() ),
				"contents" =>  htmlspecialchars( $product->get_name() ),
				"sourceCountryCode" => get_option( 'woocommerce_default_country' ),
            ];

            do_action( 'pacsoft_customs_declarations_line', $line );
            array_push($lines, $line );
        }

        $customs_declaration['lines'] = $lines;
        return apply_filters( 'woo_pacsoft_unifaun_order_json_customs_declaration', $customs_declaration, $order );
    }
}
