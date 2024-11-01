<?php

namespace src\admin\orders;

use src\utils\WUP_Utils;
use src\WUP_Order_Controller;
use src\WUP_Plugin;

use iio\libmergepdf\Merger;

class WUP_Admin_Order_Processing {
	const BULK_ACTION_ADMIN_NOTICE_TRANSIENT_KEY = 'wup_order_bulk_action';

	/**
	 * Add required UI mods
	 */
	public static function init() {


		self::maybe_display_bulk_action_results();
		add_filter( 'bulk_actions-edit-shop_order',
			function ( $actions ) {
				$actions['unifaun_pdfs'] = __( 'Unifaun, Bulk print order shipment labels', WUP_Plugin::TEXTDOMAIN  );

				return $actions;
			} );
		add_filter( 'handle_bulk_actions-edit-shop_order', __CLASS__ . '::handle_bulk_action', 10, 3 );
	}


	/**
	 * Adds custom columns including
	 *      * PDFs links column
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public static function add_columns( $columns = [] ) {
		$columns['unifaun_pdfs'] = __( "Unifaun PDFs", 'woocommerce' );

		return $columns;
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
                    try{
	                    $pdfs = WUP_Order_Controller::print_order_apiconnect($id);
	                    error_log(__FUNCTION__.': '.json_encode($pdfs));
	                    foreach ( $pdfs['pdf_files'] as $pdf ) {
		                    $data    = explode( ',', $pdf );
		                    $decoded = base64_decode( $data[1] );
		                    if ( false !== $decoded ) {
			                    $files[]          = $decoded;
			                    $PDFs_unavailable = false;
			                    $index ++;
		                    }
	                    }
                    }
                    catch (\Exception $exception){

                    }
					if ( $PDFs_unavailable ) {
						$unprocessed[] = $id;
					}
					if ( ! WUP_Utils::get_order_meta_compat( $id, '_pacsoft_order_synced') ) {
						$not_synced[] = $id;
					}
				}
				$result_url    = null;
				$extra_message = null;

				if ( 1 === count( $files ) ) {
					$extra_message = __( 'Nothing to merge but 1 file available:', 'woocommerce' );
					/**
					 * @wrike https://www.wrike.com/open.htm?id=850553109
					 * Using raw data to do not store on server side
					 */
					$result_url    = 'data:application/pdf;base64,' . $files[0];
				}


				if ( ! empty( $files ) ) {

					require_once WUP_Plugin::get_path( '/vendor/autoload.php' );


					$merger = new Merger;
					foreach ( $files as $file ) {
						$merger->addRaw( $file );
					}
					$createdPdf = $merger->merge();
					/**
					 * @wrike https://www.wrike.com/open.htm?id=850553109
					 * Using raw data to do not store on server side
					 */
					$result_url = 'data:application/pdf;base64,' . base64_encode( $createdPdf );
				}

				set_transient( self::BULK_ACTION_ADMIN_NOTICE_TRANSIENT_KEY,
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
	 * Echoes cell contents with PDFs links
	 *
	 * @param $column string column slug
	 * @param $order_id int post id
	 */
	public static function get_column_content( $column, $order_id ) {
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
	 * Displays bulk action results if any
	 */
	private static function maybe_display_bulk_action_results() {
		if ( ! $bulk_results = get_transient( self::BULK_ACTION_ADMIN_NOTICE_TRANSIENT_KEY ) ) {
			return;
		}

		add_thickbox();
		delete_transient( self::BULK_ACTION_ADMIN_NOTICE_TRANSIENT_KEY );

		add_action( 'admin_notices',
			function () use ( $bulk_results ) {
				?>
                <div id="pacsoft-merged-pdf-thickbox" style="display: none">
					<?php
					if ( ! empty( $bulk_results['not_synced'] ) ) {
						?>
                        <div class="notice notice-warning is-dismissible">
                            <h2>Unifaun integration warning:</h2>
                            <p>These order IDs were not processed since they're not synced:</p>
                            <p><?php echo implode( ', ', $bulk_results['not_synced'] ); ?></p>
                        </div>
						<?php
					}
					if ( ! empty( $bulk_results['unprocessed'] ) ) {
						?>
                        <div class="notice notice-warning is-dismissible">
                            <h2>Unifaun integration warning:</h2>
                            <p>These order IDs were not processed since they're have no PDFs:</p>
                            <p><?php echo implode( ', ', $bulk_results['unprocessed'] ); ?></p>
                        </div>
						<?php
					}

					if ( ! empty( $bulk_results['result_url'] ) ) {
						?>
                        <div class="notice notice-success is-dismissible">
                            <h2>Unifaun integration info:</h2>
                            <p>Here is your PDF:</p>
                            <p>
                                <a href="<?php echo $bulk_results['result_url']; ?>"> <?php echo __( "PDF", WUP_Plugin::TEXTDOMAIN ); ?></a>
                            </p>
							<?php if ( ! empty( $bulk_results['extra_message'] ) ) {
								echo '<p>' . $bulk_results['extra_message'] . '</p>';
							} ?>
                        </div>
                        <div>
                            <p><a href="<?php echo $bulk_results['result_url']; ?>"><?php echo __( "Direct link",
										"woocommerce" ); ?></a></p>
                            <iframe src="<?php echo $bulk_results['result_url']; ?>"
                                    style="width: 100%; height: 685px;"></iframe>
                        </div>
						<?php

					}

					?>
                </div>
                <script>
                    (($) => {
                        $(document).ready(() => {
                            setTimeout(() => {
                                tb_show("<?php echo __( "Bulk print shipment labels",
									"woocommerce" );?>", "/?TB_inline&width=800&height=750&inlineId=pacsoft-merged-pdf-thickbox")
                            }, 1000);
                        });
                    })(jQuery);
                </script>
				<?php
			} );
	}

    /**
     * Returns array of PDFs to be displayed.
     * @param $order_id int Woocommerce order id
     * @return array
     * @throws \Exception
     */
	public static function print_order_pdfs_apiconnect_ajax( $order_id ) {
		if ( WUP_Utils::get_order_meta_compat( $order_id, 'wtuc_freight_label0' ) ) {
            WUP_Utils::delete_order_meta_compat( $order_id, 'wtuc_freight_label0' );
		}

		if ( WUP_Utils::get_order_meta_compat( $order_id, 'wtuc_freight_label_merged', true ) ) {
            WUP_Utils::delete_order_meta_compat( $order_id, 'wtuc_freight_label_merged' );
		}

//        WUP_Order_Controller::print_order_apiconnect( $order_id );

		$url = WUP_Order_Controller::get_merged_pdf_url($order_id);
		return $url?[$url]:[false];
	}

    /**
     * Returns an url to load OnlineConnects print ifram.
     * @param $order_id int Woocommerce order id
     * @return array
     * @throws \Exception
     */
    public static function print_order_onlineconnect_ajax( $order_id ){
        $print_urls = array(
            WUP_Plugin::UNIFAUN_XML => 'https://www.unifaunonline.com/ext.uo.se.se.StartEmbeddedShipmentJob?Login=',
            WUP_Plugin::PACSOFT_XML => 'https://www.pacsoftonline.com/ext.po.se.se.StartEmbeddedShipmentJob?Login='
        );

        $order_number = apply_filters( 'woocommerce_order_number', $order_id, wc_get_order( $order_id ) );
        $url = $print_urls[get_option('pacsoft_account_type')] . get_option('pacsoft_usern_unif') .  '&Stage=PRINT&OrderNo=' . $order_number . '&ReturnUrl=';
        return array('url'=> $url);
    }
}
