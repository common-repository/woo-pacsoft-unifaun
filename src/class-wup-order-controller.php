<?php

namespace src;

use Exception;
use src\api\WUP_HTTP_Request;
use src\api\WUP_Payload;
use src\api\WUP_Payload_Json;
use src\api\WUP_Payload_XML;
use src\utils\WUP_Credentials;
use src\utils\WUP_Help_Links;
use src\utils\WUP_Utils;
use src\WUP_Track_Shipment;

use iio\libmergepdf\Merger;

if ( ! defined( 'ABSPATH' ) ) exit;

class WUP_Order_Controller {
	const XML_PARSING_REGEX = '/<val n="to">([0-9]{1,})<\/val>/';
	const bulk_action_admin_notice_transient_key = 'wup_order_bulk_action';


	/**
	 * Adds necessary UI modifications
	 */
	public static function init() {

		add_action('kco_wc_process_payment',__CLASS__.'::kco_wc_process_payment_hook_handler',1000,2);

		self::maybe_display_bulk_action_results();
		add_filter( 'bulk_actions-edit-shop_order',
			function ( $actions ) {
				$actions['unifaun_pdfs'] = __( 'Fetch Unifaun shipment labels in a single PDF file', 'woocommerce' );

				return $actions;
			} );
		add_filter( 'handle_bulk_actions-edit-shop_order', __CLASS__ . '::handle_bulk_action', 10, 3 );
	}

	/**
	 * Returns array of PDFs to be displayed.
	 *
	 * @param $order_id int Woocommerce order id
	 *
	 * @return array
	 */
	public static function print_order_pdfs_apiconnect( $order_id ) {
		$ret   = [];
		$index = 0;
		while ( $url = WUP_Utils::get_order_meta_compat( $order_id, 'wtuc_freight_label' . $index ) ) {
			$ret[] = $url;
			$index ++;
		}
		if ( empty( $ret ) ) {

			//TODO try to refetch
			$ret = false;
		}

		return $ret;
	}

	/**
	 * @param $redirect string redirect url
	 * @param $doaction string action slug
	 * @param $object_ids array of post IDs
	 *
	 * @return mixed
	 */
	public static function handle_bulk_action( $redirect, $doaction, $object_ids ) {
		switch ( $doaction ) {
			case 'unifaun_pdfs':
				$unprocessed = [];
				$not_synced  = [];
				$files       = [];
				foreach ( $object_ids as $id ) {
					$index            = 0;
					$PDFs_unavailable = true;
					while ( $url = WUP_Utils::get_order_meta_compat( $id, 'wtuc_freight_label' . $index ) ) {
						$PDFs_unavailable = false;
						$index ++;
						//error_log( __FUNCTION__ . self::attachment_url_to_path( $url ) );
						$files[] = self::attachment_url_to_path( $url );
					}
					if ( $PDFs_unavailable ) {
						$unprocessed[] = $id;
					}
					if ( ! WUP_Utils::get_order_meta_compat( $id, '_pacsoft_order_synced' ) ) {
						$not_synced[] = $id;
					}
				}
				$result_url    = null;
				$extra_message = null;

				if ( 1 === count( $files ) ) {
					$extra_message = __( 'Nothing to merge but 1 file available:', 'woocommerce' );
					$result_url    = self::abs_path_to_url( $files[0] );
				}


				if ( ! empty( $files ) ) {

					require_once WUP_Plugin::get_path( '/vendor/autoload.php' );


					$merger = new Merger;
					foreach ( $files as $file ) {
						$merger->addFile( $file );
					}
					$createdPdf = $merger->merge();

					/**
					 * @wrike https://www.wrike.com/open.htm?id=850553109
					 * Using raw data to do not store on server side
					 */
					$result_url = 'data:application/pdf;base64,' . base64_encode( $createdPdf );
				}

				set_transient( self::bulk_action_admin_notice_transient_key,
					[
						'not_synced'    => $not_synced,
						'unprocessed'   => $unprocessed,
						'result_url'    => $result_url,
						'extra_message' => $extra_message,
					],
					300 );

				break;
		}

		return $redirect;
	}

