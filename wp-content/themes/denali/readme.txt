Denali WordPress Theme By TwinCitiesTech.com - http://twincitiestech.com
Theme Homepage -  http://twincitiestech.com/plugins/wp-property/wp-property-premium-theme-the-denali/
-------------------------------------------------------------------------------------------------

== Changelog ==

= 3.2.4 =
* Removed default WP-Property logo from home page
* Fixed rounded corners styling
* Fixed mobile device compatibility
* Fixed editor styles on frontend ( streamline of images ).

= 3.2.3 =
* Fixed Yes/No checkbox settings.
* Fixed issue related to scrollTo when Attention Grabber's tab is changed.

= 3.2.2 =
* Fixed fatal error caused by fnmatch function on non-POSIX compliant systems.
* Fixed [supermap] shortcode view on Attention Grabber tabs.
* Fixed selected value of dropdown list on FEPS Edit page.

= 3.2.1 =
* Added Front End Property Submissions (FEPS) 2.0 compatibility

= 3.2 =
* Fixed fatal warnings.
* Fixed EqualHeights functionality.
* Fixed single property page view.
* Fixed [property_overview] default view.
* Fixed issue with incorrect data of [property_map].
* Fixed issue related to mod_security ( 406 error ).
* Fixed issue related to rewrite rules ( 404 error ).
* Fixed issues with HTML symbols in Denali settings values.
* Fixed and improved adding of dummy properties ( Setup Assistant ).
* Added compatibility with WP-Property 1.37.4 and higher.
* Added prevention of saving specific Denali postmeta data on auto-draft.
* Added French Localization.

= 3.1.3 =
* Fixed issue related to switching Denali theme to default one on WP-Property updating.
* Maintenance Mode will be shown on frontend if WP-Property plugin is deactivated or doesn't installed.

= 3.1.2 =
* Fixed Denali updates checker
* Fixed bug related to HTML data storing in theme settings

= 3.1.1 =
* Removed declaration of unused Google Fonts.
* Fixed logo image storing.
* Fixed issue on post saving and removed extra hook calling.
* Fixed issues related to using WP-Property version less then 1.36.

= 3.1 =
* Added Responsive stylesheet for different browser resolutions (compatibility with mobile platforms).
* Added ability to import/export Denali theme settings, widgets and menus settings (Help Tab).
* Added ability to look through denali settings array for better troubleshooting (Help Tab).
* Added set of icons and added ability to assign icon to attribute for 'property overview' horizontal list.
* Added localization files and implemented Russian localization.
* Added warning notice and switching Denali theme to default WP theme (Twenty Eleven, Twenty Ten) if WP-Property plugin is not activated.
* Improved CSS.
* Typos fixes.
* Fixed theme update functionality and added ability to enable/disable Denali new version checking.
* Fixed adding extra widgets to Search area on first theme activation.
* Fixed notification about Inquire sending.
* Fixed Child Theme creation: actual parent directory name is set in style.css on creating.
* Fixed templates.

= 3.0.1 =
* Fixed "Contact Us" form functionality and view
* Fixed property_overview templates: attributes showing

= 3.0.0 =
* Globally disable comments on all non-property pages.
* Page-specific options: Do not show sidebar, Do not show page title, Do not show header area, Do not show location map and Allow property inquiries.
* Option to automatically hide all empty widgets from being displayed on the front-end.
* Shortcode can be used in "Contact Us" dropdown info block.
* PDF Flyer shortcode is rendered with an icon when inserted into tagline or property title.
* Added option to enable or disable post meta on in home page loop.

= 2.6.1 =
* Fix to slideshow feature being called causing an error when the feature is not installed.

= 2.6 =
* Added styles for FEPS feature.
* Fixed to "Global Property Search" widget on slideshow overlay.
* Added conditional body classes for the currently used color scheme.
* Added fix to Auto Setup Assistant to configure permalinks.
* Added fix to slideshow loading.
* Changed the way Property Listings sidebar widget is loaded.
* Minor fixed to "Dark" color scheme.

= 2.5 =
* Style fixes to make compatible with WPP 1.22.0 attribute grouping.
* Added pending property template page.

= 2.4 =
* Added an option to add the qTranslate widget to the top of the page, when installed.
* Small fix to [property_overview] to format results without images better by adding a padding to the left side.

= 2.3 =
* Added styles for WPP 1.20.0 pagination.
* Improved auto-setup function.
* Fixed issue that occured on certain sties with method_exists() causing a fatal error.
* Improved Child Theme setup function to copy images.
* Added conditional checks to prevent errors when premium features are not installed.
* Added UI to customize phone number prefix.
* Updated default property template to show attributes with value of "true" as checkboxes.
* Fixed issue with highlighted navigation button not working for links with long titles.
* Added styles for "Details" button in [property_overview]

= 2.1.1 =
* Updated the way header images and slide shows are handled.
* Added marker to "Our Location" drop down map.
* Fixed line breaks in address in header drop down tab.
* Added filters to hook into WP-CRM plugin for email notifications.
* Added code to hide "gap" on home page when there is no content in the page.
* Fixed styling for Blue Scheme text widgets.

= 2.1 =
* New "Setup Assistant" feature for quick setup, example: http://vimeo.com/26637788
* New navigation menu available at the very bottom of the screen.
* Denali Child theme is now packaged into the main theme and can be installed automatically using the Settings page.
* New widget area (sidebar) now available and is included on all pages that include the [property_overview] shortcode.
* Added the wpp_inquiry_form() function to replace the default comment forum function.
* Upgraded theme to use the new wpp_get_image_link() function for on-the-fly image resizing.
* Added new feature where skins can have thumbnails associated with them.

= 2.0.4 =
* Fixed a property overview bug that occured when no default address attribute was set but address attribute was set to display on results.

= 2.0.3 =
* Added JavaScript code to adjust margins on home and post page for situations when slideshow is not as tall as property search widget that overlays it.
* Fixed positioning of slideshow scrolling arrows on home page slideshow.

= 2.0.2 =
* Added shortcode execution to property meta fields.
* Fixed footer to prevent overlap of EHO icon when Explore block is hidden.
* Added option to make custom inquiry fields required.
* Added JavaScript validation of forms (inquiry and comment)
* Fixed static width issue or horizontal widget area when using no-column templates.
* Added fixed title width to sidebar property widgets to resolve problem mentioned here: http://forums.twincitiestech.com/topic/featured-properties-title-not-wrapping-properly

= 1.9  =
* Fixes to dark color scheme.
* Removed text from sprite image, and changes style.css file to not hide text labels on buttons.
* Fixed property_overview width issues for home page.
* Added 	current_theme_supports() elements: inner_page_slideshow_area, post_page_attention_grabber_area, footer_explore_block
* Hid thumbnail from property overview shortcode if no thumbnail exists.
* Added numerous localization strings which can be inherited from WP-Property.
* Added 'denali_header_menu' filter to navigation menu.
* Added option to hide header image completely if there is no big enough image for the given page.
* Added option to hide slideshow areas on home and post pages.
* Added option to hide explorer block.

