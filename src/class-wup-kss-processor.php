<?php

namespace src;

use src\api\WUP_HTTP_Request;
use src\api\WUP_Payload_Json;
use src\utils\WUP_Utils;

class WUP_KSS_processor {

	/**
	 * Add hooks
	 */
	public static function init() {

		add_action( 'kco_wc_process_payment', __CLASS__ . '::kco_wc_process_payment_hook_handler', 1000, 2 );
	}

	/**
	 * @param $order_id int
	 * @param $klarna_order array
	 */
	public static function kco_wc_process_payment_hook_handler( $order_id, $klarna_order = [] ) {
		if ( $prepared_shipment = WUP_Utils::get_order_meta_compat( $order_id, '_kco_kss_reference' ) ) {
			if ( WUP_Order_Controller::get_stored_shipment_id( $order_id ) ) {
				return;
			}
			$endpoint = '/rs-extapi/v1/prepared-shipments/' . $prepared_shipment . '/stored-shipments';
			$order    = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}
			$json = self::generate_stored_shipment_json( $order );

			wetail_pacsoft_write_log( "Unifaun API: creating stored shipment" );
			wetail_pacsoft_write_log( $json );

			$response = WUP_HTTP_Request::post( $endpoint, $json );

			wetail_pacsoft_write_log( 'Stored shipment response' );
			wetail_pacsoft_write_log( $response );

			WUP_Order_Controller::set_stored_shipment_id( $order_id, $response->id );

            		$order->update_meta_data( '_pacsoft_order_synced', 1 );
            		$order->save();
		} else {
			wetail_pacsoft_write_log( 'No kss ref in ' . $order_id );
		}
	}

	/**
	 * @param $order \WC_Order
	 *
	 * @return array
	 */
	private static function generate_stored_shipment_json( $order ) {
		$total_weight = WUP_Payload_Json::get_order_total_weight( $order ) ;
		$json         = [
			'sender'  => self::format_json_sender(),
			'parcels' => [
				[
					'copies'         => '1',
					'weight'         => "$total_weight",
					"contents"       => "important things", //proper value?
					"valuePerParcel" => true,
				],
			],
		];

		//extract to settings?
		//if ( get_option( WTUC_SLUG .'_print_return_labels' ) ) {
		//        $json['service'] = array(
		//            'id'                => $order->get_meta('unifaun_delivery_checkout_shipping_service_id'),
		//            'returnShipment'    => 1,
		//            'normalShipment'    => 1,
		//        );
		//}

		$json = self::format_json_addons( $order, $json );

		return apply_filters( 'unifaun_checkout_stored_shipment_payload', $json );
	}

	/**
	 * @param  $order \WC_Order
	 * @param  $not_json array
	 *
	 * @return array
	 */
	private static function format_json_addons( $order, $not_json ) {
		// Pre-notification by e-mail
		if ( get_option( 'pacsoft_addon_enot' ) ) {
			$not_json['options'] = [
				[
					'id'   => 'ENOT',
					'from' => get_option( 'admin_email' ),
					'to'   => $order->get_billing_email(),
				],
			];
		}

		// Addons
		if ( get_option( 'pacsoft_addon_sms' ) ) {
			$not_json['service']['addons'] = [
				[
					'id'   => 'NOTSMS',
					'misc' => '' . str_replace( " ", "", $order->get_billing_phone() ),
				],
				[
					'id'   => 'NOT',
					'misc' => '' . str_replace( " ", "", $order->get_billing_phone() ),
				],
				[
					'id'    => 'PRENOT',
					'text3' => '' . str_replace( " ", "", $order->get_billing_phone() ),
				],
			];

		}

		return $not_json;
	}

	/**
	 * Generates sender info
	 *
	 * @return array
	 */
	private static function format_json_sender() {
		$sender_quick_value = get_option( 'pacsoft_default_sender_quick_id' );
		if ( empty( $sender_quick_value ) ) {
			$sender_quick_value = 1;
		}

		return [
			'quickId' => "$sender_quick_value",
		];
	}
}
