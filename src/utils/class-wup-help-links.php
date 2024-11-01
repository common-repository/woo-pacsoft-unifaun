<?php

namespace src\utils;

use src\WUP_Plugin;

class WUP_Help_Links{

    /**
     * @param $code
     * @return mixed
     */
    public static function get_error_log_text( $code ){
        $link = self::get_link( $code );

        if( $link ){
            return '<a href="' . $link . ' " target="_blank">HJÄLPAVSNITT</a>';
        }
        return '';
    }

    /**
     * @param $code
     * @return mixed
     */
    public static function get_error_text( $code ){
        $link = self::get_link( $code );

        if( $link ){
            return '<button class="button button-primary"><a href="' . $link . ' " target="_blank" style="color:white;">HJÄLPAVSNITT</a></button>';
        }
        return '';
    }

    /**
     * @param $code
     * @return mixed
     */
    private static function get_link( $code ){

        if( in_array( get_option( 'pacsoft_account_type' ), [ WUP_Plugin::PACSOFT_XML, WUP_Plugin::UNIFAUN_XML] ) ) {
            $ref_table = self::get_xml_ref_table();
        }
        else{
            $ref_table = self::get_api_rest_ref_table();
        }

        if( array_key_exists( $code, $ref_table ) ){
            return $ref_table[$code];
        }
        return false;
    }

    /**
     * @return mixed
     */
    private static function get_xml_ref_table(){
        return array(
            "100" => 'https://docs.wetail.io/woocommerce-fortnox-integration/api-licens-saknas/',
            "401" => '',
            "403" => 'https://docs.wetail.io/woocommerce-fortnox-integration/api-licens-saknas/',
        );
    }

    /**
     * @return mixed
     */
    private static function get_api_rest_ref_table(){
        return array(
            );
    }
}