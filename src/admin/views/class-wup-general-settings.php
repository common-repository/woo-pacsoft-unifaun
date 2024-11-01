<?php

namespace src\admin\views;

use src\admin\WUP_Admin_Template;
use src\WUP_Plugin;

class WUP_General_Settings{

    public static function init_settings(){
        $page = 'woocommerce-pacsoft';

        WUP_Admin_Template::add_tab( [
            'page' => $page,
            'name' => "general",
            'title' => __( "General", WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_section( [
            'page' => $page,
            'tab' => 'general',
            'name' => 'pacsoft-account-type',
            'title' => __( 'Account', WUP_Plugin::TEXTDOMAIN ),
            'links' =>
                '<a href="https://wetail.se/support/" target="_blank" class="page-title-action">'
                . __( "Support", WUP_Plugin::TEXTDOMAIN )
                . '</a> ' .
                '<a href="https://docs.wetail.io/woocommerce-pacsoft-unifaun-integration/" '
                .  'target="_blank" class="page-title-action">'
                . __( "FAQ", WUP_Plugin::TEXTDOMAIN )
                . '</a>'
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'section' => 'pacsoft-credentials',
            'tab' => 'general',
            'name' => 'pacsoft_account_type',
            'title' => __( 'Account type', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'dropdown',
            'options' => [
                [
                    'value' => '',
                    'label' => __( 'Please select...', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => WUP_Plugin::PACSOFT_XML,
                    'label' => 'Pacsoft OnlineConnect'
                ],
                [
                    'value' => WUP_Plugin::UNIFAUN_XML,
                    'label' => 'Unifaun OnlineConnect'
                ],
                [
                    'value' => WUP_Plugin::UNIFAUN_REST,
                    'label' => 'Unifaun APIConnect'
                ],
                [
                    'value' => WUP_Plugin::PACSOFT_REST,
                    'label' => 'Pacsoft APIConnect'
                ]
            ]
        ] );

        WUP_Admin_Template::add_section( [
            'page'      => $page,
            'tab'       => 'general',
            'name'      => 'pacsoft-credentials-xml',
            'title'     => __( 'Credentials OnlineConnect', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_field( [
            'page'      => $page,
            'tab'       => 'general',
            'section'   => 'pacsoft-credentials-xml',
            'name'      => 'pacsoft_usern_unif',
            'title'     => __( 'User', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_field( [
            'page'      => $page,
            'tab'       => 'general',
            'section'   => 'pacsoft-credentials-xml',
            'name'      => 'pacsoft_pass_unif',
            'title'     => __( 'Password', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_section( [
            'page'          => $page,
            'tab'           => 'general',
            'name'          => 'pacsoft-credentials-api-rest',
            'title'         => __( 'Credentials APIConnect', WUP_Plugin::TEXTDOMAIN ),
            'description'   => __( 'If you want to use Unifaun/Pacsoft features like Track and trace and printing from WooCommerce order listing please add Credentials to your Unifaun/Pacsoft account under <b>Credentials Unifaun/Pacsoft</b>', WUP_Plugin::TEXTDOMAIN ),
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'general',
            'section' => 'pacsoft-credentials-api-rest',
            'name' => 'pacsoft_api_id',
            'title' => __( 'API ID', WUP_Plugin::TEXTDOMAIN )
        ]  );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'general',
            'section' => 'pacsoft-credentials-api-rest',
            'name' => 'pacsoft_api_secret_id',
            'title' => __( 'API Secret ID', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_section( [
            'page' => $page,
            'tab' => 'general',
            'name' => 'pacsoft-general',
            'title' => __( 'General', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'general',
            'section' => 'pacsoft-general',
            'name' => 'pacsoft_license_key',
            'title' => __( 'API license key', WUP_Plugin::TEXTDOMAIN ),
            'description' => __( "The license key you received from Wetail", WUP_Plugin::TEXTDOMAIN ),
            'after' =>
                '<a href="https://wetail.se/service/intergrationer/woocommerce-pacsoft/" '.
                'class="button pacsoft-buy-license" target="_blank" style="display:none">'
                . __( "Buy", WUP_Plugin::TEXTDOMAIN ) . '</a>'
        ] );
    }
}
