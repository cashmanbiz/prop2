=== WP Admin QR Code Generator ===
Contributors: themeavenue
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JYUUP2UCJVZPA
Tags: qr code,barcode
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is an extremely simple plugin that generates QR codes for posts and pages

== Description ==

Are you developing for mobile devices? Then this plugin is for you!

How annoying is it to type the long and complex URL of a development site on your small mobile's keyboard? I got tired of it and came out with this plugin.

The plugin will add a new metabox in the page/post edit screen, and another one on the admin dashboard. For pages/posts, a QR code will be dynamically generated, pointing to the page's URL. No need to manually type the URL, just scann the QR code!

On the WordPress dashboard, another QR code will take you directly to the site's homepage after you scanned it with your mobile. All QR codes are dynamically generated through the Google Chart API.

The plugin is extremely simple, it doesn't do anything else than adding the QR Code metabox to posts. No calls to database, no extra information added to your site! Just a faster way to preview your pages on mobile devices.

== Installation ==

1. Upload `wp-admin-qr-code` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Scan the QR code on your pages / posts edit screen

The plugin can easily support custom post types. To add more post types, use the filter 'wpaqr_post_types'.

== Frequently asked questions ==

= How To Add Support for CPTs =

Example:

<?php
function add_qt_to_my_cpt( $cpts ) {
	return array_push( $cpts, 'my_cpt' );
}
add_filter( 'wpaqr_post_types', 'add_qt_to_my_cpt' );
?>

== Changelog ==

= v1.0.1 =
* Dashboard widget added

= v1.0.0 =
* First release of the plugin