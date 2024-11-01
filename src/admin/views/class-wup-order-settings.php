<?php

namespace src\admin\views;

use src\admin\WUP_Admin_Template;
use src\admin\WUP_Settings;
use src\WUP_Plugin;

class WUP_Order_Settings{

    public static function init_settings(){

        $page = 'woocommerce-pacsoft';

        WUP_Admin_Template::add_tab( [
            'page' => $page,
            'name' => "order",
            'title' => __( "Order", WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_section( [
            'page' => $page,
            'tab' => 'order',
            'name' => 'order-mapping',
            'title' => __( 'Order Mapping', WUP_Plugin::TEXTDOMAIN )
        ] );

        // Services table
        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'order-mapping',
            'title' => __( 'Map services', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'table',
            'name' => 'pacsoft_services',
            'table' => [
                'table' => [
                    'id' => 'pacsoft-services',
                    'columns' => [
                        [
                            'column' => [
                                'name' => 'shipping-method',
                                'title' => __( 'Shipping Method', WUP_Plugin::TEXTDOMAIN )
                            ]
                        ],
                        [
                            'column' => [
                                'name' => 'service',
                                'title' => __( 'Service', WUP_Plugin::TEXTDOMAIN )
                            ]
                        ],
                        [
                            'column' => [
                                'name' => 'sender-quick-value',
                                'title' => __( 'Sender Quick Value', WUP_Plugin::TEXTDOMAIN )
                            ]
                        ]
                    ],
                    'rows' => WUP_Settings::get_service_settings(),
                    'addRowButton' => true,
                    'addRowButtonClass' => 'addPacsoftServiceRow'
                ]
            ],
            'description' => __( "NOTE: Remember to set your customer number for each service added in the list above in Pacsoft/Unifaun Admin &#8594; Maintenance &#8594; Senders &#8594; Search (sender quick value) &#8594; Edit<br>", WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_section( [
            'page' => $page,
            'tab' => 'order',
            'name' => 'synchronization-settings',
            'title' => __( 'General', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_base_country',
            'title' => __( 'Base country', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'dropdown',
            'options' => [
                [
                    'value' => 'SE',
                    'label' => __( 'Sweden, SE', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'NO',
                    'label' => __( 'Norway, NO', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'DK',
                    'label' => __( 'Denmark, DK', WUP_Plugin::TEXTDOMAIN )
                ]
            ]
        ] );

	    WUP_Admin_Template::add_field( [
		    'page' => $page,
		    'tab' => 'order',
		    'section' => 'synchronization-settings',
		    'name' => 'pacsoft_favorite',
            	    'tooltip' => __( "This setting will apply the given favorite printing template when the order is synchronized. You will find your favorite templates in nShift interface. Shipment->Printing favorites", WUP_Plugin::TEXTDOMAIN ),
		    'title' => __( 'Favorite', WUP_Plugin::TEXTDOMAIN ),
	    ] );

	    WUP_Admin_Template::add_field( [
		    'page' => $page,
		    'tab' => 'order',
		    'section' => 'synchronization-settings',
		    'name' => 'pacsoft_order_number_prefix',
		    'title' => __( 'Prefix added to order number', WUP_Plugin::TEXTDOMAIN ),
	    ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_default_product_type',
            'tooltip' => __( "The default product description that will be used in the printing label.", WUP_Plugin::TEXTDOMAIN ),
            'title' => __( 'Default product type', WUP_Plugin::TEXTDOMAIN ),
            'default' => 'Varor'
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_default_minimum_order_weight',
            'tooltip' => __( "The minimum order weight sent to the stored shipment. This setting is handy when you have not assigned any weight on your products as many freight companies does not allow orders without any weight and will hence invalidate the order..", WUP_Plugin::TEXTDOMAIN ),
            'title' => __( 'Default minimum Order weight (grams)', WUP_Plugin::TEXTDOMAIN )
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_default_sender_quick_id',
            'tooltip' => __( "The default sender quick id. You can find your default sender quick ids in nShift interface. Maintenance->Sender", WUP_Plugin::TEXTDOMAIN ),
            'title' => __( 'Default sender quick id', WUP_Plugin::TEXTDOMAIN ),
            'default' => '1'
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_on_order_status',
            'title' => __( 'Send on order status', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'dropdown',
            'options' => [
                [
                    'value' => '',
                    'label' => __( 'Please select...', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'processing',
                    'label' => __( 'Processing', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'completed',
                    'label' => __( 'Completed', WUP_Plugin::TEXTDOMAIN )
                ]
            ]
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'type' => 'checkboxes',
            'title' => __( 'More options', WUP_Plugin::TEXTDOMAIN ),
            'options' => [
                [
                    'name' => 'pacsoft_sync_with_options',
                    'label' => __( 'Show options when syncing (disables auto-sync)', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_addon_sms',
                    'label' => __( 'Send SMS notification (Addon)', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_addon_notemail',
                    'label' => __( 'Send email notification (Addon)', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_addon_enot',
                    'label' => __( 'Send pre-notification by e-mail (Addon)', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_print_freight_label_per_item',
                    'label' =>  __( 'Print freight label per item in a box', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_single_package_per_order',
                    'label' =>  __( 'Send single package per order', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_print_return_labels',
                    'label' =>  __( 'Add return labels to order', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'name' => 'pacsoft_logfile_activated',
                    'label' =>  __( 'Activate log file', WUP_Plugin::TEXTDOMAIN )
                ]
            ]
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_track_link_on_order_status',
            'tooltip' => __( "Please note that you need to have the nShift Track & Trace service active.", WUP_Plugin::TEXTDOMAIN ),
            'title' => __( 'Send track link to customer on order status', WUP_Plugin::TEXTDOMAIN ),
            'type' => 'dropdown',
            'options' => [
                [
                    'value' => '',
                    'label' => __( 'Please select...', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'processing',
                    'label' => __( 'Processing', WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => 'completed',
                    'label' => __( 'Completed', WUP_Plugin::TEXTDOMAIN )
                ]
            ]
        ] );

        WUP_Admin_Template::add_field( [
            'page' => $page,
            'tab' => 'order',
            'section' => 'synchronization-settings',
            'name' => 'pacsoft_printer_type',
            'title' => __( "Printer type", WUP_Plugin::TEXTDOMAIN ),
            'tooltip' => __( "The type of printer you use to print the shipping label.", WUP_Plugin::TEXTDOMAIN ),
            'type' => "dropdown",
            'options' => [
                [
                    'value' => "laser-ste",
                    'label' => __( "Please select...", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "thermo-se",
                    'label' => __( "Etikett", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "laser-ste",
                    'label' => __( "A4", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "laser-a5",
                    'label' => __( "Single A5 label.", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "laser-2a5",
                    'label' => __( "Two A5 labels on a  A4-sheet", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "laser-a4",
                    'label' => __( "Normal A4", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "thermo-se",
                    'label' => __( "107 x 251 mm thermo label", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "thermo-brev3",
                    'label' => __( "107 x 72 mm thermo label", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "thermo-165",
                    'label' => __( "107 x 165 mm thermo label", WUP_Plugin::TEXTDOMAIN )
                ],
                [
                    'value' => "thermo-190",
                    'label' => __( "107 x 190 mm thermo label", WUP_Plugin::TEXTDOMAIN )
                ]
            ]
        ] );
    }
}
