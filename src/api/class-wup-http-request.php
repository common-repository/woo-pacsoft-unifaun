<?php

namespace src\api;

require( WOOCOMMERCE_PACSOFT_UNIFAUN_PLUGIN_DIR . 'vendor/autoload.php' );

use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class WUP_HTTP_Request {

	const REST_BASE_URL = 'https://api.unifaun.com/';
	const XML_BASE_URL = 'https://www.unifaunonline.se/ufoweb/';

	/**
	 * @param $endpoint
	 * @param array $params
	 *
	 * @return array | mixed
	 * @throws Exception
	 */
	public static function get( $endpoint, $params=[] ) {

		$endpoint .= '?' . http_build_query( $params );

		wetail_pacsoft_write_log( "GET REQUEST " );
		wetail_pacsoft_write_log( $endpoint );

		$response = wp_remote_get( self::REST_BASE_URL . $endpoint );

		if( is_a( $response, 'WP_Error' ) ){
			throw new Exception( $response->get_error_message() );
		}

		$code = (int)$response['response']['code'];
		if ( $code >= 400 ) {
			throw new Exception( 'Request failed ' . $code . ': ' . $response['response']['message'], 1221 );
		}

        return json_decode( $response['body'] );
	}

    /**
     * @return array
     */
	private static function get_auth() {
        return array(
            'auth' => [
                get_option( 'pacsoft_api_id' ),
                get_option( 'pacsoft_api_secret_id' ),
                'digest'
            ]
        );
    }

	/**
	 * Post XML
	 *
	 * @param string $xml
	 * @param bool $return_response
	 *
	 * @throws Exception
	 *
	 * @return array | mixed
	 */
	public static function post_xml( $xml, $return_response = false ) {

        $endpoint = 'order?session=' . get_option( 'pacsoft_account_type' )
            . '&user=' . get_option( 'pacsoft_usern_unif' )
            . '&pin=' . get_option( 'pacsoft_pass_unif' )
            . '&type=XML&developerid=0020012792';

		$args = [
			'headers'       => [
				'Content-Type' => 'text/xml; charset=UTF8'
			],
			'body'          => $xml,
			'method'        => 'POST',
			'data_format'   => 'body',
			'timeout'       => 10
		];

        wetail_pacsoft_write_log( "XML Payload " );
        wetail_pacsoft_write_log( $xml );
        try{
	        $response = wp_remote_post( self::XML_BASE_URL . $endpoint, $args );

	        if( is_a( $response, 'WP_Error' ) ){
		        throw new Exception( $response->get_error_message() );
	        }

            wetail_pacsoft_write_log( $response );

            if ( $return_response )
            	return $xml;
        } catch ( Exception $e ){
            if( (int) $e->getCode() >= 400 ){
                if( $e->getCode() == 401 ){
                    throw new Exception( 'Felaktigt användarnamn eller lösenord. Vänligen kontrollera dem <a href="' . get_admin_url() . '/options-general.php?page=woocommerce-pacsoft">här</a>', 401 );
                }
                throw new Exception( 'Något gick fel vid anrop till Unifaun/Pacsoft. Har du angett rätt användarnamn eller lösenord? Vänligen kontrollera dem <a href="' . get_admin_url() . '/options-general.php?page=woocommerce-pacsoft">här</a>' , $e->getCode() );
            }
        }

        return true;
    }

    /**
     * Post JSON
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return array | mixed
     *
     * @throws Exception
     */
	public static function post( $endpoint, $params) {

        wetail_pacsoft_write_log( "POST REQUEST " );
        wetail_pacsoft_write_log( $endpoint );
        wetail_pacsoft_write_log( $params );

		$args = [
			'headers'       => [
				'Authorization' => 'Bearer ' . get_option( 'pacsoft_api_id' ) . '-' . get_option( 'pacsoft_api_secret_id' ),
				'Content-Type'  => 'application/json'
			],
			'body'          => wp_json_encode( $params ),
			'method'        => 'POST',
			'data_format'   => 'body',
			'timeout'       => 10
		];

		$response = wp_remote_post( self::REST_BASE_URL . $endpoint, $args );

		if( is_a( $response, 'WP_Error' ) ){
			throw new Exception( $response->get_error_message() );
		}


        wetail_pacsoft_write_log( "RESPONSE" );
        wetail_pacsoft_write_log( $response );

        $code = (int)$response['response']['code']; //https://developer.wordpress.org/reference/functions/wp_remote_retrieve_response_code/#source
        if( ! $code ) $code = 404;

        if( $code >= 400 ){
            if( $code === 401 ){
                throw new Exception( 'Invalid credentials, please check your username and password' );
            }
			throw new Exception( 'Request failed ' . $code . ': ' . $response['response']['message'] );
		}elseif( $code === 204 ){
	        throw new Exception( 'Ditt konto är felkonfigurerat, vänligen kontakta Unifaun/Pacsoft' );
        }

		return json_decode( $response['body'] );
	}

    /**
     * @param string $endpoint
     * @param array $params
     * @return array | mixed
     * @throws Exception
     */
	public static function put( $endpoint, $params ) {

		$args = [
			'headers'       => array_merge( [ 'Content-Type'  => 'application/json' ], self::get_auth() ),
			'body'          => wp_json_encode( $params ),
			'method'        => 'PUT',
			'data_format'   => 'body',
			'timeout'       => 10
		];

		$response = wp_remote_request( self::REST_BASE_URL . $endpoint, $args );

		if( is_a( $response, 'WP_Error' ) ){
			throw new Exception( $response->get_error_message() );
		}

        $code = (int)$response['response']['code'];

		if( $code >= 400 ){
			throw new Exception( 'Request failed ' . $code . ': ' . $response['response']['message'], 1221 );
		}

		return json_decode( $response['body'] );
	}
}