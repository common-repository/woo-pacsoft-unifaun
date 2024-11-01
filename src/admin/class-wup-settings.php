<?php

namespace src\admin;

use Mustache_Autoloader;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use src\WUP_Plugin;
use WC_Shipping;
use WC_Shipping_Zones;

if ( ! defined( 'ABSPATH' ) ) exit;

class WUP_Settings{

    /**
     * Get services settings
     */
    public static function get_service_settings()
    {
        $settings = get_option( 'pacsoft_services', [] );
        $rows = [];

        if( ! empty( $settings ) ) {
            foreach( $settings as $x => $setting ) {
                if( empty( $setting['shipping_method_id'] ) )
                    continue;
                $rows[] = [
                    'columns' => [
                        [
                            'column' => [
                                'name' => 'id',
                                'content' => self::get_shipping_methods_dropdown( $setting['shipping_method_id'] )
                            ]
                        ],
                        [
                            'column' => [
                                'name' => 'service',
                                'content' => self::get_services_dropdown( $setting['service'] )
                            ]
                        ],
                        [
                            'column' => [
                                'name' => 'sender-quick-value',
                                'content' => '<input type="text" name="pacsoft_services[sender_quick_value][]" value="' . $setting['sender_quick_value'] . '" placeholder="1"> <a href="#" class="dashicons dashicons-dismiss removeRow"></a>'
                            ]
                        ]
                    ]
                ];
            }
        }

        return $rows;
    }

    /**
     * @param $shipping_methods
     * @param $zone
     * @return array
     */
    private static function get_shipping_methods( $shipping_methods, $zone ){
        $function = function ( $shipping_method ) use( $zone ) {
            return [
                'id' => $shipping_method->id . ':' . $zone['zone_id'] . ':'.$shipping_method->instance_id,
                'name' => $shipping_method->title . ' - ' . $zone['zone_name']
            ];
        };
        return array_map( $function, $shipping_methods ) ;
    }

    private static function get_class_methods()
    {
        $function = function ( $class ) {
            return [
                'id' => $class->taxonomy . ':' . $class->slug . ':' . $class->term_taxonomy_id . ':' . $class->term_id,
                'name' => $class->name
            ];
        };
        return array_map($function, \WC_Shipping::instance()->get_shipping_classes());
    }
    /**
     * Get WooCommerce Shipping Methods dropdown
     *
     * @param $selected
     * @return string
     */
    public static function get_shipping_methods_dropdown( $selected = '' ){

        $function = function ( $methods, $shipping_zone ){
            return array_merge( $methods, self::get_shipping_methods( $shipping_zone['shipping_methods'], $shipping_zone ) );
        };

        $shipping_methods = array_reduce( \WC_Shipping_Zones::get_zones(), $function, [] );
        $shipping_methods = array_merge( $shipping_methods, self::get_class_methods() );

        $options = array_map( function( $shipping_method ) use( $selected ) {
            $selected = ( $shipping_method['id'] == $selected ) ? ' selected="selected"' : '';

            return '<option value="' . $shipping_method['id'] . '"' . $selected . '>' . $shipping_method['name'] . '</option>';
        }, array_values( $shipping_methods ) );

        return '<select name="pacsoft_services[shipping_method_id][]"><option value=""></option>' . join( '', $options ) . '</select>';
    }


    /**
     * Get services dropdown
     *
     * @param $selected
     * @return string
     */
    public static function get_services_dropdown( $selected = '' ){
        $services = self::get_services();

        $options = array_map( function( $service ) use( $selected ) {
            $selected = ( $service['code'] == $selected ? ' selected="selected"' : '' );

            $show_or_hide = get_option('pacsoft_base_country') == $service['country'] ? '' : 'hidden';

            return '<option value="' . $service['code'] . '"' . $selected . ' data-woocommerce-pacsoft-service-base-country="' . $service['country'] . '" ' . $show_or_hide . '>' . $service['title'] . '</option>';
        }, $services );

        return '<select name="pacsoft_services[service][]"><option value=""></option>' . join( '', $options ) . '</select>';
    }