	/**
	 * Converts file url to full path
	 *
	 * @param $url string
	 *
	 * @return bool|false|string|string[]
	 */
	private static function attachment_url_to_path( $url ) {
		$dir  = wp_get_upload_dir();
		$path = $url;

		$site_url   = parse_url( $dir['url'] );
		$image_path = parse_url( $path );

		//force the protocols to match if needed
		if ( isset( $image_path['scheme'] ) && ( $image_path['scheme'] !== $site_url['scheme'] ) ) {
			$path = str_replace( $image_path['scheme'], $site_url['scheme'], $path );
		}

		if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
			$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
		}
		$path = $dir['basedir'] . '/' . $path;
		if ( file_exists( $path ) ) {
			return $path;
		}

		return false;
	}


	/**
	 * Adds custom columns including
	 *      * PDFs links column
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public static function add_columns($columns = []){
		$columns['unifaun_pdfs'] = __( "Unifaun PDFs", 'woocommerce' );

		return $columns;
	}


	/**
	 * Echoes cell contents with PDFs links
	 *
	 * @param $column string column slug
	 * @param $order_id int post id
	 */
	public static function get_column_content( $column, $order_id )
	{
		if ( 'unifaun_pdfs' == $column ) {
			$urls_html = [];
			$index     = 0;
			while ( $url = WUP_Utils::get_order_meta_compat( $order_id, 'wtuc_freight_label' . $index ) ) {
				$urls_html[] = '<a href="' . $url . '">' . ( $index + 1 ) . '</a>';
				$index ++;
			}
			if ( empty( $urls_html ) ) {
				$urls_html = 'none';
			}
			if ( is_array( $urls_html ) ) {
				$urls_html = implode( ', ', $urls_html );
			}
			echo $urls_html;
		}
	}

	/**
	 * Converts abs path to url relative to site/blog
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	private static function abs_path_to_url( $path = '' ) {
		$url = str_replace(
			wp_normalize_path( untrailingslashit( ABSPATH ) ),
			site_url(),
			wp_normalize_path( $path )
		);
		return esc_url_raw( $url );
	}

    /**
     * Sync order
     *
     * @param int $order_id
     * @param string $service
     * @param bool $force
     * @return bool
     * @throws Exception
     */
    public static function sync( $order_id, $service = null, $force = false ){
        if( ! WUP_Credentials::check_license() ){
            throw new Exception( 'Ogiltig licens. ' . WUP_Help_Links::get_error_text( 100 ) );
        }

        if( WUP_Utils::get_order_meta_compat( $order_id, '_pacsoft_order_synced' ) && ! $force )
            throw new Exception( __( 'Order already synced to Pacsoft/Unifaun.', WUP_Plugin::TEXTDOMAIN ) );

        if( in_array( get_option( 'pacsoft_account_type' ), [ WUP_Plugin::PACSOFT_XML, WUP_Plugin::UNIFAUN_XML] ) ){

            $response = self::create_stored_shipment_xml( $order_id, $service );
        }
        elseif ( get_option( 'pacsoft_account_type' ) == WUP_Plugin::UNIFAUN_REST || get_option( 'pacsoft_account_type' ) == WUP_Plugin::PACSOFT_REST ){
	        //TODO handle kss
	        if ( WUP_Utils::get_order_meta_compat( $order_id, '_kco_kss_reference') ) {
				WUP_KSS_processor::kco_wc_process_payment_hook_handler( $order_id );
	        } else {
		        $response = self::create_stored_shipment_json( $order_id, $service );
	        }

        }
        $wc_order = wc_get_order( $order_id);
        $wc_order->update_meta_data( '_pacsoft_order_synced', 1 );
        $wc_order->update_meta_data( '_pacsoft_tracking_link',  WUP_Track_Shipment::get_tracking_link( $order_id ) );
        $wc_order->save();
        return $response;
    }

    /**
     * @param int $order_id
     * @param string $service
     * @return array|mixed|object
     * @throws Exception
     */
    private static function create_stored_shipment_xml( $order_id, $service  ){

        try{
            $response = WUP_HTTP_Request::post_xml( WUP_Payload_XML::generate( $order_id, $service ), true );
        }
        catch( Exception $error ) {

            self::add_order_log( $order_id, $error );
            throw new Exception( self::format_error_message( $error ) );

        }
	    preg_match_all( self::XML_PARSING_REGEX, $response, $matches );

	    if ( isset( $matches[1] ) ) {
		    self::set_stored_shipment_id( $order_id, $matches[1][0] );
	    }
	    $response = true; //WUP_HTTP_Request::post_xml always returns true. This thing for back capability.
        return $response;
    }

    /**
     * @param int $order_id
     * @param string $service
     * @return array|mixed|object
     * @throws Exception
     */
    private static function create_stored_shipment_json( $order_id, $service  ){

        $endpoint = '/rs-extapi/v1/stored-shipments';
        $response = WUP_HTTP_Request::post( $endpoint, WUP_Payload_Json::generate( $order_id, $service ) );

        if ( self::has_error( $response ) ){
            self::add_order_log( $order_id, $response );
            throw new Exception( self::format_error_message( $response ) );
        }
        self::set_stored_shipment_id( $order_id, $response->id );
        return $response;
    }

	/**
	 * @param $response
	 * @return bool
	 */
	private static function has_error( $response ){
		if ( $response->status == 'INVALID' ){
			return true;
		}
		return false;
	}


	/**
	 * Sets postmeta 'stored_shipment_id' of shop_order
	 * @param int $order_id
	 * @param $stored_shipment_id
	 */
	public static function set_stored_shipment_id( $order_id, $stored_shipment_id ) {
		$order = wc_get_order( $order_id );
		wetail_pacsoft_write_log("set_stored_shipment_id");
		$order->add_meta_data( 'stored_shipment_id', $stored_shipment_id, true );
		$order->save_meta_data();
	}

	/**
	 * Returns store Id
	 * @param int $order_id
	 * @return int
	 */
	public static function get_stored_shipment_id( $order_id ) {
		$order = wc_get_order( $order_id );
		return $order->get_meta( 'stored_shipment_id', true );
	}

	/**
	 * Fetches Unifaun PDF(s) for supplied order_id
	 *
	 * @param $order_id
	 *
	 * @return array
	 * @throws Exception
	 */
    public static function print_order_apiconnect( $order_id ){
    	if( ! ( $media = get_option( 'pacsoft_printer_type' ) ) ) $media = 'laser-ste';
	    $payload = array(
		    'target1Media'   => $media,
            'target1Type'    => 'pdf',
		    'target1XOffset' => 0,
		    'target1YOffset' => 0,
		    'target2Media'   => 'laser-a4',
            'target2Type'    => 'pdf',
		    'target2XOffset' => 0,
		    'target2YOffset' => 0
	    );

	    if( WUP_Payload::should_add_customs_declaration( $order_id ) ) {
	    	wetail_pacsoft_write_log( 'Should be printed with declaration for order ' . $order_id );
	    	$payload =  array_merge( $payload, [
			    "target3Media" => 'laser-ste',
			    "target3XOffset" => 0,
			    "target3YOffset" => 0
		    ] );
        }

	    $endpoint = '/ufoweb-prod-201709081356/rs-extapi/v1/stored-shipments';

	    $response = WUP_HTTP_Request::post(
		    $endpoint . '/' . self::get_stored_shipment_id( $order_id ) . '/shipments?inlinePdf=true',
		    $payload
	    );

	    wetail_pacsoft_write_log( $response );

	    $pdf_files = array();
	    if ( is_array( $response ) ) {
		    $pdf_files = array();
		    $index     = 0;
            foreach ( $response as $document ) {
                if ( isset( $document->pdfs ) && isset( $document->pdfs[0] ) && $document->pdfs[0]->pdf ) {
                    $pdf_files[] = self::render_label_pdf( $document->pdfs[0]->pdf, $order_id, $index );
                }
                elseif ( isset( $document->prints ) && isset( $document->prints[0] ) && $document->prints[0]->data ) {
                    $pdf_files[] = self::render_label_pdf( $document->prints[0]->data, $order_id, $index );
                }
                $index ++;
            }
	    } else {

		    if ( isset( $response->pdfs ) && isset( $document->pdfs[0] ) && $response->pdfs[0]->pdf ) {
			    $pdf_files[] = self::render_label_pdf( $response[0]->pdfs[0]->pdf, $order_id, 0 );
		    }
		    elseif ( isset( $response->prints ) && isset( $document->prints[0] ) && $response->prints[0]->data ) {
                $pdf_files[] = self::render_label_pdf( $response[0]->prints[0]->data, $order_id, 0 );
            }
	    }

        $wc_order = wc_get_order( $order_id);
        $wc_order->update_meta_data( '_unifaun_checkout_order_printed', 1 );
        $wc_order->save();

	    return array(
		    'pdf_files'  => $pdf_files,
		    'response'   => $response
	    );

    }

	/**
	 * Saves the given string to a pdf
	 *
	 * reworked with https://www.wrike.com/open.htm?id=850553109
	 *
	 * @param $base64_str string
	 * @param $order_id int
	 *
	 * @return string
	 */
	public static function render_label_pdf( $base64_str, $order_id, $number ){
		wetail_pacsoft_write_log( "PDF Rendering" );
		$upload_dir = wp_upload_dir();
		$pdf_decoded = base64_decode( $base64_str );
		wp_mkdir_p( trailingslashit( $upload_dir['basedir'] ) . 'freight_labels/' );
		$fn = $order_id . '-' . $number . '.pdf';

		/**
		 * @wrike https://www.wrike.com/open.htm?id=850553109
		 * Using raw data to do not store on server side
		 */
		$url = 'data:application/pdf;base64,' . base64_encode( $pdf_decoded );
		return $url;
	}

    /**
     * @param array | Exception $response
     * @return string
     */
    private static function format_error_message( $response ){
        if( is_a( $response, 'Exception' ) ){
            return  $response->getMessage() . ' ' . WUP_Help_Links::get_error_text( $response->getCode() );
        }

        $message = '';
        foreach ( $response->statuses as $status ){
            $message .= str_replace( '_', ' ',$status->field ) . ', ' . $status->message . WUP_Help_Links::get_error_text( $status->field ) . "\n" ;
        }
        return $message;
    }

    /**
     * @param int $order_id
     * @param array | Exception $response
     */
    private static function add_order_log( $order_id, $response ){

        $order = wc_get_order( $order_id );
        if( is_a( $response, 'Exception' ) ){
            $order->add_order_note( $response->getMessage() . ' ' . WUP_Help_Links::get_error_log_text( $response->getCode() ) );
            return;
        }

        $message = '';
        foreach ( $response->statuses as $status ){
            $message .= str_replace( '_', ' ',$status->field ) . ', ' . $status->message . WUP_Help_Links::get_error_log_text( $status->field ) . "\n" ;
        }

        $order->add_order_note( $message );
    }

	/**
	 * @param int $order_id WC_Order id
	 *
	 * @wrike   https://www.wrike.com/open.htm?id=807485034 - added filter on returning URL,
	 *          reworked in https://www.wrike.com/open.htm?id=850553109
	 *
	 * @return string
	 */
	public static function get_merged_pdf_url( $order_id ) {
		$meta_key = 'wtuc_freight_label_merged';
		$index = 0;

		require_once WUP_Plugin::get_path( '/vendor/autoload.php' );
		$merger = new Merger;
		/**
		 * @wrike https://www.wrike.com/open.htm?id=850553109
		 * Using raw data to do not store on server side
		 */
		$pdfs = self::print_order_apiconnect($order_id);
		foreach ( $pdfs['pdf_files'] as $pdf ) {
			$data    = explode( ',', $pdf );
			$decoded = base64_decode( $data[1] );
			if ( false !== $decoded ) {
				$merger->addRaw( $decoded );
				$index ++;
			}
		}
//		while ( $url = get_post_meta( $order_id, 'wtuc_freight_label' . $index, true ) ) {
//			$merger->addRaw( self::attachment_url_to_path($url ));
//			$index ++;
//		}
		$createdPdf = $merger->merge();

		/**
		 * @wrike https://www.wrike.com/open.htm?id=850553109
		 * Return base64 instead of url, do not even write file
		 */
		$url = 'data:application/pdf;base64,' . base64_encode( $createdPdf );

		return $url;
	}


}
