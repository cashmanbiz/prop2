=== Column-Matic ===

Contributors: twinpictures, baden03
Donate link: http://plugins.twinpictures.de/flying-houseboat
Tags: columns, layout, wp post columns, shortcode, column, content areas, column shortcode, twinpictures, plugin-oven, pluginoven
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Column-Matic adds a shortcode that wraps post/page content up in cozy columns. Useful for magazine or newspaper style layouts.

== Description ==

Column-Matic easily inserts columns into posts and pages by wrapping content with a snappy shortcode. Useful for sites with a magazine or newspaper style layout, Column-Matic shortcode wraps content in cozy columns. Content can also be separated so that text and images are displayed on opposite sides of a post or page. Visit the <a href='http://plugins.twinpictures.de/plugins/column-matic/documentation/'>Twinpictures Plugin Oven | Colum-Matic | Documentation</a> for a complete listing of shortcode options, even a few examples that might delight and even inform. <a href='http://wordpress.org/tags/column-matic'>Free Community Support</a> is available.

== Installation ==

1. Old-school: upload the `column-matic` folder to the `/wp-content/plugins/` directory via FTP.  Hipster: Add the Column-Matic via the WordPress Plugins menu.
1. Activate the Plugin
1. Test that the this plugin meets your demanding needs.
1. Tweak the theme css files for premium enjoyment.
1. Rate the plugin and verify that it works at wordpress.org.
1. Leave a comment regarding bugs, feature request or cocktail recipes at http://wordpress.org/tags/column-matic

== Frequently Asked Questions ==

= Example 1: 2 column layout using class only =
[column class="column1"]Column 1 content goes here.[/column]
[column class="column2"]Column 2 content goes here.[/column]
[end_columns]
Note: .column1 and .column2 must be defined in your theme's style.css

= Example 2: 3 column layout using css definitions =
[column width="200px" padding="10px"]Column 1 content goes here.[/column]
[column width="200px" padding="10px"]Column 2 content goes here.[/column]
[column width="200px" padding="10px"]Column 3 content goes here.[/column]
[end_columns]

= How does one remove the strange &lt;br/> and &lt;/p> tags that are being inserted between the columns =
This is due to WordPress processing the wonky wpautop function before processing shortcodes.  For a quick workaround, try the following:
  
1. Change execution priority of wpautop by adding the following line to function.php:  
add_filter( 'the_content', 'wpautop',20 );  
  
2. Remove breaks and spaces between the shortcodes, so the the columns appear all clumped together:  
[column class="column1"]Column 1 content goes here.[/column][column class="column2"]Column 2 content goes here.[/column][end_columns]

= I am a Social Netwookiee, might Twinpictures have a Facebook page? =
Yes, yes... <a href='http://www.facebook.com/twinpictures'>Twinpictures is on Facebook</a>.

= Does Twinpictures do the Twitter? =
Ah yes! <a href='http://twitter.com/#!/twinpictures'>@Twinpictures</a> does the twitter tweeting around here.

= How does one use the shortcode, exactly? =
A <a href='http://plugins.twinpictures.de/plugins/column-matic/documentation/'>complete listing of shortcode options</a> has been provided to answer this exact question.

= Where can I translate this plugin into my favorite language? =
<a href='http://translate.twinpictures.de/projects/columnmatic'>Community translation for Column-Matic</a> has been set up. You are <a href='http://translate.twinpictures.de/wordpress/wp-login.php?action=register'>welcome to join</a>.

= Where can I go for jokes and fun? =
Why not try: <a href='http://jokesandfun.de/'>Jokes & Fun</a>

== Screenshots ==

1. Example 1: two columns&mdash;column 1 is assigned a width of 180px, column 2 a width of 120px. Both columns are assigned a padding of 10px.
1. Example 2: three columns w/ class attribute assigned&mdash;columns may be assigned a class that has been pre-defined in the theme's style.css file.

== Changelog ==

= 1.3.1 =
* fixed missing localization keys

= 1.3 =
* removed php4 constructor
* added I18n localization support

= 1.2 =
* added text_align attribute

= 1.1 =
* Completely re-wrote plugin
* Added options page to inject custom css

= 1.0.1 =
* Tested with 3.5 and small spelling corrections.

= 1.0 =
* The plugin came to be.

== Upgrade Notice ==

= 1.3.1 =
* fixed missing localization keys

= 1.3 =
* removed php4 constructor
* added I18n localization support

= 1.2 =
* text_align attribute has been added.

= 1.1 =
Plugin is completely rewritten and now has it's own options page for injecting custom css.

= 1.0.1 =
Plugin tested with WP 3.5

= 1.0 =
Where once there was not, there now is.