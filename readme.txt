=== Woo Pacsoft Unifaun ===
Contributors: pierrewiberg, itsmikita, themusquito, tomaskircher
Donate link: 
Tags: ecommerce, woocommerce, shipping
Requires at least: 3.3
Tested up to: 6.6.1
Stable tag: 2.99.1
Version: 2.99.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Choose from over 150 transport services to ship your WooCommerce orders with, powered by Pacsoft/Unifaun.

== Description ==

Manage advanced logistics of your WooCommerce orders. You can set to book transport automatically upon an approved order, print freight labels and map your shipping methods to different transport services.

Your customers can track their shipment with a link provided in the order completed email. Abiltity to print freight labels from your WooCommerce order listing.

Powered by Pacsoft/Unifaun. You are required to have an account registered at Pacsoft or Unifaun.

Over 150 transport services from both local and global freight companies like DHL, Posten/Postnord, Bring, DB Schenker, Posti Oy, TNT and more.

More freight companies are being added constantly. Let us know if you are missing one.

== Installation ==

1. Upload `woo-pacsoft-unifaun` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Provide your Pacsoft/Unifaun account details in 'Settings' - 'Pacsoft/Unifaun' and set your shipping preferences.

== Frequently asked questions ==

= What is Sender Quick Value? =

In some cases you need to have multiple sender addresses, e.g. when your company is based in multiple countries. You can add multiple sender addresses in Pacsoft/Unifaun System and map its Sender Quick Value in WordPress.

== Screenshots ==



== Changelog ==
= 2.99.1 =
* Feature: New shipping services added
= 2.99 =
* Feature: HPOS correction for older WooCommerce versions
= 2.98 =
* Feature: HPOS support
= 2.97 =
* Bugfix: Handles the case if order parameter in hook "woocommerce_email_order_meta" is a string, used in thank-you-link function
= 2.96 =
* Bugfix: Correction of tracklink
= 2.95 =
* Fix: Does not print tracking url in transaction emails if order is virtual
* Fix: casts product weight to a float when calculating order weight
* Bugfix: removal of debug-log-warnings
= 2.93 =
* License check fix
* Save order tracking url on order sync
= 2.92 =
* Bugfix: Stores unique shipment
= 2.91 =
* Fix: PDF render correction
= 2.90 =
* Fix: GDPR compliance
= 2.87 =
* Fix: Thermo printer correction
* Fix: For handling minimum weight
= 2.86 =
* Fix: License key SSL Fix
= 2.85 =
* Fix: PHP 8.0 compatability
2.83
- Bugfix: License checker return types
2.82
- Bugfix: Popup on manual synchronization
- Added Import/Export type as setting for Customs Declarations
2.81
- Change Http library to Wordpress native
2.80
- 	Support for OnlineConnect third party label printing
- 	Support for ApiConnect label printing from WooCommerce Admin
- 	Support for ApiConnect bulk label printing
-   Default order minimum weight
2.74
- 	Bugfix for return labels
2.73
- 	Bugfixes
- 	New filter to alter json payload when posting an order "woo_pacsoft_unifaun_order_json_payload"
- 	New action to alter xml payload when posting an order "woo_pacsoft_unifaun_order_xml_payload"
2.72
- 	Customs declaration fixes
- 	Abiblity to choose on  which order status tracking link is sent to customer
2.71
- 	Customs declaration fixes
- 	Abiblity to choose on  which order status tracking link is sent to customer

2.69
- 	Bugfix: link in order mail will only be appended if order status is processing
2.68
- 	Bugfix: link in order mail will only be appended if order status is completed
- 	Bugfix: service mapping fix
2.67
- 	Bugfix: localization for link in order mail
2.66
- 	Bugfixes

2.65
- 	Bugfixes for unnecessary loading of settings
-	P17 SMS addon fix

2.60
- 	Makeover of admin settings
-       Bugfix for addons

2.50
- 	Support for APIConnect
-   Standard Sender quick value for manual sync

2.42
- 	Bugfix for automatic sync

2.4
- 	Added support for automatic sync for WooCommerce 3.4

2.3.17
- 	Added goodsdescription for DHL

2.3.16
- 	Bugfix: Saving more than 7 freight options in settings
-   Updated services

2.3.15
- 	SMS Notification fix
-	Swedish services updated

2.3.13
- 	Added state for us

2.3.1
- 	Added support for Norway

2.2.40
- 	Automatic sync disabled if already synchronized

2.2.2
- 	Added hook for custom order weight "woo_pacsoft_unifaun_custom_order_weight"

2.2.1
- 	Unifaun Track and trace added
- 	Better UI when manually syncing orders from order listing

2.1.6
-   SMS Pre-notification code is now included when you enable "Send SMS notification".

2.1.5
-   Fixed bug where removed WooCommerce product would cause sync to fail.

2.1.3
-   This plugin now works also if running with Woocommerce 2.6.X.

2.1.0
-   Sync shippings classes with shipping services.
-   If you have more then one shipping class in an order it will automatically choose the shipping service that corresponds to the most expensive shipping class.

2.0.13
- 	Added address2 (C/O field) to all Unifaun/Pacsoft labels if this was present in the WC order
- 	When you hold the shift key down and press the "Sync order to Pacsoft / Unifaun" button, you
	now force the sync, which means that the plugin will ignore the fact that the order has been
	synced before already.
-	When syncing orders, previously the web browser window would scroll up to the top in every case.
	Now, it only does so in the event of a failed sync. 

2.0.12
-   Added option to print return labels

2.0.11
-   Added all UPS services to plugin service list

2.0.10
-   Bugfixes

2.0.1
-   Added NOT (e-mail notification) addon to IT16 service.

2.0.0
-   Fixed bug where mapped Sender Quick Value was ignored.
-   Minor improvements.
-   Changed versioning format to Semantic Versioning 2.0.0.

== Upgrade notice ==

