<?php

namespace src\admin\views;

use src\admin\WUP_Admin_Template;
use src\WUP_Plugin;

class WUP_Customs_Settings
{

    public static function init_settings(){

        $page = 'woocommerce-pacsoft';

        WUP_Admin_Template::add_tab( [
            'page' => $page,
            'name' => "customs",
            'title' => __( "Customs", WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_section( [
            'page' => $page,
            'tab' => 'customs',
            'name' => 'pacsoft-general-customs-declaration',
            'title' => __( 'Customs Declaration', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'customs',
            'section' => 'pacsoft-general-customs-declaration',
            'type' => 'checkboxes',
            'title' => __( 'Send customs declaration', WUP_Plugin::TEXTDOMAIN ),
            'options' => [
                [
                    'name'  => 'pacsoft_send_customs_declaration',
                    'label' =>  __( '', WUP_Plugin::TEXTDOMAIN )
                ]
            ]
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'customs',
            'section' => 'pacsoft-general-customs-declaration',
            'name' => 'pacsoft_customs_declaration_document_number',
            'title' => __( 'Document number', WUP_Plugin::TEXTDOMAIN ),
            'default' => 'CN22'
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'customs',
            'section' => 'pacsoft-general-customs-declaration',
            'name' => 'pacsoft_customs_declaration_description',
            'title' => __( 'Default customs declaration description', WUP_Plugin::TEXTDOMAIN ),
            'default' => 'Varor'
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'customs',
            'section' => 'pacsoft-general-customs-declaration',
            'name' => 'pacsoft_customs_declaration_cart_threshold',
            'title' => __( 'Create customs declaration if cart value is over given value', WUP_Plugin::TEXTDOMAIN ),
            'default' => '2000'
        ] );


        WUP_Admin_Template::add_field( [
            'page' => $page,
            'section' => 'pacsoft-general-customs-declaration',
            'tab' => 'customs',
            'name' => 'pacsoft_customs_other_unit',
            'title' => __( 'Customs weight unit', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'dropdown',
            'options' => [
                [
                    'value' => '',
                    'label' => __( 'Please select...', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'KGS',
                    'label' => 'KG (UPS)'
                ],
                [
                    'value' => 'KG',
                    'label' => 'KG'
                ]
            ]
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'section' => 'pacsoft-general-customs-declaration',
            'tab' => 'customs',
            'name' => 'pacsoft_customs_import_export_type',
            'title' => __( 'Import/Export Type', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'dropdown',
            'options' => [
                [
                    'value' => '',
                    'label' => __( 'Please select...', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'SAMPLE',
                    'label' => 'Commercial sample'
                ],
                [
                    'value' => 'DOCUMENTS',
                    'label' => 'KG'
                ],
                [
                    'value' => 'GIFT',
                    'label' => 'Gift'
                ],
                [
                    'value' => 'OTHER',
                    'label' => 'Other'
                ],
                [
                    'value' => 'INTERNAL',
                    'label' => 'Internal invocing'
                ],
                [
                    'value' => 'PERMANENT',
                    'label' => 'Permanent'
                ],
                [
                    'value' => 'RETURN',
                    'label' => 'Returned Goods'
                ],
                [
                    'value' => 'TEMPORARY',
                    'label' => 'Temporary'
                ]
            ]
        ] );
    }
}