    /**
     * Get services list
     */
    public static function get_services(){
        static $services = [];

        if( ! empty( $services ) ){
            return $services;
        }

        $array = self::parse_services( file_get_contents( WUP_Plugin::get_path( 'data/services_SE.csv' ) ), "SE" );
        $services = array_merge( $services, $array );

        $array = self::parse_services( file_get_contents( WUP_Plugin::get_path( 'data/services_NO.csv' ) ), "NO" );
        $services = array_merge( $services, $array );

        $array = self::parse_services( file_get_contents( WUP_Plugin::get_path( 'data/services_DK.csv' ) ), "DK" );
        $services = array_merge( $services, $array );

        return $services;
    }

    private static function parse_services( $str, $country ) {

        $services = [];
        $rows = explode( "\n", $str );

        foreach( $rows as $row ) {
            $columns = explode( "\t", $row );
            $title = $columns[ 3 ] . ' (' . $columns[ 2 ] . ')';
            $code = $columns[ 2 ];
            $from = '';
            if( ! empty( $columns[ 4 ] ) )
                $from = explode( ", ", $columns[ 4 ] );
            $to = '';
            if( ! empty( $columns[ 5 ] ) )
                $to = explode( ", ", $columns[ 5 ] );
            $packagecode = self::get_package_type( $columns[ 0 ] );

            $services[] = compact( 'code', 'title', 'from', 'to', 'packagecode', 'country' );
        }
        return $services;
    }


    /**
     * Get package type
     *
     * @param $service
     * @return string
     */
    public static function get_package_type( $service )
    {
        switch( $service ) {
            default:
                $package_code = "";
                break;

            case "BOX":
                $package_code = "PK";
                break;

            case "CG":
            case "SBTL":
            case "DGF":
            case "DHLAIR":
            case "DHLROAD":
            case "DSVD":
            case "DSVI":
            case "DACHSER":
            case "KK":
            case "FREE":
            case "DTPG":
            case "TNT":
                $package_code = "PC";
                break;

            case "HIT":
                $package_code = "PARCEL";
                break;

            case "PP":
            case "PPDK":
            case "PPFI":
                $package_code = "KXX";
                break;

            case "TP":
                $package_code = "KLI";
                break;
        }

        return $package_code;
    }

    /**
     * Get Mustache instance (singleton)
     */
    public static function get_mustache(){
        static $mustache = null;

        if( empty( $mustache ) ) {
            if( ! class_exists( '\Mustache_Autoloader' ) ) {
                require_once WUP_Plugin::get_path( '/vendor/mustache/mustache/src/Mustache/Autoloader.php' );
                Mustache_Autoloader::register();
            }

            $mustache = new Mustache_Engine( [
                'loader' => new Mustache_Loader_FilesystemLoader( WUP_Plugin::get_path( '/assets/templates' ), [
                    'extension' => "ms"
                ] ),
                'partials_loader' => new Mustache_Loader_FilesystemLoader( WUP_Plugin::get_path( '/assets/templates/partials' ), [
                    'extension' => "ms"
                ] ),
                'cache' => WP_CONTENT_DIR . '/mustache',
                'helpers' => self::get_mustache_helpers()
            ] );
        }

        $mustache->addHelper( 'WUP_Settings::wup_help_tip', array(
            'wc_help_tip' => function() {
                ob_start();
                include 'test.php';
                return ob_get_clean();
            },
        ) );

        return $mustache;
    }

    public static function wup_help_tip($tip, $allow_html = false)
    {
        if ($allow_html) {
            $tip = wc_sanitize_tooltip($tip);
        } else {
            $tip = esc_attr($tip);
        }

        return '<span class="woocommerce-help-tip" data-tip="' . $tip . '"></span>';
    }

    /**
     * Get Mustache helpers
     */
    public static function get_mustache_helpers(){
        $helpers = [];

        $helpers['i18n'] = function( $text ) {
            /**
             * i18n
             *
             * @param string $text
             */
            $strings = WUP_Plugin::get_translated_strings();

            if( isset( $strings[ $text ] ) )
                return $strings[ $text ];

            return $text;
        };

        $helpers['formatTooltip'] = function( $tip, $helper ) {
            return $helper->render( '<span class="woocommerce-help-tip" data-tip="' . esc_attr( $tip ) . '"></span>' );
        };

        $helpers['formatHtmlTooltip'] = function( $tip, $helper ) {
            return $helper->render( '<span class="woocommerce-help-tip" data-tip="' . wc_sanitize_tooltip( $tip ) . '"></span>' );
        };

        return $helpers;
    }
}
