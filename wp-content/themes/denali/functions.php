<?php
/**
 * Denali - Premium WP-Property Theme functions and definitions
 *
 * @package Denali - Premium WP-Property Theme
 * @since Denali 1.0
 */



// Ran before init so cannot be called by init function
add_action('after_setup_theme',  array('denali_theme', 'after_setup_theme'));

define('Denali_Version', '3.2.4');

/**
 * Main class for Denali theme options
 *
 * @since Denali 1.0
 */
class denali_theme {

    /**
     * Run on init hook, loads all other hooks and filters
     *
     * @since Denali 1.0
     *
     */
    static function init() {
      global $denali_theme_settings, $ds, $wp_properties;

      //** Be sure that Denali supports all WPP versions */
      self::legacy_compatibility();

      //** Set our global denali_theme_settings here once. */
      add_filter( 'denali_theme_settings', array('denali_theme', 'denali_theme_settings'), 0 );
      $denali_theme_settings = stripslashes_deep( get_option( 'denali_theme_settings' ) );
      $denali_theme_settings = apply_filters( 'denali_theme_settings', $denali_theme_settings );
      $ds = $denali_theme_settings;

      add_action('wpp_template_redirect', array('denali_theme', 'template_redirect_before'));
      add_action('wpp_template_redirect_post_scripts', array('denali_theme', 'template_redirect_after'));

      add_action('wp_ajax_denali_contact_form_submit', array('denali_theme', 'process_ajax_contact_form'));
      add_action('wp_ajax_nopriv_denali_contact_form_submit', array('denali_theme', 'process_ajax_contact_form'));

      add_action('post_submitbox_misc_actions', array('denali_theme', 'publish_metabox_options'));

      add_action('admin_menu', array('denali_theme', 'admin_menu'));
      add_action('admin_init', array('denali_theme', 'admin_init'));

      add_action('admin_bar_menu', array('denali_theme', 'admin_bar_menu'), 70);

      add_action('save_post', array('denali_theme', 'save_post'), 10, 2);

      add_action('admin_print_scripts-edit-comments.php', array('denali_theme', 'comment_page_css'), 0, 2);

      //* Enqueue specific scripts and styles on FEPS form and spc pages */
      add_action( 'wpp::feps::shortcode', array( __CLASS__, 'feps_shortcode_action') );
      add_action( 'wpp_feps_spc_page', array( __CLASS__, 'feps_shortcode_action') );

      //* Add filters/actions on Inquiry/Comment rendering and adding */
      add_action('comment_post', array('denali_theme', 'pre_send_admin_inquiry_notification'), 0, 2);
      add_action('wp_insert_comment', array('denali_theme', 'wp_insert_comment'), 0, 2);
      add_action('comment_post_redirect', array('denali_theme', 'comment_post_redirect'));

      add_action('manage_edit-comments_columns', array('denali_theme', 'add_inquiry_columns'));
      add_action('manage_comments_custom_column', array('denali_theme', 'manage_comments_custom_column'), 0, 2);
      add_action('wpp_insert_property_comment', array('denali_theme', 'send_agent_inquiry_notification'), 0, 2);

      add_filter('pre_render_inquiry_form', array('denali_theme', 'pre_render_inquiry_form'));
      add_filter('comment_form_defaults', array('denali_theme', 'comment_form_defaults'));

      add_filter('wpp_property_page_vars', array('denali_theme', 'wpp_page_vars'));
      add_filter('wpp_overview_page_vars', array('denali_theme', 'wpp_page_vars'));
      add_filter('wpp_overview_shortcode_vars', array('denali_theme', 'wpp_page_vars'));
      add_filter('get_template_part_header', array('denali_theme', 'get_template_part_header'));

      // Option is used only when W3 Total Cache is activated.
      add_action('wp_ajax_denali_delete_option_clearcache', array('denali_theme', 'delete_option_clearcache'));
      add_action('wp_ajax_denali_actions', array('denali_theme', 'ajax_actions'));

      add_action('admin_enqueue_scripts', array('denali_theme', 'admin_enqueue_scripts'));

      //** Contextual Help */
      add_action('denali_contextual_help', array(__CLASS__, 'denali_contextual_help'));

      //** Adds HOOK to update themes */
      add_filter('site_transient_update_themes', array('denali_theme', 'check_denali_updates'));

      if ( class_exists('WP_CRM_Core') ) {

        add_filter('wp_crm_notification_actions', array('denali_theme', 'crm_notificaitons_filters'));
        add_filter('wp_crm_notification_info', array('denali_theme', 'crm_notification_info'), 0, 2);

        /* Contextual Help for CRM */
        add_filter('crm_page_wp_crm_settings_help', array('denali_theme', 'wp_crm_contextual_help'));

      }

      // Set up menus for theme
      register_nav_menus(
        array(
          'header-menu' => __( 'Header Menu' , 'denali'),
          'header-sub-menu' => __( 'Header Sub-Menu' , 'denali'),
          'footer-menu' => __( 'Footer Menu' , 'denali'),
          'bottom_of_page_menu' => __( 'Bottom of Page Menu' , 'denali')
        )
      );

      // Load defaults on theme activation
      denali_theme::do_on_activation();

      // Add 'Clear W3 Total Cache' notice
      add_action('admin_notices', array('denali_theme', 'show_clear_W3_total_cache_notice'));

      if(!is_admin()) {

        denali_theme::init_level_front_end();

      } else {
        wp_register_script('denali-admin-js',  get_bloginfo('template_url') . '/js/denali-admin.js', '', Denali_Version, true);
        wp_register_script('denali-equalheights-js',  get_bloginfo('template_url') . '/js/jquery.equalheights.js', '', Denali_Version, true);
        wp_enqueue_style('denali-admin-style', get_bloginfo('template_url') . '/css/denali-admin.css',array(), Denali_Version,'screen');

      }

      add_filter( 'wp_page_menu_args', array('denali_theme','add_home_to_menu_page_selection' ));
      add_filter( 'wp_page_menu', array('denali_theme','add_some_html_to_page_menu' ), 0, 2);

      add_image_size('agent_image', 120,120,true);

      add_filter('wpp_agent_widget_image_size', create_function('','return "agent_image"; '));

    }


    /**
     * Adds compatibility for all old WPP versions
     *
     * @author peshkov@UD
     * @since 3.2
     */
    static function legacy_compatibility() {
      global $wp_properties;

      //* frontend_property_stats was added to WPP 2.0 */
      if( !isset( $wp_properties[ 'frontend_property_stats' ] ) ) {
        $wp_properties[ 'frontend_property_stats' ] = $wp_properties[ 'property_stats' ];
      }

    }


   /**
    * Front-end bound actions ran at init level
    *
    * Loads all local and remote assets, checks conditionally loaded assets, etc.
    *
    * @since Denali 3.0.0
    *
    */
    static function init_level_front_end() {
      global $denali_theme_settings, $ds, $wp_properties;

      //** Do not load assets on login page */
      if(basename($_SERVER['PHP_SELF']) == 'wp-login.php') {
        return;
      }

      if(!class_exists('WPP_F')) {
        return;
      }

      $protocol = (is_ssl() ? 'https://' : 'http://');

      $subset = (!defined('WPLANG') || WPLANG=='' || WPLANG=='en_US') ? '' : '&subset='.implode ( ',' , (array)apply_filters('google_font_subset' , array('latin','cyrillic','greek') ) ) ;
      //* Identify which assets are remote (they will be checked for existance prior to sending to browser */


      $remote_assets['script']['google-maps'] = 'maps.google.com/maps/api/js?sensor=true';

      //** Check and Load Remote Styles */
      if(is_array($remote_assets['css'])) {
        foreach($remote_assets['css'] as $asset_handle => $remote_asset) {
          if(WPP_F::can_get_script($protocol.$remote_asset)) {
            wp_enqueue_style($asset_handle, $protocol.$remote_asset);
          }
        }
      }

      //** Check and Load Remote Scripts */
      if(is_array($remote_assets['script'])) {
        foreach($remote_assets['script'] as $asset_handle => $remote_asset) {
          if(WPP_F::can_get_script($protocol.$remote_asset)) {
            wp_enqueue_script( $asset_handle, $protocol.$remote_asset);
          }
        }
      }

      //** Load local stuff */
      wp_enqueue_script( 'jquery-ui-tabs');
      wp_enqueue_script( 'google-masonry',  get_bloginfo('template_url') . '/js/jquery.masonry.min.js', '', '', true);
      wp_enqueue_script( 'equalheights',  get_bloginfo('template_url') . '/js/jquery.equalheights.js', '', '', true);
      wp_enqueue_script( 'denali-global-js',  get_bloginfo('template_url') . '/js/denali-global-js.js', '', '', true);

      if ( is_singular() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
      }

      // Load child theme style.css if exists
      if(file_exists( STYLESHEETPATH . '/style.css') ) {
        wp_enqueue_style('denali-style', get_bloginfo('stylesheet_directory') . '/style.css',array('wp-property-frontend'),Denali_Version,'screen');
      } else {
        wp_enqueue_style('denali-style', get_bloginfo('template_url') . '/style.css',array('wp-property-frontend'),Denali_Version,'screen');
      }

      //** Responsive CSS */
      if ( !is_IE( '8' ) && !is_IE( '7' ) ) {
        if(file_exists( STYLESHEETPATH . '/css/denali-responsive.css') ) {
          wp_enqueue_style('denali-responsive', get_bloginfo('stylesheet_directory') . '/css/denali-responsive.css', array('denali-style'), Denali_Version );
        } else {
          wp_enqueue_style('denali-responsive', get_bloginfo('template_url') . '/css/denali-responsive.css' ,array('denali-style'), Denali_Version );
        }
      }

      if(file_exists( STYLESHEETPATH . '/print.css') ) {
        wp_enqueue_style('denali-print', get_bloginfo('stylesheet_directory') . '/print.css','',Denali_Version,'print');
      } elseif ( file_exists( TEMPLATEPATH . '/print.css') ) {
        wp_enqueue_style('denali-print', get_bloginfo('template_url') . '/print.css','',Denali_Version,'print');
      }

      //** Allow color switching via URL */
      if($wp_properties['configuration']['developer_mode'] == 'true'  && isset($_GET['color_scheme'])) {
        $denali_theme_settings['color_scheme'] = $_GET['color_scheme'];
      }

       // Load a custom color scheme if set
      if (!empty($denali_theme_settings['color_scheme'])) {
        if(file_exists( STYLESHEETPATH . "/{$denali_theme_settings['color_scheme']}")) {
          wp_enqueue_style('denali-colors', get_bloginfo('stylesheet_directory') . "/{$denali_theme_settings['color_scheme']}",array('denali-style'),'1.04','screen');
        } elseif(file_exists( TEMPLATEPATH . "/{$denali_theme_settings['color_scheme']}") ) {
          wp_enqueue_style('denali-colors', get_bloginfo('template_url') . "/{$denali_theme_settings['color_scheme']}",array('denali-style'),'1.04','screen');
        }

        //** Add color scheme class to body element */
        add_filter('body_class', create_function('$classes,$class,$new_class=' . 'denali_skin_' . str_replace(array('.', '-', ' '), '_', $denali_theme_settings['color_scheme']) , ' $classes[] = $new_class; return $classes; '), 0, 3);

      } else {

        //** Add body class to indiciate that we are not using a custom style  */
        add_filter('body_class', create_function('$classes,$class,$new_class=' . 'denali_skin_default' , ' $classes[] = $new_class; return $classes; '), 0, 3);

      }

      // Load scripts and styles for IE
      if(isset($is_IE) && $is_IE) {
        wp_enqueue_style('niceforms-default', get_bloginfo('template_url') . '/css/niceforms-default.css','',Denali_Version,'screen');
        wp_enqueue_script( 'html5', get_bloginfo('template_url') . '/js/html5.js');
        $wp_styles->add_data( 'niceforms-default', 'conditional', 'lt IE 7' );
      }

    }

  /**
    * Load global vars for header template part.
    *
    * @since Denali 3.0.0
    */
   static function get_template_part_header($current) {
    global $denali_theme_settings, $wp_query;


    //** $denali_header_links from filter which was set by different sections that will be in header drpdowns */
    $denali_header_links = apply_filters('denali_header_links', false);

    if(empty($denali_header_links)) {
      $denali_header_links = array();
    }

    $wp_query->query_vars['denali_header_links'] = $denali_header_links;

    return $current;

   }


  /**
   * Enqueue Scripts on Theme Options Page
   *
   * @since 3.0.0
   */
  function admin_enqueue_scripts($hook) {

    $screen = get_current_screen();

    switch( $screen->id ) {
      case 'appearance_page_functions':
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-cookie');
        wp_enqueue_script('wp-property-backend-global');
        wp_enqueue_script('denali-equalheights-js');
        wp_enqueue_script('denali-admin-js');
        break;
    }

  }



  /**
     * Add contextual help data when WPI and CRM installed
     *
     * @param type $data
     * @return array
     * @author korotkov@ud
     */
    function wp_crm_contextual_help( $data ) {

      $data['Denali Theme Integration'][] = '<h3>' . __('Inquiry', 'denali') .'</h3>';
      $data['Denali Theme Integration'][] = '<p>' . __('WP-CRM Shortcode forms can be used to display property inquries. Please visit <b>CRM -> Settings -> Shortcode Forms</b>.', 'denali') . '<br/>';
      $data['Denali Theme Integration'][] = __('Also, you can use WP-CRM plugin for sending Inquire notifications. Please visit <b>CRM -> Settings -> Notifications</b> to add notification.', 'denali') . '</p>';
      $data['Denali Theme Integration'][] = '<p>' . __('You can use next additional shortcodes in your notification:','denali') . '<br/>';
      $data['Denali Theme Integration'][] = '<b>[agent_email]</b> -' . __('renders all assigned to property agent emails (You should allow to send inquire to agent. See agent\'s profile). You must have "Real Estate Agents" premium feature to use this shortcode.','denali') . '<br/>';
      $data['Denali Theme Integration'][] = '<b>[property_link]</b> - ' . __('renders clickable link with property title','denali') . '</br>';
      $data['Denali Theme Integration'][] = '<b>[property_title]</b> - ' . __('renders property\'s title','denali') . '</p>';

      return $data;
    }

   /**
   * Denali Contextual Help
   *
   * @global type $current_screen
   * @param type $args
   * @author korotkov@ud
   */
  function denali_contextual_help( $args = array() ) {

    $defaults = array(
      'contextual_help' => array()
    );

    extract( wp_parse_args( $args, $defaults ) );

    //** If method exists add_help_tab in WP_Screen */
    if(is_callable(array('WP_Screen','add_help_tab'))) {

      //** Loop through help items and build tabs */
      foreach ( (array) $contextual_help as $help_tab_title => $help) {

        //** Add tab with current info */
        get_current_screen()->add_help_tab(
          array(
            'id'      => sanitize_title( $help_tab_title ),
            'title'   => __( $help_tab_title, 'denali' ),
            'content' => implode("\n",(array)$contextual_help[$help_tab_title]),
          )
        );

      }

      //** Add help sidebar with More Links */
      get_current_screen()->set_help_sidebar(
        '<p><strong>' . __('For more information:', 'denali') . '</strong></p>' .
        '<p>' . __('<a href="https://usabilitydynamics.com/products/the-denali-premium-theme/" target="_blank">The Denali Premium theme Page</a>', 'denali') . '</p>'
      );

    } else {
      //** If WP is out of date */
      global $current_screen;
      add_contextual_help($current_screen->id, '<p>'.__('Please upgrade WordPress to the latest version for detailed help.', 'denali').'</p><p>'.__('Or visit <a href="https://usabilitydynamics.com/products/the-denali-premium-theme/" target="_blank">The Danali Premium Theme</a> on UsabilityDynamics.com', 'denali').'</p>');
    }
  }


  /**
    * Load denali specific vars into query_vars to be used in templates.
    *
    * Loaded into query_vars on overview and single property pages via 'wpp_overview_page_vars' and 'wpp_property_page_vars' hooks
    *
    * @since Denali 2.7.0
    */
   static function wpp_page_vars($current) {
    global $denali_theme_settings, $page;

    //** Load denali global settings on all WPP pages */
    $current['denali_theme_settings'] = $denali_theme_settings;
    $current['page'] = $page;
    $current['paged'] = $paged;

    return $current;

   }

  /**
    * Add "Theme Options" link to admin bar.
    *
    * @since Denali 2.1
    */
   static function admin_bar_menu($wp_admin_bar) {

    if ( ! current_user_can('switch_themes') && ! current_user_can( 'edit_theme_options' ) ) {
      return;
    }

    $wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'theme-options', 'title' => __('Theme Options','denali'), 'href' => admin_url('themes.php?page=functions.php') ) );
    $wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'setup-assistant', 'title' => __('Setup Assistant','denali'), 'href' => admin_url('themes.php?page=functions.php&action=first_time_setup') ) );

   }

  /**
   * Denali-specific ajax actions
   *
   * @uses WP-CRM
   * @since Denali 1.0
   */
  static function crm_notificaitons_filters($actions) {
    $actions['denali_header_contact_form'] = __('Denali Header Contact','denali');
    return $actions;
  }

  /**
   * Apply additional notification info
   *
   * @uses WP-CRM class_contact_messages::process_crm_message()
   * @param integer $post_id
   * @param array $notification_info
   * @since Denali 3.0
   * @author Maxim Peshkov
   */
  function crm_notification_info($notification_info = array(), $post_id = false) {
    global $wpdb;

    $notification_info['agent_email'] = "";
    $notification_info['property_link'] = "";
    $notification_info['property_title'] = "";

    if(empty($post_id)) {
      return $notification_info;
    }
    $post_id = (int)$post_id;

    /** Bail if message is not about a property */
    if($wpdb->get_var("SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = '{$post_id}'") != 'property') {
      return $notification_info;
    }
    $post = get_property($post_id);

    /** Set 'property' */
    $notification_info['property_link'] = "<a target=\"_blank\" href=\"".get_permalink($post_id)."\">{$post['post_title']}</a>";
    $notification_info['property_title'] = $post['post_title'];

    /** Determine if 'Real Estate Agents' premium feature exists and activated */
    if(!class_exists('class_agents')) {
      return $notification_info;
    }
    /** Try to set agent_email(s) */
    if(!empty($post['wpp_agents'])) {
      $agent_email = array();
      foreach((array)$post['wpp_agents'] as $agent_id) {
        /** Does agent accept notifications? */
        if(get_user_meta($agent_id, 'notify_agent_on_property_inquiry', true) != 'on') {
          continue;
        }
        $agent = get_userdata($agent_id);
        $agent_email[] = $agent->user_email;
      }
      $agent_email = implode(',',$agent_email);
      $notification_info['agent_email'] = $agent_email;
    }

    return $notification_info;
  }

  /**
   * Denali-specific ajax actions
   *
   * @since Denali 1.0
   */
  static function ajax_actions() {
    global $denali_theme_settings;

    if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'denali_actions'))
      return;

     switch($_REQUEST['denali_action']) {

      case 'delete_logo':

        unset($denali_theme_settings['logo']);
        update_option('denali_theme_settings', $denali_theme_settings);
        echo json_encode(array('success' => 'true'));

      break;

     }


    die();
  }

     /**
     * Add checkbox to property editing page to allow comments
     *
      * @since Denali 1.0
     */
   static function add_home_to_menu_page_selection($args) {
    $args['show_home'] = true;

    return $args;
  }
   /**
    * Makes page menu looks more like custom menu
    * @param type $menu
    * @param type $args
    * @return string
    * @author odokienko@UD
    */
   static function add_some_html_to_page_menu($menu, $args) {

    if (preg_match("~<div class=\"{$args['menu_class']}\"><ul>(.*?)</ul></div>~im", $menu, $matches)) {

      $menu = sprintf($args['items_wrap'], 'menu-' . $args['menu_class'], esc_attr($args['menu_class']), $matches[1]);

      if ($args['container']) {
        $allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', array('div', 'nav'));
        if (in_array($args['container'], $allowed_tags)) {
          $show_container = true;
          $class = $args['container_class'] ? ' class="' . esc_attr($args['container_class']) . '"' : ' class="menu-' . $args['menu_class'] . '-container"';
          $id = $args['container_id'] ? ' id="' . esc_attr($args['container_id']) . '"' : '';
          $nav_menu .= '<' . $args['container'] . $id . $class . '>';
        }
      }

      if ($show_container) {
        $menu = $nav_menu . $menu . '</' . $args['container'] . '>';
      }
    }

    return $menu;
  }



/**
  * Saves extra setting when objects are saved.
  *
  * @since Denali 3.0.0
  */
  static function save_post( $post_id, $post = false ) {

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
      return $post_id;
    }

    if( wp_is_post_revision( $post_id ) ) {
      return;
    }

    if( !empty( $post ) && $post->status == 'auto-draft' ) {
      return;
    }

    if( !get_post_type_object( $post->post_type )->public ) {
      return;
    }

    //** Return if user cannot edit post. */
    if( ! current_user_can( 'edit_post', $post_id ) ){
      return;
    }

    update_post_meta($post_id, 'hide_header', $_POST['hide_header']);
    update_post_meta($post_id, 'hide_sidebar', $_POST['hide_sidebar']);
    update_post_meta($post_id, 'hide_page_title', $_POST['hide_page_title']);
    update_post_meta($post_id, 'hide_default_google_map', $_POST['hide_default_google_map']);

  }

/**
  * Add checkbox to property editing page to allow comments
  *
  * @since Denali 1.0
  */
  static function publish_metabox_options() {
    global $post, $denali_theme_settings;

    $hide_header = get_post_meta($post->ID, 'hide_header', true);
    $hide_sidebar = get_post_meta($post->ID, 'hide_sidebar', true);
    $hide_page_title = get_post_meta($post->ID, 'hide_page_title', true);
    $hide_default_google_map = get_post_meta($post->ID, 'hide_default_google_map', true);

    echo '<div class="misc-pub-section"><ul>';

    echo "<li> " . UD_UI::checkbox("name=hide_header&label=".__('Do not show header area.', 'denali') ."&value=true", ($hide_header == 'true' ? true : false)) . "</li>";
    echo "<li> " . UD_UI::checkbox("name=hide_sidebar&label=".__('Do not show sidebar.', 'denali') ."&value=true", ($hide_sidebar == 'true' ? true : false)) . "</li>";

    if($post->post_type == 'page') {
      echo "<li> " . UD_UI::checkbox("name=hide_page_title&label=".__('Do not show page title.', 'denali') ."&value=true", ($hide_page_title == 'true' ? true : false)) . "</li>";
    }

    if($post->post_type == 'property') {

      echo "<li> " . UD_UI::checkbox("name=hide_default_google_map&label=".__('Do not show location map.', 'denali') ."&value=true", ($hide_default_google_map == 'true' ? true : false)) . "</li>";

      if($denali_theme_settings['show_property_comments'] == 'true') {
        echo "<li> " . UD_UI::checkbox("name=comment_status&label=".__('Allow property comments.', 'denali') ."&value=open", ($post->comment_status == 'open' ? true : false)) . "</li>";
      } else {
        echo "<li> " . UD_UI::checkbox("name=comment_status&label=".__('Allow property inquiries.', 'denali') ."&value=open", ($post->comment_status == 'open' ? true : false)) . "</li>";
      }


    }

    echo '</ul></div>';

  }


 /**
  * Load default settings on theme activation
  *
  * @since Denali 1.0
  */
 static function do_on_activation(){
  global $pagenow, $wp_properties, $denali_theme_settings;

  if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {

    // Update version
    update_option('denali_version', Denali_Version);

    // Load default settings
    $temp_ds = stripslashes_deep(get_option('denali_theme_settings'));
    if(empty($temp_ds)) {
      $first_time_setup = true;
      denali_theme::load_defaults();
    }

    $wp_properties['image_sizes']['overview_thumbnail']['width'] = 200;
    $wp_properties['image_sizes']['overview_thumbnail']['height'] = 140;

    $wp_properties['configuration][property_overview][thumbnail_size'] = 'overview_thumbnail';
    $wp_properties['configuration][feature_settings][slideshow][glob][image_size'] = 'home_page_slideshow';

    $wp_properties['image_sizes']['sidebar_gallery']['width'] = 270;
    $wp_properties['image_sizes']['sidebar_gallery']['height'] = 180;

    update_option('wpp_settings', $wp_properties);
    $wp_properties_db = stripslashes_deep(get_option('wpp_settings'));
    $wp_properties = array_merge($wp_properties, $wp_properties_db);

    if(current_theme_supports('custom_background')) {
      if(get_theme_mod('background_image_thumb') == 'BACKGROUND_IMAGE'
        || get_theme_mod('background_image_thumb') == '') {
        // Set settings for default background
        set_theme_mod('background_repeat', false);
        set_theme_mod('background_position_x', 'center');
        set_theme_mod('background_position_y', 'top');
      }
    }

    // Get latest premium features
    if(class_exists('WPP_F')) {
      WPP_F::feature_check(true);
      if($first_time_setup) {
        //** Run first time setup if not settings found for Denali */
        wp_redirect(admin_url('themes.php?page=functions.php&action=first_time_setup'));
      }
    }



  }
  return $wp_properties;
  }


  /**
   * Loads defaults on activation if no settings exist.
   *
   * @todo May make sense to remove function and have setup assistant handle it all.
   *
   * @since Denali 1.71
   *
   */
  static function load_defaults() {
    global $wpdb, $denali_theme_settings, $wp_properties;

    // Default pages
    $denali_theme_settings["options_explore"] = 'pages';

    // Default data
    $denali_theme_settings['email'] = get_option('admin_email');
    $denali_theme_settings['email_from'] = get_option('admin_email');
    $denali_theme_settings['phone'] = $wp_properties['configuration']['phone_number'];

    // Save changes
    update_option('denali_theme_settings', $denali_theme_settings);
  }


  /**
    * Handles back-end theme configurations
    *
    * @since Denali 1.0
    *
    */
  static function admin_menu() {
    global $denali_theme_settings;
    $settings_page = add_theme_page(__('Theme Options','denali'), __('Theme Options','denali'), 'edit_theme_options', basename(__FILE__), array('denali_theme','options_page'));

    add_action('load-appearance_page_functions', array( __CLASS__, 'pre_load_appearance_page_functions' ));
  }


  /**
   * Inits additional data
   *
   * @global array $denali_theme_settings
   * @global object $wp_query
   * @global array $wp_properties
   * @since 3.2
   */
  static function template_redirect_before() {
    global $denali_theme_settings, $wp_query, $wp_properties;

    //** Load denali into global var on all pages. */
    $wp_query->query_vars['ds'] = $denali_theme_settings;
    $wp_query->query_vars['denali_theme_settings'] = $denali_theme_settings;
    $wp_query->query_vars['wp_properties'] = $wp_properties;

    //** Add Registration / Site Admin Link to header links */
    $function = create_function('$c', '
      if ( ! is_user_logged_in() ) {
        if ( get_option("users_can_register") ) {
          $c["register"]["id"] = "register_link";
          $c["register"]["class"] = "reg option_tab";
          $c["register"]["title"] = "'.__("Register","denali").'";
          $c["register"]["href"] = site_url("wp-login.php?action=register", "login");
        }
      } else {
        $c["register"]["id"] = "register_link";
        $c["register"]["class"] = "reg option_tab";
        $c["register"]["title"] = "'.__("Site Admin","denali").'";
        $c["register"]["href"] = admin_url();
      }
      return $c;
    ');

    add_filter('denali_header_links', $function, 100, 1);
  }


    /**
     * Primarystatic function for handling front-end actions
     *
     * @since 3.2
     */
    static function template_redirect_after() {
      global $denali_theme_settings;

      //** Show message if site is in maintanance mode (only for non-admins) */
      if( $denali_theme_settings['maintanance_mode'] == 'true' && !current_user_can('manage_options') ) {
        if(file_exists(STYLESHEETPATH . '/maintanance.php')) {
          include STYLESHEETPATH . '/maintanance.php';
          die();
        } else {
          include 'maintanance.php';
          die();
        }
      }

      if(is_posts_page()) {
        if(file_exists(STYLESHEETPATH . '/posts_page.php')) {
          load_template(STYLESHEETPATH . '/posts_page.php');
          die();
        } else {
          load_template(TEMPLATEPATH . '/posts_page.php');
          die();
        }
      }

      if(is_front_page()) {
        if(file_exists(STYLESHEETPATH . '/index.php')) {
          load_template(STYLESHEETPATH . '/index.php');
          die();
        } else {
          load_template(TEMPLATEPATH . '/index.php');
          die();
        }
      }

   }

   /**
    * Converts a value into a true/false boolean
    *
    * @author potanin@UD
    * @since 3.2
    */
    static function to_boolean( $value = false ) {

      switch( true ) {

        case strtolower( $value ) == 'no':
        case strtolower( $value ) == strtolower( __( 'no' ) ):
        case strtolower( $value ) == 'false':
        case empty( $value ):
          return false;
        break;

        case strtolower( $value ) == 'yes':
        case strtolower( $value ) == strtolower( __( 'yes' ) ):
        case strtolower( $value ) == 'true':
        case $value == true:
          return true;
        break;

      }

      return $value;
    }

    /**
     * Setup a default homage page
     *
     * Called by setup assistant.
     *
     * @since Denali 1.1
     */
    static function setup_default_home_page() {
      global $wpdb, $user_ID;

        //** Check if this page actually exists */
      $post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'home' AND post_type = 'page' AND post_status = 'publish'");

      if(!$post_id) {
        $post_id = '';
      } else {
        return $post_id;
      }

      $home_page_content[] = "<h2>Welcome to " . get_bloginfo('blogname') . "!</h2>";
      $home_page_content[] = "[property_search]";
      $home_page_content[] = "[property_overview per_page=5 pagination=off template=grid]";

      $property_page = array(
        'post_title' => __('Home', 'denali'),
        'post_content' => implode("\n", $home_page_content),
        'post_name' => 'home',
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_id' => $post_id,
        'post_author' =>  $user_ID
      );

      $post_id = wp_insert_post($property_page);


      return $post_id;

    }

    /**
     * Checks colors folder for available color scheemes
     *
     * Rerturns thumb URL if it exists.
     *
     * @since Denali 1.1
     */
   static function get_color_schemes() {

      $default_headers = array(
      'name' => __('Color Palette','denali_theme'),
      'description' => __('Description','denali_theme'),
      'author' => __('Author','denali_theme'),
      'version' => __('Version','denali_theme'),
      'tags' => __('Tags','denali_theme')
      );


    // Scan /colors/ folder for CSS files
    if ($handle = opendir(TEMPLATEPATH . '/')) {

      while (false !== ($file = readdir($handle))) {

        if ($file != "." && $file != ".." && strpos($file, 'skin-') === 0) {

          // Skip non-css files

          if(substr($file, strrpos($file, '.') + 1) != 'css') {
            continue;
          }

          $file_data = @get_file_data(TEMPLATEPATH . '/' . $file, $default_headers, 'denali_color_css' );

          if(!empty($file_data)) {

            $files[$file] = $file_data;

            //** Load scheme thumbnail if it exists */
             if(file_exists(TEMPLATEPATH . '/' . str_replace('.css', '.jpg', $file))) {
              $files[$file]['thumb'] = get_bloginfo('template_url')  . '/' . str_replace('.css', '.jpg', $file);
            }
          }

        }
      }
    }

    //** Look for color schemes in styleshet directory (if in child theme) */
    if ($handle = opendir(STYLESHEETPATH . '/')) {

      while (false !== ($file = readdir($handle))) {

        if ($file != "." && $file != ".." && strpos($file, 'skin-') === 0) {

          // Skip non-css files

          if(substr($file, strrpos($file, '.') + 1) != 'css')
            continue;

           $file_data = @get_file_data(STYLESHEETPATH . '/' . $file, $default_headers, 'denali_color_css' );

          if(!empty($file_data)){

            $files[$file] = $file_data;

            //** Load scheme thumbnail if it exists */
            if(file_exists(TEMPLATEPATH . '/' . str_replace('.css', '.jpg', $file))) {
              $files[$file]['thumb'] = get_bloginfo('template_url')  . '/' . str_replace('.css', '.jpg', $file);
            }
          }

        }
      }
    }

    $files = apply_filters('denali_extra_css_files', $files);

    if(!is_array($files))
      return false;

    return $files;
  }



  /**
   * Handles contact form ajax
   *
   * @since Denali 1.0
   */
  static function process_ajax_contact_form() {
    if(wp_verify_nonce($_REQUEST['nonce'], 'denali_contact_form')){
      $data['name'] = $_REQUEST['name'];
      $data['email'] = $_REQUEST['email'];
      $data['phone'] = $_REQUEST['phone'];
      $data['subject'] = __('Mesage From ','denali') . get_bloginfo();
      $data['message'] = $_REQUEST['message'];
      $result = denali_theme::submit_contact_form($data);
      echo json_encode($result);
    }
    die();
  }

  /**
   * Converts a text address into coordinates
   *
   * Run on Denali options update to validate blog owner's address for map on front-end.
   *
   * @since Denali 1.0
   */
  static function get_geodata( $address = false ){

    if( empty( $address ) ) {
      return false;
    }

    $url = str_replace(" ", "+" ,"http://maps.google.com/maps/api/geocode/json?address=" . urlencode( $address ) . "&sensor=true");

    $obj = ( json_decode( wp_remote_fopen( $url ) ) );

    if( $obj->status != "OK" ) {
      return false;
    }

    $results = $obj->results;
    $results_object = $results[0];
    if( !is_object( $results_object ) || !isset( $results_object->geometry ) || !isset( $results_object->geometry ) ) {
      return false;
    }

    $return = array(
      'formatted_address' => $results_object->formatted_address,
      'latitude' => $results_object->geometry->location->lat,
      'longitude' => $results_object->geometry->location->lng,
    );

    return (object)$return;
  }


  /**
   * Ran by ajaxstatic function to validate and send the contact message.
   *
   * @since Denali 1.0
   */
  static function submit_contact_form($data){
    global $denali_theme_settings;

    foreach($data as $entry) {
      $data[$entry] = trim($entry);
    }

    $data = stripslashes_deep($data);

    if($data['name'] === '') {
      $errors['name'] =  __('You forgot to enter your  name.', 'denali');
    }

    if($data['message'] === '') {
      $errors['message'] =  __('You forgot to enter a message.', 'denali');
    }

    /* Check to make sure that a valid email address is submitted */
    if($data['email'] === '')  {
      $errors['email'] = __('You forgot to enter your e-mail.', 'denali');
    } elseif (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", $data['email'])) {
      $errors['email'] = 'Please verify your email.';
    }

    if(!empty($errors)) {
      return array('success' => 'false', 'errors' => $errors);
    }

    $emailFrom = $denali_theme_settings['email_from'];

    $body = nl2br( "
      Name: {$data['name']}
      E-mail: {$data['email']}
      $phone
      Subject: {$data['subject']}
      - - - - - - - - - - - - - - - - -
      Message: {$data['message']}");

    $headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= 'From: '. $emailFrom. "\r\n" . 'Reply-To: ' . $emailFrom;

    if(function_exists('wp_crm_send_notification') &&
      $denali_theme_settings['wp_crm']['header_contact'] != 'denali_default_header_form'
    ) {
      $notification_info['message_content'] = stripslashes($message);
      $mailed = wp_crm_send_notification('denali_header_contact_form',$notification_info);
    } else {
      if(!wp_mail($denali_theme_settings['email'], $data['subject'], $body, $headers)) {
        return array('success' => 'false', 'errors' => array('mail' => __('Error with sending message. Please contact site administrator.', 'denali')));
      }
    }
    return array('success' => 'true');
  }

    /**
     * Draw the custom site background
     *
     * Run on Denali options update to validate blog owner's address for map on front-end.
     *
     * @since Denali 1.0
     */

   static function custom_background() {
    $background = get_background_image();
    $color = get_background_color();

   if ( ! $background && ! $color )
      return;

    $style = $color ? "background-color: #$color;" : '';

    $image = " background-image: url('$background');";

    $repeat = get_theme_mod( 'background_repeat', 'no-repeat' );

    if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
      $repeat = 'no-repeat';

    $repeat = " background-repeat: $repeat;";

    $position = get_theme_mod( 'background_position_x', 'left' );
    if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
      $position = 'center';

    $position = " background-position: top $position;";

    $attachment = get_theme_mod( 'background_attachment', 'scroll' );
    if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
    $attachment = 'scroll';
    $attachment = " background-attachment: $attachment;";

    $style .= $image . $repeat . $position . $attachment;

    ?>
    <style type="text/css">
    div.wrapper { <?php echo trim( $style ); ?> }
    </style>
    <?php

    }

  /**
   * Display area for background image in back-end
   *
   *
   * @since Denali 1.2
   */
  function admin_image_div_callback() { ?>

    <h3><?php _e('Background Image','denali'); ?></h3>
    <table class="form-table">
    <tbody>
    <tr valign="top">
    <th scope="row"><?php _e('Preview','denali'); ?></th>
    <td>
    <?php
    $background_styles = '';
    if ( $bgcolor = get_background_color() )
      $background_styles .= 'background-color: #' . $bgcolor . ';';

    if ( get_background_image() ) {
      // background-image URL must be single quote, see below
      $background_styles .= ' background-image: url(\'' .  get_background_image() . '\');'
        . ' background-repeat: ' . get_theme_mod('background_repeat', 'no-repeat') . ';'
        . ' background-position: top ' . get_theme_mod('background_position_x', 'left');
    }
    ?>
    <div id="custom-background-image" style=" min-height: 200px;<?php echo $background_styles; ?>"><?php // must be double quote, see above ?>

    </div>
    <?php

  }

  /**
   * Setups up core theme functions
   *
   * Adds image header section and default headers
   *
   * @since Denali 1.2
   */
  static function after_setup_theme() {
    global $wp_properties, $denali_theme_settings, $pagenow, $_wp_theme_features;

    /** Determine if WP-Property plugin is not activated and try to switch theme to another one in this way. peshkov@UD */
    if( !class_exists('WPP_F')) {
      /** Be sure that we don't update anything now and it's admin panel! */
      if( is_admin() && $pagenow != 'update.php' ){
        $themes = get_themes();
        foreach($themes as $k => $v) {
          if(in_array($k, array("Twenty Eleven","Twenty Ten"))) {
            switch_theme($v['Template'], $v['Stylesheet']);
          }
        }
        add_action('admin_notices', array('denali_theme', 'show_wpp_error_notice'));
      }
      /** Show maintanance mode on frontend if plugin is deactivated. */
      elseif ( !is_admin() ) {
        if(file_exists(STYLESHEETPATH . '/maintanance.php')) {
          include STYLESHEETPATH . '/maintanance.php';
          die();
        } else {
          include 'maintanance.php';
          die();
        }
      }
    }


    //** Load all needed libraries */
    self::load_files( TEMPLATEPATH . '/libs/' );

    load_theme_textdomain('denali', get_template_directory() . '/languages');

    add_action( 'widgets_init', array('denali_theme', 'widgets_init'));
    add_action( 'after_switch_theme', array('denali_theme', 'after_switch_theme'), 0 );

    add_editor_style();
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-background' );

    // These can be disabled via UI, or child theme (if so, there will be no option in UI to toggle)
    add_theme_support( 'home_page_attention_grabber_area' );
    add_theme_support( 'post_page_attention_grabber_area' );
    add_theme_support( 'inside_attention_grabber_area' );
    add_theme_support( 'header-property-search' );
    add_theme_support( 'header-property-contact' );
    add_theme_support( 'header-login' );
    add_theme_support( 'header-card' );
    add_theme_support( 'footer_explore_block' );

    // Hooks used to add / remove theme_support
    do_action('denali_post_theme_support');

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    define('BACKGROUND_IMAGE', get_bloginfo('template_url') . '/img/back.png');

    $header_width = $wp_properties['image_sizes']['header_image']['width'];
    $header_height = $wp_properties['image_sizes']['header_image']['height'];

    if(empty($header_width)) {
      $header_width = 924;
    }

    if(empty($header_height)) {
      $header_height = 183;
    }

    // No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
    define( 'NO_HEADER_TEXT', true );
    define( 'HEADER_IMAGE', '%s/img/denali_default_home_header.jpg' );
    define( 'HEADER_IMAGE_WIDTH', apply_filters( 'denali_header_image_width',  $header_width) );
    define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'denali_header_image_height',    $header_height ) );
    add_custom_image_header( '', create_function('',''));

    // Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
    register_default_headers( array (
      'oceanfront' => array (
        'url' => '%s/img/headers/oceanfront.jpg',
        'thumbnail_url' => '%s/img/headers/oceanfront_thumb.jpg',
        'description' => __( 'Oceanfront', 'denali' )),
      'minneapolis' => array (
        'url' => '%s/img/headers/minneapolis.jpg',
        'thumbnail_url' => '%s/img/headers/minneapolis_thumb.jpg',
        'description' => __( 'Minneapolis', 'denali' )),
      'suburbs' => array (
        'url' => '%s/img/headers/suburbs.jpg',
        'thumbnail_url' => '%s/img/headers/suburbs_thumb.jpg',
        'description' => __( 'Cozy Suburbs', 'denali' )),
      'wilmington' => array (
        'url' => '%s/img/headers/wilmington.jpg',
        'thumbnail_url' => '%s/img/headers/wilmington_thumb.jpg',
        'description' => __( 'Wilmington', 'denali' ))
    ));


    if(current_theme_supports('custom-background')) {
     add_custom_background(array('denali_theme','custom_background'),'',array('denali_theme','admin_image_div_callback'));
    }

    do_action('denali_theme_setup');

    add_action('init', array('denali_theme', 'init'), 0);
  }


  /**
   * Includes all PHP files from specific folder
   *
   * @param string $dir Directory's path
   * @author peshkov@UD
   * @version 0.1
   */
  static function load_files ( $dir = '' ) {
    if ( !empty( $dir ) && is_dir( $dir ) ) {
      if ( $dh = opendir( $dir ) ) {
        while (($file = readdir($dh)) !== false) {
          if( !in_array( $file, array( '.', '..' ) ) && is_file( $dir . $file ) && 'php' == pathinfo( $dir . $file, PATHINFO_EXTENSION ) ) {
            include_once( $dir . $file );
          }
        }
        closedir( $dh );
      }
    }
  }


  /**
   *
   */
  function pre_load_appearance_page_functions(){
    $contextual_help['General settings'][] = '<h3>' . __('Denali Theme Help','denali') .'</h3>';
    $contextual_help['General settings'][] = '<p>' . __('Since version 3.0.0 much flexibility was added to page layouts by adding a number of conditional Tabbed Widget areas which are available on all the pages.', 'denali') .'</p>';

    $contextual_help['General settings'][] = '<h3>' . __('Home & Posts Pages', 'denali') .'</h3>';
    $contextual_help['General settings'][] = '<p>' . sprintf(__('<b>Posts Page</b> is typically used to display the <b>blog</b> part of site when WordPress is used as a CMS. You can configure the posts page on the <a href="%1s">Settings -> Reading</a> settings page.', 'denali'), admin_url("options-reading.php")) .'</p>';
    $contextual_help['General settings'][] = '<p>' . __('The <b>Property Search</b> widget area is hidden automatically when no widget exists in the area.', 'denali') .'</p>';


    $contextual_help['General settings'][] = '<h3>' . __('Color Schemes', 'denali') .'</h3>';
    $contextual_help['General settings'][] = '<p>' . sprintf(__('If you want to customize colors, it is advisable to create a separate a color palette within a child theme. Please visit <a href="">WP-Property & Denali Help</a> to learn more about this', 'denali'), 'http://usabilitydynamics.com/help/') .'</p>';

    $contextual_help['General settings'][] = '<h3>' . __('General Enhancements', 'denali') .'</h3>';
    $contextual_help['General settings'][] = '<p>' . __('<b>Automatically Hide Widgets:</b> After a page is rendered on the front-end, any widgets that do not have any text, excluding the title, will be hidden automatically.', 'denali') .'</p>';

    $contextual_help['General settings'][] = '<h3>' . __('Child Theme', 'denali') .'</h3>';
    $contextual_help['General settings'][] = '<p>' . __('To install child theme automatically you should just check \'Install Denali child theme\'.', 'denali') .'</p>';

    $contextual_help['General settings'][] = '<p>' . __('Child theme will be created automatically. You have to make any code customizations in created child theme folder ( <b>themes/denali-child</b> ) to avoid losing your changes on upgrade.','denali') . '</p>';
    $contextual_help['General settings'][] = '<p>' . __('Check <a target="_blank" href="http://codex.wordpress.org/Child_Themes">Wordpress Codex</a> for more information.', 'denali') . '</p>';

    $contextual_help['General settings'][] = '<p>' . __('If you have the problems with automatically installing of child theme, you may need to install it manually:', 'denali');
    $contextual_help['General settings'][] = '<br/>' . __('Copy folder <b>child-theme</b> from your Denali theme\'s folder to themes root directory ( wp-content/themes ). And child theme will be available.', 'denali') .'</p>';

    $contextual_help['Header'][] = '<h3>' . __('Header Settings', 'denali') .'</h3>';
    $contextual_help['Header'][] = '<p>' . sprintf(__('If you would like to change the header images, please visit the <a href="%1s">Appearance -> Header</a> page.', 'denali'), admin_url("themes.php?page=custom-header")) .'</p>';

    $contextual_help['Header'][] = '<p>' . sprintf(__('The navigational menus are configured are on the  <a href="%1s">Appearance -> Menus</a> page.', 'denali'), admin_url("nav-menus.php")) .'</p>';
    $contextual_help['Header'][] = '<p>' . sprintf(__('And be sure to configure the widgets on the <a href="%1s">Appearance -> Widgets </a> page.', 'denali'), admin_url("widgets.php")) .'</p>';
    $contextual_help['Header'][] = '<h3>' . __('Header Logo','denali') .'</h3>';
    $contextual_help['Header'][] = '<p>' . __('It is recommended the header logo is under 100 pixels tall.', 'denali') .'</p>';

    $contextual_help['Header'][] = '<p>' . __('Address: The address will be automatically converted into coordinates, and a Google Map will be displayed on the top of every page in the <b>Contact Us</b> dropdown.', 'denali') .'</p>';

    $contextual_help['Header'][] = '<h3>' . __('Header Property Search','denali') .'</h3>';
    $contextual_help['Header'][] = '<p>' . __('By default the header property search widget area, and the home page slideshow overlay area were the same widget area, <b>Header &amp; Home: Property Search</b>. This setting can be changed on within the Header tab, and two new widget areas will be created to differentiate between the two.', 'denali') .'</p>';

    $contextual_help['Footer'][] = '<h3>' . __('Footer','denali') .'</h3>';
    $contextual_help['Footer'][] = '<p>' . __('The footer includes several elements - <b>Footer: Bottom Left Block</b> widget, <b>Explore Block</b>, <b>phone number</b>, <b>site tagline</b>, <b>social media icons</b>, <b>Equal Housing Opportunity logo</b>, and <b>copyright notice.</b>, ', 'denali') .'</p>';

    $contextual_help['Help'][] = '<h3>' . __('Backup','denali') .'</h3>';
    $contextual_help['Help'][] = '<p>' . __('You can download Backup of Denali theme settings, <a target="_blank" href="../wp-admin/nav-menus.php">Menus</a> settings (optional) and <a target="_blank" href="../wp-admin/widgets.php">Widgets</a> settings (optional) to file and then, in any time, restore the settings from that Backup file.', 'denali') .'</p>';
    $contextual_help['Help'][] = '<p><b>' . __('Note:', 'denali') . '</b>';
    $contextual_help['Help'][] = '<p><ul><li>' . __('Menus Settings: If element of menu has no related page or post (it doesn\'t exist) it will not be restored during backup.', 'denali') .'</li>';
    $contextual_help['Help'][] = '<li>' . __('Widgets Settings: If widget is not registered, it will not be restored during backup.', 'denali') .'</li>';

    //** Hook this action is you want to add info */
    $contextual_help = apply_filters('denali_theme_options_page_help', $contextual_help);

    do_action('denali_contextual_help', array('contextual_help'=>$contextual_help));
  }


  /**
   * Adds additional default options to theme settings array.
   *
   * @uses filter 'denali_theme_settings'
   * @author peshkov@UD
   */
  static function denali_theme_settings($denali_theme_settings) {

    $denali_theme_settings['stats_icons'] = array(
      'icon-price', 'icon-area', 'icon-cold',
      'icon-bed', 'icon-bath', 'icon-cars',
      'icon-cal', 'icon-fire', 'icon-storage',
      'icon-eye', 'icon-leaf', 'icon-marker',
      'icon-pet', 'icon-lamp', 'icon-shower',
      'icon-pool', 'icon-heat', 'icon-house',
      'icon-wifi', 'icon-pot', 'icon-sun',
      'icon-tools', 'icon-phone', 'icon-fb',
      'icon-tw', 'icon-tw2', 'icon-rss',
      'icon-skype', 'icon-email', 'icon-li',
      'icon-goopl',
    );

    if( !isset( $denali_theme_settings['property_overview_attributes']['stats_icons'] ) ) {
      $denali_theme_settings['property_overview_attributes']['stats_icons'] = array(
        'icon-price' => 'price',
        'icon-bed' => 'bedrooms',
        'icon-bath' => 'bathrooms',
        'icon-marker' => 'location',
        'icon-phone' => 'phone_number',
      );
    }

    if( !isset( $denali_theme_settings['property_overview_attributes']['stats_by_icon'] ) ) {
      $denali_theme_settings['property_overview_attributes']['stats_by_icon'] = array(
        'icon-price', 'icon-bed', 'icon-bath',
        'icon-marker', 'icon-phone',
      );
    }


    return $denali_theme_settings;
  }


  /**
   * Adds a widget to a sidebar.
   *
   * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
   *
   * Example usage:
   * denali_theme::add_widget_to_sidebar('global_property_search', 'text', array('title' => 'Automatically Added Widget', 'text' => 'This widget was added automatically'));
   *
   * @todo Some might exist that adds widgets twice.
   * @todo Consider moving functionality to UD_F
   *
   * @since Denali 1.0
   */
  static function add_widget_to_sidebar($sidebar_id = false, $widget_id = false, $settings = array(), $args = '') {
    global $wp_registered_widget_updates, $wp_registered_widgets;

    $defaults = array(
      'do_not_duplicate' => 'true'
    );

    extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

    require_once(ABSPATH . 'wp-admin/includes/widgets.php');

    do_action('load-widgets.php');
    do_action('widgets.php');
    do_action('sidebar_admin_setup');

    //** Need some validation here */
    if(!$sidebar_id) {
      return false;
    }

     if(!$widget_id) {
      return false;
    }

    if(empty($settings)) {
      return false;
    }

    //** Load sidebars */
    $sidebars = wp_get_sidebars_widgets();

    //** Get widget ID */
    $widget_number  = next_widget_id_number($widget_id);

    if(is_array($sidebars[$sidebar_id])) {
      foreach($sidebars[$sidebar_id] as $this_sidebar_id => $sidebar_widgets) {

        //** Check if this sidebar already has this widget */
        if(strpos($sidebar_widgets, $widget_id) === false) {
          continue;
        }

        $widget_exists = true;

      }
    }

    if($do_not_duplicate == 'true' && $widget_exists) {
      return true;
    }

    foreach ( (array) $wp_registered_widget_updates as $name => $control ) {

      if ( $name == $widget_id ) {
        if ( !is_callable( $control['callback'] ) )
          continue;

        ob_start();
          call_user_func_array( $control['callback'], $control['params'] );
        ob_end_clean();
        break;
      }
    }

    //** May not be necessary */
    if ( $form = $wp_registered_widget_controls[$widget_id] ) {
      call_user_func_array( $form['callback'], $form['params'] );
    }

    //** Add new widget to sidebar array */
    $sidebars[$sidebar_id][] = $widget_id . '-' . $widget_number;

    //** Add widget to widget area */
    wp_set_sidebars_widgets($sidebars);

    //** Get widget configuration */
    $widget_options = get_option('widget_' . $widget_id);

    //** Check if current widget has any settings (it shouldn't) */
    if($widget_options[$widget_number]) {
    }

    //** Update widget with settings */
    $widget_options[$widget_number] = $settings;

    //** Commit new widget data to database */
    update_option('widget_' . $widget_id, $widget_options);


    return true;

  }

  /**
   * Remove all instanced of a widget from a sidebar
   *
   * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
   *
   * @since Denali 1.0
   */
  function remove_widget_from_sidebar($sidebar_id, $widget_id) {
     global $wp_registered_widget_updates;


    //** Load sidebars */
    $sidebars = wp_get_sidebars_widgets();


    //** Get widget ID */

    if(is_array($sidebars[$sidebar_id])) {
      foreach($sidebars[$sidebar_id] as $this_sidebar_id => $sidebar_widgets) {

        //** Check if this sidebar already has this widget */

        if(strpos($sidebar_widgets, $widget_id) === 0 || $widget_id == 'all') {

          //** Remove widget instance if it exists */
          unset($sidebars[$sidebar_id][$this_sidebar_id]);

        }

      }
    }


    //** Save new siebars */
    wp_set_sidebars_widgets($sidebars);
   }

  /**
   * Displays first-time setup splash screen
   *
   * WPP widgets:
   * - searchpropertieswidget
   * - childpropertieswidget
   * - latestpropertieswidget
   * - gallerypropertieswidget
   * - featuredpropertieswidget
   * - agentwidget
   *
   * @todo Fix permalink rewrites.
   *
   * @since Denali 1.0
   */
  static function admin_init() {
    global $wp_properties, $wp_registered_widget_updates, $wpdb, $denali_theme_settings, $wp_registered_widgets, $wp_rewrite;;

    //** Check if child thme exists and updates denali_theme_settings accordingly */
    denali_theme::denali_child_theme_exists();

    //** Adds specific options to Edit Agent page, if agents premium feature installed. */
    add_filter( 'wpp::agents::agent::options', array( __CLASS__, 'wpp_agent_options' ), 0, 2 );

    if(!empty($_REQUEST['_wpnonce'])) {

    //** Save main Denali Settings */
    if(wp_verify_nonce($_REQUEST['_wpnonce'], 'denali_settings')) {

      //** Handle backup */
      if($backup_file = $_FILES['denali_theme_settings']['tmp_name']['settings_from_backup']) {
        $backup_contents = file_get_contents($backup_file);

        if( !empty( $backup_contents ) ) {
          $decoded_settings = json_decode($backup_contents, true);
        }
        if( !empty( $decoded_settings['denali_theme_settings'] ) ) {
          $_REQUEST['denali_theme_settings'] = $decoded_settings['denali_theme_settings'];
        } else {
          //** File's data is wrong. Redirect page to default Theme Settings page */
          wp_redirect(admin_url('themes.php?page=functions.php&message=backup_failed'));
          die();
        }

        /** Update Widgets from Denali Backup */
        if( !empty( $decoded_settings['widgets'] ) ) {
          foreach( (array)$decoded_settings['widgets']['sidebars_widgets'] as $sidebar => $widgets ) {
            foreach( (array)$widgets as $k => $widget ) {
              if( !isset( $decoded_settings['widgets']['wp_registered_widgets'][$widget]['class'] ) ||
                  !class_exists( $decoded_settings['widgets']['wp_registered_widgets'][$widget]['class'] )
              ) {
                unset( $decoded_settings['widgets']['sidebars_widgets'][$sidebar][$k] );
                continue;
              }
              $option_name = $decoded_settings['widgets']['wp_registered_widgets'][$widget]['option_name'];
              $number = $decoded_settings['widgets']['wp_registered_widgets'][$widget]['number'];
              $settings = get_option( $option_name );
              if( !$settings ) $settings = array();
              $settings[$number] = $decoded_settings['widgets']['wp_registered_widgets'][$widget]['settings'];
              update_option( $option_name, $settings );
            }
          }
          wp_set_sidebars_widgets($decoded_settings['widgets']['sidebars_widgets']);
        }

        /** Set Menus from Denali Backup */
        if( !empty( $decoded_settings['menus'] ) ) {
          /** Remove old menus before set menus from backup. */
          $nav_menus = wp_get_nav_menus();
          foreach( (array)$nav_menus as $nav_menu ) {
            wp_delete_nav_menu($nav_menu->term_id);
          }
          /** Reset All Menu Locations */
          $nav_menu_locations = array();
          foreach( (array)$decoded_settings['menus']['nav_menu_locations'] as $k => $v ) {
            $nav_menu_locations[$k] = 0;
          }
          //** */
          $menu_relations = array();
          //** Add Nav Menu from Backup */
          foreach( (array)$decoded_settings['menus']['nav_menus'] as $nav_menu ) {
            $_nav_menu_id = wp_update_nav_menu_object( 0, array(
              'menu-name' => $nav_menu['name'],
              'description' => $nav_menu['description'],
              'parent' => $nav_menu['parent'],
            ) );
            if ( ! is_wp_error( $_nav_menu_id ) ) {
              //** Update Nav Menu items */
              foreach( (array)$nav_menu['items'] as $item ) {
                //** Check if related ( to menu item ) post exists */
                if( $item['object'] != 'custom' ) {
                  $p = get_post($item['object_id']);
                  if( empty( $p ) ) continue;
                }
                $_nav_menu_item_id = wp_update_nav_menu_item( $_nav_menu_id, 0, array(
                  'menu-item-object-id' => $item['object_id'],
                  'menu-item-object' => $item['object'],
                  'menu-item-parent-id' => ( isset($menu_relations[$item['menu_item_parent']]) ? $menu_relations[$item['menu_item_parent']] : 0 ),
                  'menu-item-position' => $item['menu_order'],
                  'menu-item-type' => $item['type'],
                  'menu-item-title' => $item['post_title'],
                  'menu-item-url' => $item['url'],
                  'menu-item-description' => $item['post_content'],
                  'menu-item-attr-title' => $item['post_excerpt'],
                  'menu-item-target' => $item['target'],
                  'menu-item-classes' => implode(' ', (array)$item['classes']),
                  'menu-item-xfn' => $item['xfn'],
                  'menu-item-status' => $item['post_status'],
                ) );
                $menu_relations[$item['ID']] = $_nav_menu_item_id;
              }
              //** Set Menu Location */
              foreach( (array)$decoded_settings['menus']['nav_menu_locations'] as $location => $m ) {
                if( $nav_menu['term_id'] == $m ) {
                  $nav_menu_locations[$location] = $_nav_menu_id;
                }
              }
            }
          }
          //** Save new Menus Locations */
          set_theme_mod( 'nav_menu_locations', array_map( 'absint', $nav_menu_locations ) );
        }

      }

      $dts = array();
      foreach($_REQUEST['denali_theme_settings'] as $key => $value) {
          $dts[$key] = $value;
      }

      //* Set coordinates */
      $coordinates = denali_theme::get_geodata( $dts['address'] );
      $dts['longitude'] = $coordinates->longitude;
      $dts['latitude'] = $coordinates->latitude;

      //* Set logo */
      if( ! empty ($_FILES['logo']['name'])){
        $overrides = array ( 'test_form' => false );
        $file = wp_handle_upload($_FILES['logo'], $overrides);
        $dts['logo'] = $file['url'];
      } else {
        $dts['logo'] = $denali_theme_settings['logo'];
      }

      if($_REQUEST['denali_theme_settings']['install_denali_child_theme'] == 'true') {
        //** Install theme if it is not */
        if(denali_theme::install_child_theme()) {
          $dts['install_denali_child_theme'] = 'true';
        } else {
          $dts['install_denali_child_theme'] = 'false';
        }

      }

      update_option('denali_theme_settings', $dts);

      //** Update WP-Property Settings */
      $wp_properties_db = get_option('wpp_settings');
      if( !empty( $wp_properties_db ) && !empty( $_REQUEST['wpp_settings'] ) ) {
        $wp_properties_db['configuration']['property_overview']['thumbnail_size'] = $_REQUEST['wpp_settings']['configuration']['property_overview']['thumbnail_size'];
        $wp_properties_db['configuration']['property_overview']['show_children'] = $_REQUEST['wpp_settings']['configuration']['property_overview']['show_children'];
        $wp_properties_db['configuration']['property_overview']['fancybox_preview'] = $_REQUEST['wpp_settings']['configuration']['property_overview']['fancybox_preview'];
        $wp_properties_db['configuration']['bottom_insert_pagenation'] = $_REQUEST['wpp_settings']['configuration']['bottom_insert_pagenation'];
        update_option( 'wpp_settings', $wp_properties_db );
      }

      //** Redirect page to default Theme Settings page */
      wp_redirect(admin_url('themes.php?page=functions.php&message=settings_updated'));
      die();

    }

    //** Download backup of configuration */
    if($_REQUEST['action'] == 'download-denali-backup' && wp_verify_nonce($_REQUEST['_wpnonce'], 'download-denali-backup')) {
      $stylesheet = get_stylesheet();
      $sitename = sanitize_key( get_bloginfo( 'name' ) );
      $filename = $sitename . '-' . $stylesheet . '-theme-backup.' . date( 'Y-m-d' ) . '.txt';
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Disposition: attachment; filename=$filename");
      header("Content-Transfer-Encoding: binary");
      header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );

      //** Set Backup of Denali Settings */
      $settings = array( 'denali_theme_settings' => $denali_theme_settings );

      //** Set Backup of Widgets */
      $sidebars_widgets = wp_get_sidebars_widgets();
      if( !empty( $_REQUEST['widgets_settings'] ) && !empty( $sidebars_widgets ) ) {
        $settings['widgets']['sidebars_widgets'] = $sidebars_widgets;
        $settings['widgets']['wp_registered_widgets'] = array();
        foreach( (array)$sidebars_widgets as $sidebar => $widgets ) {
          foreach( (array)$widgets as $widget ) {
            if( !isset( $wp_registered_widgets[$widget]['callback'][0] ) ||
                !is_object( $wp_registered_widgets[$widget]['callback'][0] ) ||
                !is_callable( array( $wp_registered_widgets[$widget]['callback'][0] , 'get_settings' ) )
            ) {
              continue;
            }
            $s = $wp_registered_widgets[$widget]['callback'][0]->get_settings();
            $number = $wp_registered_widgets[$widget]['params'][0]['number'];
            $s = $s[$number];
            $settings['widgets']['wp_registered_widgets'][$widget] = array(
              'class' => get_class($wp_registered_widgets[$widget]['callback'][0]),
              'id_base' => $wp_registered_widgets[$widget]['callback'][0]->id_base,
              'name' => $wp_registered_widgets[$widget]['callback'][0]->name,
              'option_name' => $wp_registered_widgets[$widget]['callback'][0]->option_name,
              'number' => $number,
              'settings' => $s,
            );
          }
        }
      }

      //** Set Backup of Menus */
      if( !empty( $_REQUEST['menus_settings'] ) ) {
        $nav_menus = wp_get_nav_menus();
        foreach( (array)$nav_menus as $k => $menu ) {
          $nav_menus[$k]->items = wp_get_nav_menu_items($menu);
        }
        $settings['menus']['nav_menus'] = $nav_menus;
        $settings['menus']['nav_menu_locations'] = get_nav_menu_locations();
      }

      echo json_encode($settings);
      die();
    }

    if(current_user_can('edit_theme_options') && wp_verify_nonce($_REQUEST['_wpnonce'], 'denali_auto_setup')) {

    $dfts = $_REQUEST['dfts'];

    if(!empty($dfts)) {

      //** Set default title in header if no image or text exists already */
      if(empty($denali_theme_settings['logo_text']) && empty($denali_theme_settings['logo'])) {
        $denali_theme_settings['logo_text'] = get_bloginfo('name');
      }

      $default_search_widget_config = array(
        'title' => __('Property Search','denali'),
        'searchable_property_types' => $wp_properties['searchable_property_types'],
        'searchable_attributes' => array_slice($wp_properties['searchable_attributes'], 0, 4)
      );

      foreach($dfts as $key => $value) {


        switch($key) {

          //** Do these first in case property data is needed later */
          case 'automation_tasks':

            foreach($value as $best_practice) {

              switch ($best_practice) {

                case 'generate_properties':
                  denali_theme::generate_dummy_properties();
                break;

                case 'create_agents':

                  //** Create some default fields */
                  if(empty($wp_properties['configuration']['feature_settings']['agents']['agent_fields'])) {
                    $wp_properties['configuration']['feature_settings']['agents']['agent_fields']['phone_number']['name'] = "Phone Number";
                    $wp_properties['configuration']['feature_settings']['agents']['agent_fields']['website_url']['name'] = "Website URL";
                  }

                  denali_theme::generate_dummy_agents();
                break;

              }

            }
          break;

          //** Setup color scheme, exact value is passed */
          case 'color_scheme':

            $denali_theme_settings['color_scheme'] = $value;

            if(empty($value)) {
              remove_theme_mod('background_image');
              remove_theme_mod('background_image_thumb');
              set_theme_mod('background_repeat', 'repeat-x');
            }

            if($value == 'skin-dark-colors.css') {
              set_theme_mod('background_color', '868686');
            }

            if($value == 'skin-dark-colors.css' || $value == 'skin-blue.css') {
              set_theme_mod('background_color', '');
              set_theme_mod('background_image', '');
              set_theme_mod('background_image_thumb', '');
            }

          break;

          //** Setup color scheme, exact value is passed */
          case 'home_page':

            if($value == 'option_1') {
              //** Do not include a widget area on home page, have the regular content fill up entire page.  */
              denali_theme::remove_widget_from_sidebar('home_sidebar', 'all');

            } elseif ($value == 'option_2') {
              //** Show a sidebar on home page, and load some widgets in there.  */
              denali_theme::remove_widget_from_sidebar('home_sidebar', 'all');
              denali_theme::add_widget_to_sidebar('home_sidebar', 'text', array('title' => 'Welcome!', 'text' => 'This widget was added automatically to the <b>Home - Sidebar</b> widget area.'));
              denali_theme::add_widget_to_sidebar('home_sidebar', 'searchpropertieswidget',$default_search_widget_config);
            }

          break;

          case 'slideshow':

            if($value == 'option_1') {
              //** Show a slideshow with a property search widget.  */
              denali_theme::add_widget_to_sidebar('global_property_search', 'searchpropertieswidget', $default_search_widget_config);
              $denali_theme_settings['hide_slideshow_search'] = 'false';
              $denali_theme_settings['home_page_attention_grabber_area_hide'] = 'false';
              $wp_properties['configuration']['feature_settings']['slideshow']['glob']['image_size'] = 'home_page_slideshow';
              $wp_properties['configuration']['feature_settings']['slideshow']['glob']['link_to_property'] = 'true';
              $wp_properties['configuration']['feature_settings']['slideshow']['glob']['show_property_title'] = 'true';

              //** Somehow add the slidershow to home page */

            } elseif ($value == 'option_2') {
              //** Show a large slideshow, but no search widget.  */

              $denali_theme_settings['hide_slideshow_search'] = 'true';
              $denali_theme_settings['home_page_attention_grabber_area_hide'] = 'false';
              $wp_properties['configuration']['feature_settings']['slideshow']['glob']['image_size'] = 'property_slideshow';
              $wp_properties['configuration']['feature_settings']['slideshow']['glob']['link_to_property'] = 'true';
              $wp_properties['configuration']['feature_settings']['slideshow']['glob']['show_property_title'] = 'true';
              //** Somehow add the slidershow to home page */


            } elseif ($value == 'option_3') {
              //** Do not show any slideshow or search widget on home page at all.  */
              $denali_theme_settings['home_page_attention_grabber_area_hide'] = 'true';
            }

          break;

          case 'property_page':
            if($value == 'option_1') {
              //** Yes, show large slideshow above property information when slideshow images exist.  */
              $wp_properties['configuration']['feature_settings']['slideshow']['property']['image_size'] = 'property_slideshow';
              $denali_theme_settings['singular_header_image_size'] = 'property_slideshow';
              $denali_theme_settings['never_show_property_slideshow'] = 'false';


            } elseif ($value == 'option_2') {
              //** No, I don't like slideshows or header images, do not show a header.  */
              $denali_theme_settings['singular_header_image_size'] = '';
              $denali_theme_settings['never_show_property_slideshow'] = 'true';

            }

          break;


          case 'best_practices':

            foreach($wp_properties['property_types'] as $property_slug => $property_title) {
              $wpp_property_sidebars[] = "wpp_sidebar_$property_slug";
            }

            foreach($value as $best_practice) {

              switch ($best_practice) {

                case 'setup_single_property_page':
                  //** Setup the single listing page with all the widgets.*/

                  foreach( (array)$wpp_property_sidebars as $sidebar_id ) {
                    denali_theme::add_widget_to_sidebar($sidebar_id, 'agentwidget', array('title' => '', 'saved_fields' => array('display_name', 'agent_image', 'widget_bio', 'phone_number')));
                    denali_theme::add_widget_to_sidebar($sidebar_id, 'gallerypropertieswidget', array('title' => 'Gallery', 'image_type' => 'sidebar_gallery', 'big_image_type' => 'large'));
                  }
                break;

                case 'fix_permalinks':
                  //** Setup my URLs to be pretty.*/
                  $wp_rewrite->set_permalink_structure('/%category%/%postname%/');
                break;

                case 'setup_property_page':

                  //** Setup my property result page with shortcodes and attributes */
                  if(is_callable(array('WPP_F','setup_default_property_page'))) {
                    $post_page = WPP_F::setup_default_property_page();

                    if(!empty($post_page)) {
                      $wp_properties['configuration']['base_slug'] = $post_page['post_name'];
                      $wp_properties['configuration']['automatically_insert_overview'] = 'false';
                      $wp_properties['configuration']['do_not_override_search_result_page'] = 'true';
                    }
                  }

                  $wp_properties['configuration']['property_overview']['thumbnail_size'] = 'overview_thumbnail';

                  //** Setup attributes for property overviews */

                  $denali_theme_settings['property_overview_attributes']['detail'] = array_slice(array_keys($wp_properties['property_stats']), 0, 6);


                break;

                case 'setup_widgets':
                  //** Setup all the widgets. */

                  denali_theme::add_widget_to_sidebar('right_sidebar', 'pages', array(
                    'title' => __('Pages', 'denali')
                  ));

                  denali_theme::add_widget_to_sidebar('right_sidebar', 'featuredpropertieswidget', array(
                    'title' => __('Featured', 'denali'),
                    'image_type' => 'sidebar_gallery',
                    'big_image_type' => 'large'
                  ));


                  denali_theme::add_widget_to_sidebar('property_overview_sidebar', 'featuredpropertieswidget', array(
                    'title' => __('Featured', 'denali'),
                    'image_type' => 'sidebar_gallery',
                    'big_image_type' => 'large'
                  ));

                break;

                case 'setup_homepage':

                  //** Setup up my homepage, put some properties on there, and a search form for good measure.*/

                  denali_theme::add_widget_to_sidebar('property_overview_sidebar', 'featuredpropertieswidget', array(
                    'title' => __('Featured', 'denali'),
                    'image_type' => 'sidebar_gallery',
                    'big_image_type' => 'large'
                  ));

                  $home_page_id = denali_theme::setup_default_home_page();

                  if(get_option('show_on_front') != 'page') {
                    update_option('show_on_front', 'page');
                  }

                  update_option('page_on_front', $home_page_id);

                break;

              }

            }

          break;

        }

      }

      $wp_rewrite->flush_rules();

    }

    //** Save settings */
    update_option('wpp_settings', $wp_properties);
    update_option('denali_theme_settings', $denali_theme_settings);

    //wp_redirect(admin_url('themes.php?page=functions.php&action=first_time_setup'));

    //** Redirect page to default Theme Settings page */
    wp_redirect(admin_url('themes.php?page=functions.php&message=auto_complete_done'));
    die();

    }

    }

  }


  /**
   * Displays first-time setup splash screen
   *
   *
   * @since Denali 1.0
   */
  static function show_first_time_setup() {
    global $wp_properties, $denali_theme_settings;
    ?>
    <style type="text/css">

    </style>
    <script type="text/javascript">
      jQuery(document).ready(function() {

        denali_adjust_inquiry_tab();

      });


    </script>

    <div id="denali_settings_page" class="wrap denali_settings_page">

    <h2><?php _e('Thank you for using the Denali Theme.','denali'); ?></h2>
    <p><?php _e('This is a step-by-step process for quickly setting up your WP-Property website by following some of our best setup practices. ','denali'); ?></p>

    <form action="#" method="post">
      <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('denali_auto_setup'); ?>" />

      <div class="denali_setup_block">

        <div class="step_explanation">
          <h3 class="step_title"><?php _e('General Color Scheme.','denali'); ?></h3>
          <p><?php _e('Which of the three default color schemes would you like to use?','denali'); ?></p>
        </div>

         <ul class="block_options">
          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/skin-default.jpg" />
            <div class="option_note"><?php _e('Brown colors with a gradient background.','denali'); ?> </div>
            <input type="checkbox"  <?php checked('', $denali_theme_settings['color_scheme']); ?> name="dfts[color_scheme]" value="" />
          </li>

         <?php
         if($skins = denali_theme::get_color_schemes()) {
          foreach($skins as $skin_slug => $skin_data) {

            //** Don't show skins without a thumb */
            if(empty($skin_data['thumb'])) {
              continue;
            }
         ?>
          <li class="denali_setup_option_block">
            <img src="<?php echo $skin_data['thumb']; ?>" />
            <div class="option_note"><?php $skin_data['description']; ?> </div>
            <input type="checkbox" <?php checked($skin_slug, $denali_theme_settings['color_scheme']); ?> name="dfts[color_scheme]" value="<?php echo $skin_slug; ?>" />
          </li>
          <?php } }?>
         </ul>
      </div>


       <div class="denali_setup_block">
        <div class="step_explanation">
          <h3 class="step_title"><?php _e('Home Page','denali'); ?></h3>
          <p><?php _e('How do you want to display the content on the home page?','denali'); ?></p>
        </div>

         <ul class="block_options">
          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_4_opt_1.jpg" />
            <div class="option_note"><?php _e('Do not include a widget area on home page, have the regular content fill up entire page.','denali'); ?> </div>
            <input type="checkbox" <?php echo (!denali_theme::is_active_sidebar('home_sidebar') ? 'checked="true"' :''); ?> name="dfts[home_page]" value="option_1" />
          </li>

          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_4_opt_2.jpg" />
            <div class="option_note"><?php _e('Show a sidebar on home page, and load some widgets in there.','denali'); ?> </div>
            <input type="checkbox" <?php echo (denali_theme::is_active_sidebar('home_sidebar') ? 'checked="true"' :''); ?> name="dfts[home_page]" value="option_2" />
          </li>
         </ul>
      </div>

       <div class="denali_setup_block">
        <div class="step_explanation">
          <h3 class="step_title"><?php _e('Slideshow.','denali'); ?></h3>
          <p><?php _e('Do you want to show a slideshow on the home page? How about a property search form?','denali'); ?></p>
        </div>

         <ul class="block_options">
          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_2_opt_1.jpg" />
            <div class="option_note"><?php _e('Show a slideshow with a property search widget.','denali'); ?> </div>
            <input type="checkbox" name="dfts[slideshow]" <?php echo ($denali_theme_settings['hide_slideshow_search'] != 'true' && $denali_theme_settings['home_page_attention_grabber_area_hide'] != 'true' ? 'checked="true"' :''); ?>  value="option_1" />
          </li>

          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_2_opt_2.jpg" />
            <div class="option_note"><?php _e('Show a large slideshow, but no search widget.','denali'); ?> </div>
            <input type="checkbox" name="dfts[slideshow]"  <?php echo ($denali_theme_settings['hide_slideshow_search'] == 'true' && $denali_theme_settings['home_page_attention_grabber_area_hide'] != 'true' ? 'checked="true"' :''); ?>   value="option_2" />
          </li>

          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_2_opt_3.jpg" />
            <div class="option_note"><?php _e('Do not show any slideshow or search widget on home page at all.','denali'); ?> </div>
            <input type="checkbox" name="dfts[slideshow]"  <?php echo ($denali_theme_settings['home_page_attention_grabber_area_hide'] == 'true' ? 'checked="true"' :''); ?>   value="option_3" />
          </li>
         </ul>
      </div>

       <div class="denali_setup_block">
        <div class="step_explanation">
          <h3 class="step_title"><?php _e('Property Page','denali'); ?></h3>
          <p><?php _e('Should we show slideshows on the property pages?','denali'); ?></p>
        </div>

         <ul class="block_options">
          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_3_opt_1.jpg" />
            <div class="option_note"><?php _e('Yes, show large slideshow above property information when slideshow images exist.','denali'); ?> </div>
            <input type="checkbox" name="dfts[property_page]" <?php checked($denali_theme_settings['never_show_property_slideshow'], 'false'); ?> value="option_1" />
          </li>

          <li class="denali_setup_option_block">
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/first_time_setup/row_3_opt_2.jpg" />
            <div class="option_note"><?php _e('No, I don\'t like slideshows or header images, do not show a header.','denali'); ?> </div>
            <input type="checkbox" name="dfts[property_page]" <?php checked($denali_theme_settings['never_show_property_slideshow'], 'true'); ?> value="option_2" />
          </li>
         </ul>
      </div>

       <div class="denali_setup_block">
        <div class="step_explanation">
          <h3 class="step_title"><?php _e('Best Practices','denali'); ?></h3>
        </div>

         <ul class="block_options regular_list">
          <li class="bigger_text"><?php _e('We can quickly setup your site according to some of the best practices we have established while working with hundreds of our cilents, and helping them setup their WP-Property powered sites:','denali'); ?></li>

          <li><input type="checkbox" name="dfts[best_practices][]" value="setup_homepage" id="setup_homepage"><label for="setup_homepage"> <?php _e('Setup up my homepage, put some properties on there, and a search form for good measure.','denali'); ?></li>

          <?php if(is_callable(array('WPP_F','setup_default_property_page'))) {  ?>
          <li><input type="checkbox" name="dfts[best_practices][]" value="setup_property_page" id="setup_property_page"><label for="setup_property_page"> <?php _e('Setup my property result page with shortcodes and attributes.','denali'); ?></li>
          <?php } ?>

          <li><input type="checkbox" name="dfts[best_practices][]" value="setup_single_property_page" id="setup_single_property_page"><label for="setup_single_property_page"> <?php _e('Setup the single listing page with all the widgets.','denali'); ?></li>

          <?php if(get_option('permalink_structure') == '') { ?>
          <li><input type="checkbox" name="dfts[best_practices][]" value="fix_permalinks" id="fix_permalinks"><label for="fix_permalinks"> <?php _e('Setup my URLs to be pretty.','denali'); ?></li>
          <?php } ?>

          <li><input type="checkbox" name="dfts[best_practices][]" value="setup_widgets" id="setup_widgets"><label for="setup_widgets"> <?php _e('Populate widget areas and sidebars with widgets.','denali'); ?></li>

         </ul>

      </div>

       <div class="denali_setup_block">
        <div class="step_explanation">
          <h3 class="step_title"><?php _e('Other','denali'); ?></h3>
        </div>

         <ul class="block_options regular_list">
          <li><input type="checkbox" name="dfts[automation_tasks][]" value="generate_properties" id="generate_properties"><label for="generate_properties"> <?php _e('Create a few sample properties.','denali'); ?></li>

          <?php if(is_callable(array('class_agents','create_agent'))) { ?>
          <li><input type="checkbox" name="dfts[automation_tasks][]" value="create_agents" id="create_agents"><label for="create_agents"> <?php _e('Create a sample agent, a dedicated agent page, and associate with the sample properties.','denali'); ?></li>
          <?php } ?>
         </ul>

      </div>

      <label class="denali_save_settings" for="denali_save_settings"><?php _e('...enough questions,','denali'); ?></label>
      <input type="submit" value="<?php _e('Setup My Site!','denali'); ?>" class="denali_save_settings" />

    </form>

    </div>

    <?php
  }


  /**
   * Adds "Theme Options" page on back-end
   *
   * Used for configurations that cannot be logically placed into a built-in Settings page
   *
   * @todo Update 'auto_complete_done' message to include a link to the front-end for quick view of setup results.
   *
   * @since Denali 1.0
   */
  static function options_page() {
    global $wp_properties, $denali_theme_settings;

    if(class_exists('WP_CRM_Core')) {
      global $wp_crm;
      $shortcode_forms = $wp_crm['wp_crm_contact_system_data'];
    } else {
      $wp_crm = false;
      $shortcode_forms= false;
    }

    if(!class_exists('WPP_F')) { ?>
        <div class="wrap">
        <p><?php _e('The WP-Property plugin does not seem to be active.  Please activate before using theme.','denali'); ?></p>
        </div>
    <?php return; }

    if($_GET['action'] == 'first_time_setup') {
      denali_theme::show_first_time_setup();
      return;
    }

    if( !empty ( $_REQUEST['message'] ) ) {
      $updated_class = 'updated';
      switch( $_REQUEST['message'] ) {
        case 'auto_complete_done':
          $updated = __('Your site has been setup.  You may configure more advanced options here.','denali');
          break;
        case 'settings_updated':
          $updated = __('Theme settings updated.','denali');
          break;
        case 'backup_failed':
          $updated_class = 'error';
          $updated = __('Backup failed. The file, which you tried to upload, has incorrect data.','denali');
          break;
      }
    }



    $denali_footer_follow = denali_footer_follow($denali_theme_settings, array('return_raw' => true));

    // Load some default settings
    if(!isset($denali_theme_settings['wpp_d_inquiry_fields'])) {
      $denali_theme_settings['wpp_d_inquiry_fields']['phone_number']['name'] = __('Phone Number','denali');
    }

    //** Load available property types into for quick access and to avoid doing if(is_array())
    if(is_array($wp_properties['property_types'])) {
      $property_types = $wp_properties['property_types'];
    } else {
      $property_types = array();
    }


    ?>

    <script type="text/javascript">
      jQuery(document).ready(function() {

        denali_adjust_dom_to_conditionals('instant');

        jQuery(".denali_delete_logo").click(function() {
          jQuery.post(ajaxurl, {action: 'denali_actions', denali_action: 'delete_logo', _wpnonce: '<?php echo wp_create_nonce('denali_actions'); ?>'},function (result) {

           if(result.success == 'true') {
              jQuery(".current_denali_logo").remove();
            }

          }, 'json');

        });

      });
    </script>

    <div id="denali_settings_page" class="wpp_tabs wrap denali_settings_page">
      <h2 class="theme_settings_title">
        <?php _e('Denali Settings','denali'); ?>
        <a class="add-new-h2" href="<?php echo admin_url('themes.php?page=functions.php&action=first_time_setup'); ?>"><?php _e('Open Setup Assistant','denali'); ?></a>
      </h2>

      <?php if($updated): ?>
          <div class="<?php echo (!empty($updated_class) ? $updated_class : ""); ?> fade">
              <p><?php echo $updated; ?></p>
          </div>
      <?php endif; ?>
      <form action="<?php echo admin_url('themes.php?page=functions.php'); ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('denali_settings'); ?>" />
      <div id="wpp_settings_tabs" class="clearfix">
        <ul class="tabs">
          <li><a href="#tab_misc"><?php _e('General Settings','denali'); ?></a></li>
          <li><a href="#tab_header"><?php _e('Header','denali'); ?></a></li>
          <li><a href="#tab_footer"><?php _e('Footer','denali'); ?></a></li>
          <li><a href="#tab_property_display"><?php _e('Property Overview','denali'); ?></a></li>
          <li><a href="#tab_property_single"><?php _e('Single Content Pages','denali'); ?></a></li>
          <li><a href="#tab_inquiry"><?php _e('Inquiry','denali'); ?></a></li>
          <li><a href="#tab_help"><?php _e('Help','denali'); ?></a></li>
        </ul>

        <div id="tab_misc">


        <table class="form-table">
        <tbody>
        <tr valign="top">
        <th><?php _e('General Settings','denali'); ?></th>
        <td>
          <ul>

          <li>
              <input type='hidden' name='denali_theme_settings[maintanance_mode]' value='false' /><input type='checkbox' id="maintanance_mode" name='denali_theme_settings[maintanance_mode]' value='true'  <?php if($denali_theme_settings['maintanance_mode'] == 'true') echo " CHECKED " ?>/>
              <label for="maintanance_mode"><?php _e('Put site into maintenance mode.','denali'); ?></label>
              <br />
              <span class="description"><?php _e('Maintenance mode will display a splash image on front-end for non-administrators while you make changes.','denali'); ?></span>
          </li>

          </ul>

        </td>
        </tr>
        <tr valign="top">

        <th><label for="name"><?php _e('Color Scheme','denali'); ?><label></th>
          <td>
          <?php $color_schemes = denali_theme::get_color_schemes(); ?>

           <?php if($color_schemes) { ?>

            <ul class="denali_color_schemes block_options">
              <li class="denali_setup_option_block">
                <img class="skin_thumb" src="<?php echo get_bloginfo('template_url'); ?>/skin-default.jpg" />
                <div class="option_note"><?php _e('Default Denali Colors - Shades of blue and tan.','denali'); ?></div>
                <input <?php echo checked(false, $denali_theme_settings['color_scheme']); ?> type="checkbox" group="denali_color_scheme" name='denali_theme_settings[color_scheme]' id="color_scheme_default"  value="" />
              </li>

            <?php foreach($color_schemes as $scheme => $scheme_data): ?>
              <li class="denali_setup_option_block">
                <?php if($scheme_data['thumb']) { ?>
                <img class="skin_thumb" src="<?php echo $scheme_data['thumb']; ?>" />
                <?php } ?>
                <input group="denali_color_scheme" <?php echo checked($scheme, $denali_theme_settings['color_scheme']); ?> type="checkbox" name='denali_theme_settings[color_scheme]' id="color_scheme_<?php echo $scheme; ?>"  value="<?php echo $scheme; ?>" />
                <div class="option_note"><?php echo $scheme_data['name']; ?> - <?php echo $scheme_data['description']; ?></div>
              </li>
            <?php endforeach; ?>
            </ul>

           <?php } else { ?>
            <?php _e("You don't have any extra color schemes.",'denali'); ?> <br />
           <?php } ?>

            </td>
        </tr>

        <tr valign="top">
          <th><?php _e('Category Pages','denali'); ?></th>
          <td>
            <ul>
              <li>
                <input <?php echo checked('true', $denali_theme_settings['hide_meta_data_on_category_pages']); ?> type="checkbox"  name='denali_theme_settings[hide_meta_data_on_category_pages]' id="denali_theme_settings_hide_meta_data_on_category_pages"  value="true" />
                <label for="denali_theme_settings_hide_meta_data_on_category_pages"><?php _e('Hide post meta (date posted, author, categories listed in,etc) on category pages.','denali'); ?></label>
              </li>
            </ul>
            <div class="denali_help_wrap">
              <div class="denali_help_switch"><?php _e('What is "post meta"?','denali'); ?></div>
              <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0932.png" class='denali_help_image denali_help_element' />
            </div>
          </td>
        </tr>

        <tr valign="top">
          <th><?php _e('General Enhancements','denali'); ?></th>
          <td>
            <ul>
              <li>
                <input <?php echo checked('true', $denali_theme_settings['globally_disable_comments']); ?> type="checkbox"  name='denali_theme_settings[globally_disable_comments]' id="globally_disable_comments"  value="true" />
                <label for="globally_disable_comments"><?php _e('Hide all comment forms on the front-end.  This does not include property inquiry forms.','denali'); ?></label>
              </li>

            </ul>
          </td>
        </tr>


        <tr valign="top">
        <th><?php _e('Home Page','denali'); ?></th>
        <td>
        <ul>

        <li>
          <input <?php echo checked('true', $denali_theme_settings['hide_meta_data_on_home_page']); ?> type="checkbox"  name='denali_theme_settings[hide_meta_data_on_home_page]' id="hide_meta_data_on_home_page"  value="true" />
          <label for="hide_meta_data_on_home_page"><?php _e('Hide post meta (date posted, author, tags, etc) when displaying posts.','denali'); ?></label>
        </li>

        <li>
          <input <?php echo checked('true', $denali_theme_settings['hide_slideshow_search']); ?> type="checkbox"  name='denali_theme_settings[hide_slideshow_search]' id="hide_slideshow_search"  value="true" />
          <label for="hide_slideshow_search"><?php _e('Hide "Property Search" box from slideshow on home page.','denali'); ?></label>
        </li>

        <?php if(current_theme_supports('home_page_attention_grabber_area')): ?>
        <li>
          <input <?php echo checked('true', $denali_theme_settings['home_page_attention_grabber_area_hide']); ?> type="checkbox"  name='denali_theme_settings[home_page_attention_grabber_area_hide]' id="home_page_attention_grabber_area_hide"  value="true" />
          <label for="home_page_attention_grabber_area_hide"><?php _e('Hide the Attention Grabber section (including Header Image) from the home page','denali') ?>.</label>
        </li>
        <?php endif; ?>

        </ul>
          <div class="denali_help_wrap">
            <div class="denali_help_switch"><?php _e('More about home and post page property search.','denali'); ?></div>
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0923.png" class='denali_help_image denali_help_element' />
          </div>
        </td>
        </tr>

        <tr valign="top">
        <th><?php _e('Posts Blog Page','denali'); ?></th>
          <td>
          <ul>
            <li>
              <input <?php echo checked('true', $denali_theme_settings['hide_slideshow_search_from_posts_page']); ?> type="checkbox"  name='denali_theme_settings[hide_slideshow_search_from_posts_page]' id="hide_slideshow_search_from_posts_page"  value="true" />
              <label for="hide_slideshow_search_from_posts_page"><?php _e('Hide "Property Search" from slideshow on Posts Page.','denali'); ?></label><br />
            </li>
            <?php if(current_theme_supports('post_page_attention_grabber_area')): ?>
            <li>
              <input <?php echo checked('true', $denali_theme_settings['post_page_attention_grabber_area_hide']); ?> type="checkbox"  name='denali_theme_settings[post_page_attention_grabber_area_hide]' id="post_page_attention_grabber_area_hide"  value="true" />
              <label for="post_page_attention_grabber_area_hide"><?php _e('Hide the Attention Grabber section (including Header Image) from the blog home page.','denali'); ?></label>
            </li>
            <?php endif; ?>
          </ul>

          </td>
        </tr>

        <?php if(current_theme_supports('post_page_attention_grabber_area')): ?>
        <tr valign="top">
        <th><?php _e('Inside Pages ( Post, Pages )','denali'); ?></th>
          <td>
          <ul>
            <li>
              <input <?php echo checked('true', $denali_theme_settings['inside_attention_grabber_area_hide']); ?> type="checkbox"  name='denali_theme_settings[inside_attention_grabber_area_hide]' id="inside_attention_grabber_area_hide"  value="true" />
              <label for="inside_attention_grabber_area_hide"><?php _e('Hide the Attention Grabber section (including Header Image) from the inside pages.','denali'); ?></label>
            </li>
          </ul>
          </td>
        </tr>
        <?php endif; ?>

        <tr valign="top">
          <th><?php _e('Child Theme','denali'); ?></th>
          <td>
            <ul>
            <li>
              <?php if(is_child_theme()) { ?>
               <?php _e('You are currently using the Default Denali child theme, you are safe to make code and CSS customizations.','denali'); ?>
              <?php } elseif(denali_theme::denali_child_theme_exists()) { ?>
              <?php _e('Default Denali child theme is installed. Please be sure to make any code customizations in there, you can activate it on Appearance/Themes page to avoid losing your changes on upgrade.','denali'); ?>
              <?php } else { ?>
                <input <?php echo checked('true', $denali_theme_settings['install_denali_child_theme']); ?> type="checkbox"  name='denali_theme_settings[install_denali_child_theme]' id="denali_theme_settings_install_denali_child_theme"  value="true" />
                <label for="denali_theme_settings_install_denali_child_theme"><?php _e('Install Denali child theme.','denali'); ?></label>
                <?php if($denali_theme_settings['install_denali_child_theme'] == 'false') { ?>
                  <?php _e('Note: There was a problem installing the default Child Theme. You may need to install it manually. Read \'Help\' above.','denali'); ?>
                <?php } else { ?>
                  <br/><span class="description"><?php _e('Child theme will be created automatically. You have to make any code customizations in created child theme folder ( <b>themes/denali-child</b> ) to avoid losing your changes on upgrade.','denali'); ?></span>
                  <br/><span class="description"><?php _e('Check <a target="_blank" href="http://codex.wordpress.org/Child_Themes">Wordpress Codex</a> for more information.', 'denali'); ?></span>
                <?php } ?>
              <?php } ?>
            </li>
            </ul>
          </td>
        </tr>


      </tbody>
      </table>


    </div>


    <div id="tab_header" style="display:block">

    <table class="form-table">
      <tbody>
      <tr valign="top">
      <th><?php _e('Header Elements','denali'); ?></th>
      <td>
        <ul>

        <li>
          <input class="denali_conditional_setting" affected_options="logo_options" <?php echo checked('true', $denali_theme_settings['hide_logo']); ?> type="checkbox"  name='denali_theme_settings[hide_logo]' id="hide_logo"  value="true" />
          <label for="hide_logo"><?php _e('Hide logo from header.','denali'); ?></label>
        </li>

        <?php if(current_theme_supports('header-property-search')) { ?>
        <li>
          <input class="denali_conditional_setting" affected_options="header_property_search"  <?php echo checked('true', $denali_theme_settings['hide_header_property_search']); ?> type="checkbox"  name='denali_theme_settings[hide_header_property_search]' id="hide_header_property_search"  value="true" />
          <label for="hide_header_property_search"><?php _e('Disable the header <b>Property Search</b> dropdown section.','denali'); ?></label>
        </li>
        <?php } ?>

        <?php if(current_theme_supports('header-property-contact')) { ?>
        <li>
          <input class="denali_conditional_setting" affected_options="header_contact_us" <?php echo checked('true', $denali_theme_settings['hide_header_contact']); ?> type="checkbox"  name='denali_theme_settings[hide_header_contact]' id="denali_theme_settings_hide_header_contact"  value="true" />
          <label for="denali_theme_settings_hide_header_contact"><?php _e('Disable the header <b>Contact Us</b> dropdown section.','denali'); ?></label>
        </li>
        <?php } ?>

        <?php if(current_theme_supports('header-login')) { ?>
        <li>
          <input <?php echo checked('true', $denali_theme_settings['hide_header_login']); ?> type="checkbox"  name='denali_theme_settings[hide_header_login]' id="denali_theme_settings_hide_header_login"  value="true" />
          <label for="denali_theme_settings_hide_header_login"><?php _e('Disable the header <b>Login</b> dropdown section.','denali'); ?></label>
        </li>
        <?php } ?>

        <?php if(current_theme_supports('header-card')) { ?>
        <li>
          <input  class="denali_conditional_setting" affected_options="header_caller_card"  <?php echo checked('true', $denali_theme_settings['hide_header_card']); ?> type="checkbox"  name='denali_theme_settings[hide_header_card]' id="hide_header_card"  value="true" />
          <label for="hide_header_card"><?php _e('Disable the header <b>Caller Card</b> section.','denali'); ?></label>
        </li>
        <?php } ?>

        </ul>
      </td>
      </tr>

      <tr class="denali_conditional_setting_result" required_enabled_setting="explore_block">
      <th><?php _e('Header image','denali'); ?></th>
      <td>
        <p><?php _e('You can setup header image','denali'); ?> <a href="<?php echo get_admin_url(); ?>themes.php?page=custom-header"><?php _e('here','denali'); ?></a>.</p>
      </td>
      </tr>
      <tr class="denali_conditional_setting_result" required_enabled_setting="explore_block">
      <th><?php _e('Menus','denali'); ?></th>
      <td>
        <p><?php _e('To set up your header menus you should go','denali'); ?> <a href="<?php echo get_admin_url(); ?>nav-menus.php"><?php _e('here','denali'); ?></a>.</p>
      </td>
      </tr>

      <tr class="denali_conditional_setting_result" required_enabled_setting="header_property_search">
        <th><label for="property_search_label"><?php _e('"Property Search" label','denali'); ?></label></th>
        <td>
          <ul>
            <li>
              <input type="text" name="denali_theme_settings[property_search_label]" id="property_search_label"  value="<?php echo esc_attr( $denali_theme_settings['property_search_label'] ); ?>"/>
              <em><?php _e('If empty, default label is "Property Search"','denali'); ?></em>
            </li>
          </ul>
        </td>
      </tr>

      <tr class="denali_conditional_setting_result" required_enabled_setting="header_contact_us">
        <th><label for="contact_us_label"><?php _e('"Contact us" label','denali'); ?></label></th>
        <td>
          <ul>
            <li>
              <input type="text" name="denali_theme_settings[contact_us_label]" id="contact_us_label" value="<?php echo esc_attr( $denali_theme_settings['contact_us_label'] ); ?>"/>
              <em><?php _e('If empty, default label is "Contact us"','denali'); ?></em>
            </li>
          </ul>
        </td>
      </tr>

      <tr valign="top">
        <th>&nbsp;</th>
        <td>
          <ul>
            <li class="denali_conditional_setting_result" required_enabled_setting="header_property_search">
              <input <?php echo checked('true', $denali_theme_settings['break_out_global_property_search_areas']); ?> type="checkbox"  name='denali_theme_settings[break_out_global_property_search_areas]' id="break_out_global_property_search_areas"  value="true" />
              <label for="break_out_global_property_search_areas">
                <?php _e('Create separate widget area for the header property search. The widget area is called <b>Header: Property Search</b>.','denali'); ?>
              </label>
            </li>
            <?php if ( class_exists('qTranslateWidget') ) { ?>
            <li>
              <input <?php echo checked('true', $denali_theme_settings['show_qtranslate_widget']); ?> type="checkbox"  name='denali_theme_settings[show_qtranslate_widget]' id="show_qtranslate_widget"  value="true" />
              <label for="show_qtranslate_widget"><?php _e('Show qTranslate language switcher in the header.','denali'); ?></label>
            </li>
            <?php } ?>
          </ul>
        </td>
      </tr>

    <tr class="denali_conditional_setting_result" required_enabled_setting="logo_options">
      <th><?php _e('Header Logo','denali'); ?></th>
      <td>

        <div class="denali_logo_upload">

          <?php if(!empty($denali_theme_settings['logo'])) {?>
          <div class='current_denali_logo'>
            <img src="<?php echo $denali_theme_settings['logo']; ?>" class="denali_logo" />
            <div class="denali_delete_logo"><span class="button "><?php _e('Delete Logo','denali'); ?></span></div>
          </div>
          <?php } else { ?>
          <p><?php _e('You currently do not have a logo uploaded. You may set one using CSS as well.','denali'); ?></p>
          <input class='big_text' id="logo_text" type="regular-text" value="<?php echo $denali_theme_settings['logo_text']; ?>" name="denali_theme_settings[logo_text]" />
          <?php } ?>

          <div class="upload_new_logo">
            <label for="denali_text_logo"><?php _e('To upload new logo, choose an image from your computer: ','denali'); ?></label>
            <input id="denali_text_logo" type="file" name="logo" />
          <div>

        </div>
        </td>
      </tr>

      <tr valign="top">
        <th><label for="phone"><?php _e('Phone Number','denali'); ?><label></th>
        <td>
          <ul>
            <li>
              <input type="text" name="denali_theme_settings[phone]" id="phone"  value="<?php echo esc_attr( $denali_theme_settings['phone'] ); ?>"/>
            </li>
          </ul>
        </td>
      </tr>


      <tr valign="top">
        <th><label for="address"><?php _e('Address','denali'); ?><label></th>
        <td>
          <ul>
            <li>
              <textarea name="denali_theme_settings[address]" id="address"><?php echo  esc_attr( $denali_theme_settings['address'] ); ?></textarea>
            </li>
          </ul>
        </td>
      </tr>


      <tr valign="top" class="denali_conditional_setting_result" required_enabled_setting="header_caller_card">
         <th><label for="name"><?php _e('Caller Card','denali'); ?><label></th>
        <td>
          <ul>
            <li>
              <label for="phone_number_prefix"><?php _e('Text before phone number:','denali'); ?></label>
              <input type="text" name="denali_theme_settings[phone_number_prefix]" id="phone_number_prefix"  value="<?php echo esc_attr( $denali_theme_settings['phone_number_prefix'] ); ?>"/>
            </li>
            <li>
              <input  <?php echo checked('true', $denali_theme_settings['hide_address_from_card']); ?> type="checkbox" name='denali_theme_settings[hide_address_from_card]' id="hide_address_from_card"  value="true" />
              <label for="hide_address_from_card"><?php _e('Don\'t display the address below the phone number on top of page.','denali'); ?></label>
          </li>
          </ul>
        </td>
      </tr>


      <tr valign="top" class="denali_conditional_setting_result" required_enabled_setting="header_contact_us">
        <th><label for="name"><?php _e('Organization Name','denali'); ?><label></th>
        <td>
          <input type="text" name="denali_theme_settings[name]" id="name" value="<?php echo  esc_attr( $denali_theme_settings['name'] );  ?>"/>
        </td>
      </tr>

      <tr valign="top" class="denali_conditional_setting_result" required_enabled_setting="header_contact_us">
      <th>
        <label for="info"><?php _e('Primary Information','denali'); ?><label>
        <div class="description"></div>
      </th>
      <td>
        <textarea name="denali_theme_settings[info]" id="info" class="large-text code" style="height: 15em;"><?php echo  esc_attr( $denali_theme_settings['info'] ); ?></textarea>
        <div class="description"><?php _e('Information displayed under the name, within the "Contact Us" dropdown header. Shortcodes can be used here.','denali'); ?></div>
        <div class="denali_help_wrap">
        <div class="denali_help_switch"><?php _e('Where is this displayed?','denali'); ?></div>
        <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0943.png" class='denali_help_image denali_help_element' />
      </div>
      </td>
      </tr>

      <tr valign="top" class="denali_conditional_setting_result" required_enabled_setting="header_contact_us">
      <th><label for="phone"><?php _e('Fax Number','denali'); ?><label></th>
      <td>
        <input type="text" name="denali_theme_settings[fax]" id="fax_phone"  value="<?php echo esc_attr( $denali_theme_settings['fax'] ); ?>"/>
        <div class="description"><?php _e('Fax is displayed in the Contact Us dropdown below the phone number.','denali'); ?></div>
      </td>
      </tr>


    <tr class="denali_conditional_setting_result" required_enabled_setting="header_contact_us">
      <th><?php _e('Header Contact Form','denali'); ?></th>
      <td>
      <?php
      if(isset($denali_theme_settings['wp_crm']['header_contact'])) {
        $selection = $denali_theme_settings['wp_crm']['header_contact'];
      } else {
        $selection = 'denali_default_header_form';
      }
      ?>
      <table class="widefat wpp_something_advanced_wrapper">
        <tr>
          <th><label for="denali_theme_settings_header_crm_form"><?php _e('Form: ','denali'); ?></label></th>
          <td>
            <select id="denali_theme_settings_header_crm_form" name="denali_theme_settings[wp_crm][header_contact]">
              <option <?php selected($selection, 'denali_header_form'); ?> value="denali_default_header_form"><?php _e('Default Form','denali'); ?></option>
              <?php if(!empty($shortcode_forms) && is_array($shortcode_forms)) : ?>
                <?php foreach($shortcode_forms as  $form) : ?>
                  <option <?php selected($selection, $form['current_form_slug']); ?> value="<?php echo esc_attr($form['current_form_slug']); ?>"><?php echo $form['title']; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select><br />
            <?php if(!$wp_crm) : ?>
              <span class="description"><?php _e("You can use WP-CRM to have more flexibility over your contact forms, to include the header contact form.",'denali'); ?></span>
            <?php else : ?>
              <span class="description"><?php _e("Visit CRM -> Settings -> Shortcode Forms to add a contact form.",'denali'); ?></span>
            <?php endif; ?>
          </td>
        </tr>
        <tr valign="top" class="denali_header_crm_form_settings <?php echo $selection == 'denali_default_header_form' ? 'hidden' : ''; ?>">
          <td colspan="2">
            <?php  _e('Visit CRM -> Settings -> Shortcode Forms to set the selected form.','denali'); ?>
          </td>
        </tr>

        <tr valign="top" class="denali_header_regular_form_settings <?php echo $selection != 'denali_default_header_form' ? 'hidden' : ''; ?>">
          <th><?php _e('Send Notifications To:','denali'); ?></th>
          <td>
              <input type="text" name="denali_theme_settings[email]" id="email" value="<?php echo $denali_theme_settings['email'];?>" />
              <br /><span class="description"><?php _e('Messages submitted via the "Contact Us" form will be sent here. Separate multiple recipients with a comma.','denali'); ?></span>
          </td>
        </tr>

        <tr valign="top" class="denali_header_regular_form_settings <?php echo $selection != 'denali_default_header_form' ? 'hidden' : ''; ?>">
          <th><?php _e('Email From Address:','denali'); ?></th>
            <td>
              <input type="text" name="denali_theme_settings[email_from]" id="email" value="<?php echo $denali_theme_settings['email_from'];?>" />
              <br /><span class="description"><?php _e('This is the email messaged sent by the website will appear to be sent from. You can do something like this: <b>Contact Form &lt;website@mydomain.com&gt;</b>','denali'); ?></span>
          </td>
        </tr>
      </table>

      </td>

    </tr>


      </tbody>
      </table>

    </div>


    <div id="tab_footer" style="display:block">
    <?php
        $explore = $denali_theme_settings["options_explore"];
    ?>
    <table class="form-table">

    <tr valign="top">
      <th>&nbsp;</th>
      <td>
        <ul>
          <li>
            <input class="denali_conditional_setting" affected_options="explore_block"  type="checkbox" name="denali_theme_settings[footer_explore_block_hide]" id="footer_explore_block_hide" value="true" <?php checked($denali_theme_settings['footer_explore_block_hide'], 'true'); ?> />
            <label for="footer_explore_block_hide"><?php _e("Hide the <b>Explore Block</b> from footer area.",'denali');?></label>
          </li>

          <li>
            <input type="checkbox" name="denali_theme_settings[disable_footer_bottom_left_block_widget_area]" id="disable_footer_bottom_left_block_widget_area" value="true" <?php checked($denali_theme_settings['disable_footer_bottom_left_block_widget_area'], 'true'); ?> />
            <label for="disable_footer_bottom_left_block_widget_area"><?php _e('Disable the <b>Footer: Bottom Left Block</b> widget.','denali'); ?></label>
          </li>

          <?php if($denali_footer_follow) { ?>
          <li>
            <input class="denali_conditional_setting" affected_options="denali_footer_follow"  type="checkbox" name="denali_theme_settings[disable][denali_footer_follow]" id="disable_denali_footer_follow" value="true" <?php checked($denali_theme_settings['disable']['denali_footer_follow'], 'true'); ?> />
            <label for="disable_denali_footer_follow"><?php _e('Disable footer Social Media Icons.','denali'); ?></label>
          </li>
          <?php } ?>


          <li>
            <input type="checkbox" name="denali_theme_settings[disable][footer_description]" id="disable_footer_description" value="true" <?php checked($denali_theme_settings['disable']['footer_description'], 'true'); ?> />
            <label for="disable_footer_description"><?php _e('Do not include site description text in footer:','denali'); ?> <span class="description">"<?php echo get_bloginfo('description'); ?>"</span></label>
          </li>

          <li>
            <input <?php echo checked('true', $denali_theme_settings['show_equal_housing_icon']); ?> type="checkbox"  name='denali_theme_settings[show_equal_housing_icon]' id="show_equal_housing_icon"  value="true" />
            <label for="show_equal_housing_icon"><?php _e('Show "Equal Housing Opportunity" icon in footer.','denali'); ?></label>
          </li>

          <li>
            <input type="checkbox" name="denali_theme_settings[disable][powered_by_link]" id="disable_powered_by_link" value="true" <?php checked($denali_theme_settings['disable']['powered_by_link'], 'true'); ?> />
            <label for="disable_powered_by_link"><?php _e('Do not show <strong>Powered By</strong> link in footer','denali'); ?></label>
          </li>

        </ul>
      </td>
    </tr>

    <tr class="denali_conditional_setting_result" required_enabled_setting="explore_block">
      <th><?php _e('Menus','denali'); ?></th>
      <td>
        <p><?php _e('To set up your footer menus you should go','denali'); ?> <a href="<?php echo get_admin_url(); ?>nav-menus.php"><?php _e('here','denali'); ?></a>.</p>
        <ul>
          <li>
            <input type="checkbox" name="denali_theme_settings[footer_menu_hide]" id="disable_footer_menu" value="true" <?php checked($denali_theme_settings['footer_menu_hide'], 'true'); ?> />
            <label for="disable_footer_menu"><?php _e('Hide the Footer Menu.','denali'); ?></label>
          </li>
          <li>
            <input type="checkbox" name="denali_theme_settings[bottom_of_page_menu_hide]" id="disable_bottom_of_page_menu" value="true" <?php checked($denali_theme_settings['bottom_of_page_menu_hide'], 'true'); ?> />
            <label for="disable_bottom_of_page_menu"><?php _e('Hide the Bottom of Page Menu.','denali'); ?></label>
          </li>
        </ul>
      </td>
    </tr>

    <tr class="denali_conditional_setting_result" required_enabled_setting="explore_block">

      <th><?php _e('Explore Block','denali'); ?></th>
      <td>
      <p><?php _e('The Expore block may contain a list of your top pages, categories, or custom HTML, which can include shortcodes.','denali'); ?></p>
        <ul>
            <li><input group="options_explore"  type="radio" name="denali_theme_settings[options_explore]" id="pages" value="pages" <?php if($explore =='pages') echo 'checked="checked"'; ?>> <label for="pages"><?php _e('Pages','denali'); ?></label></li>
            <li><input group="options_explore"  type="radio" name="denali_theme_settings[options_explore]" id="cats" value="cats" <?php if($explore =='cats') echo 'checked="checked"'; ?>> <label for="cats"><?php _e('Categories','denali'); ?></label></li>
            <li>
              <input group="options_explore" type="radio" name="denali_theme_settings[options_explore]" id="custom_html" value="custom_html" <?php checked($explore,'custom_html'); ?>> <label for="custom_html"><?php _e('Custom HTML','denali'); ?></label>
              <div class="denali_theme_settings_explore_custom_html <?php echo ($explore != 'custom_html' ? 'hidden' : ''); ?>">
                <textarea name="denali_theme_settings[explore][custom_html_content]" id="custom_html_content"><?php echo ($denali_theme_settings ? $denali_theme_settings['explore']['custom_html_content'] : ''); ?></textarea>
              </div>
            </li>
          <li>
            <input type="checkbox" name="denali_theme_settings[footer_explore_title_hide]" id="footer_explore_title_hide" value="true" <?php checked($denali_theme_settings['footer_explore_title_hide'], 'true'); ?> />
            <label for="footer_explore_title_hide"><?php _e('Hide the <b>Explore Block</b> title.','denali'); ?></label>
          </li>
        </ul>
        <div class="denali_help_wrap">
          <div class="denali_help_switch"><?php _e('What is the "Explore" block?','denali'); ?></div>
          <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0938.png" class='denali_help_image denali_help_element' />
        </div>

      </td>
    </tr>

    <?php
    if($denali_footer_follow) {

    ?>
    <tr class="denali_conditional_setting_result" required_enabled_setting="denali_footer_follow">

      <th><?php _e('Social Media Links','denali'); ?></th>
      <td>
        <ul class="social_media_icons">
          <?php foreach( $denali_footer_follow as $social_key => $social_row ) { ?>
            <li>
              <label for="<?php echo $social_key; ?>">
                <img class="social_media_icon_thumb" src="<?php echo $social_row['thumb_url']; ?>" />
              <label>
                <input id="<?php echo $social_key; ?>" type="text"  class="denali_force_http_prefix" name="denali_theme_settings[<?php echo $social_row['option']; ?>]" value="<?php echo esc_attr( $social_row['url'] ); ?>" />
              <span class="label"><?php echo $social_row['label']; ?></span>
            </li>
          <?php } ?>
        </ul>

        <div class="denali_help_wrap">
          <div class="denali_help_switch"><?php _e('What are social media icons?','denali'); ?></div>
          <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0935.png" class='denali_help_image denali_help_element' />
        </div>
      </td>
      </tr>

  <?php } ?>

    </table>


    </div>

    <div id="tab_property_display" style="display:block">
      <?php
        // Add extra attributes
        $wp_properties['property_stats']['post_content'] = 'Property Content';

      ?>
      <table class="form-table">
      <tbody>
        <?php if ( class_exists( 'WPP_F' ) ) : ?>
        <tr>
          <th><?php _e("General Settings", "denali"); ?>
          <div class="description"><?php _e('These settings are duplicates of WP-Property plugin\'s settings. You can also find other ones on WP-Property Settings page.','denali'); ?></div>
          </th>
          <td>
            <div>
              <ul>
                <li><?php _e('Thumbnail size:','denali') ?> <?php WPP_F::image_sizes_dropdown("name=wpp_settings[configuration][property_overview][thumbnail_size]&selected=" . $wp_properties['configuration']['property_overview']['thumbnail_size']); ?></li>
                <?php if (method_exists('WPP_F', 'checkbox')) : ?>
                <li><?php echo WPP_F::checkbox('name=wpp_settings[configuration][property_overview][show_children]&label=' . __('Show children properties.','denali'), $wp_properties['configuration']['property_overview']['show_children']); ?></li>
                <li><?php echo WPP_F::checkbox('name=wpp_settings[configuration][property_overview][fancybox_preview]&label=' . __('Show larger image of property when image is clicked using fancybox.','denali') , $wp_properties['configuration']['property_overview']['fancybox_preview']); ?></li>
                <li><?php echo WPP_F::checkbox("name=wpp_settings[configuration][bottom_insert_pagenation]&label=" . __('Show pagination on bottom of results.','denali'), $wp_properties['configuration']['bottom_insert_pagenation']); ?></li>
                <?php else: ?>
                <?php
                  wp_enqueue_script('thickbox');
                  wp_enqueue_style('thickbox');
                ?>
                <li><span class='notice'><?php echo sprintf(__('Sorry, but some options cannot be displayed. Please <a title="Update WP-Property" class="thickbox" href="%s">update the WP-Property Plugin.</a>','denali'),admin_url('plugin-install.php?tab=plugin-information&amp;plugin=wp-property&amp;TB_iframe=true&amp;width=640&amp;height=408') ); ?></span></li>
                <?php endif; ?>
              </ul>
            </div>
          </td>
        </tr>
        <?php endif; ?>
        <tr>
          <th><?php _e('Overview Attributes','denali'); ?>
          <div class="description"><?php _e('Select the attributes to display in the [property_overview] shortcodes.','denali'); ?></div>
          </th>
          <td class="horizontal-list-settings">
            <div class="alignleft">
            <h3 class="equal_heights"><?php _e("Horizontal List. Icon should be assigned to attribute for showing on front-end.", "denali"); ?></h3>
            <div class="wp-tab-panel">
            <ul class="denali-stats-icons">
            <?php $counter = 0; ?>
            <?php foreach($denali_theme_settings['stats_icons'] as $icon): ?>
              <?php $v = $denali_theme_settings['property_overview_attributes']['stats_icons'][$icon]; ?>
              <?php $alt = ( $counter++%4 != 0 ) ? "even" : "odd"; ?>

              <?php if( $counter%2 ): ?>
              <li class="<?php echo $alt; ?>">
              <?php endif; ?>

                <div class="container">
                  <span class="denali-icon-box"><i class="denali-icon <?php echo $icon; ?> denali-box"></i></span>
                  <select name="denali_theme_settings[property_overview_attributes][stats_icons][<?php echo $icon ?>]">
                    <option value=""></option>
                    <?php foreach ( $wp_properties['frontend_property_stats'] as $attrib_slug => $attrib_title ) : ?>
                    <option value="<?php echo $attrib_slug; ?>" <?php echo $v == $attrib_slug ? "selected=\"selected\"" : ""; ?>><?php echo $attrib_title; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <?php echo UD_UI::checkbox("id=property_overview_{$icon}_stats&name=denali_theme_settings[property_overview_attributes][stats_by_icon][]&value={$icon}", (is_array($denali_theme_settings['property_overview_attributes']['stats_by_icon']) && in_array($icon, (array)$denali_theme_settings['property_overview_attributes']['stats_by_icon']) ? true : false)); ?>
                </div>
                <?php if( !($counter%2) ): ?>
                <div class="clear"></div>
              </li>
              <?php endif; ?>
            <?php endforeach; ?>
            </ul>
            </div>
            </div>

            <div class="alignright">
            <h3 class="equal_heights"><?php _e("Detailed list below the horizontal list, includes titles and values.", "denali"); ?></h3>
            <div class="wp-tab-panel">
            <ul>
            <?php $counter = 1; ?>
            <?php foreach($wp_properties['frontend_property_stats'] as $attrib_slug => $attrib_title): ?>
              <?php $alt = ( ++$counter%2 != 0 ) ? "even" : "odd"; ?>
              <li class="<?php echo $alt; ?>">
                <?php echo UD_UI::checkbox("id=property_overview_attributes_{$attrib_title}_detail&name=denali_theme_settings[property_overview_attributes][detail][]&label=$attrib_title&value={$attrib_slug}", (is_array($denali_theme_settings['property_overview_attributes']['detail']) && in_array($attrib_slug, (array)$denali_theme_settings['property_overview_attributes']['detail']) ? true : false)); ?>
              </li>
            <?php endforeach; ?>

            </ul>
            </div>
            </div>
            <div class="denali_help_wrap">
              <div class="denali_help_switch"><?php _e('Which is which?','denali'); ?></div>
              <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0842.png" class='denali_help_image denali_help_element' />
            </div>
          </td>
        </tr>
        <tr>
          <th><?php _e("Overview Attributes - Grid", "denali"); ?>
          <div class="description"><?php _e('Select the attributes to display in the [property_overview template=grid] shortcodes.','denali'); ?></div>
          </th>
          <td>
          <div class="wp-tab-panel">
          <ul>
          <?php $counter = 1; ?>
          <?php foreach($wp_properties['frontend_property_stats'] as $attrib_slug => $attrib_title): ?>
            <?php $alt = ( ++$counter%2 != 0 ) ? "even" : "odd"; ?>
            <li class="<?php echo $alt; ?>">
              <?php echo UD_UI::checkbox("id=property_overview_attributes_grid_{$attrib_title}_stats&name=denali_theme_settings[grid_property_overview_attributes][stats][]&label=$attrib_title&value={$attrib_slug}", (is_array($denali_theme_settings['grid_property_overview_attributes']['stats']) && in_array($attrib_slug, (array)$denali_theme_settings['grid_property_overview_attributes']['stats']) ? true : false)); ?>
            </li>
          <?php endforeach; ?>

          </ul>
          </div>
          <div class="denali_help_wrap">
            <div class="denali_help_switch"><?php _e('What is this for?','denali'); ?></div>
            <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0852.png" class='denali_help_image denali_help_element' />
          </div>
          </td>
        </tr>

      </tbody>
      </table>
      </div>

      <div id="tab_property_single" style="display:block">

        <div class="options_page_message">
          <p><?php _e('The attention grabber area is displayed above the property-specific content on the property pages. Denali checks several settings to determine what to display within the attention grabbing area of a property. ','denali'); ?></p>
        </div>

        <table class="form-table">
          <tbody>
            <tr>
              <th>
                <?php _e('1) Attention Area Widgets','denali'); ?>
                <div class="description"><?php _e('Property Type-specific widget area is first checked. If widgets found, they will be displayed.','denali'); ?></div>
              </th>
              <td>

                <p><?php _e('A custom Attention Grabber widget area is made available for every property type.','denali'); ?></p>
                <ul>
                <?php //** Check if custom widget areas exist */
                foreach($property_types as $property_type => $property_title) {

                  if($tabs = denali_theme::widget_area_tabs("wpp_header_{$property_type}")) {

                    if(count($tabs) > 1) {
                      echo '<li>' . sprintf(__('%1s has %2s widgets in the attention grabber area which will be displayed as dynamic tabs.', 'denali'), '<b>' . $property_title . '</b>', count($tabs)) . '</li>';
                    } else {
                      echo '<li>' . sprintf(__('%1s has one widget in the attention grabber area which will be displayed.', 'denali'), '<b>' . $property_title . '</b>') . '</li>';
                    }

                  } else {
                    echo '<li>' . sprintf(__('%1s does not have any widgets in the attention grabber area, a slideshow and featured image will be checked for.','denali'),  '<b>' . $property_title . '</b>') . '</li>';
                  }

                }
                ?>
                </ul>
                <span class="description"><?php _e('To select which widgets to display in this section, visit the Widgets page. ','denali'); ?></span>

              </td>
            </tr>


            <tr valign="top">
              <th>
                <?php _e('2) Property Slideshow','denali'); ?>
                <div class="description"><?php _e('If no widgets exist in widget area for a property type, we will attempt to display a slideshow.','denali'); ?></div>
              </th>
              <td>
                <p><?php _e('In order for a slideshow to be displayed, the images attached to the property must be of a certain size - WordPress will never enlarge your images to avoid pixelation. ','denali'); ?></p>
                <ul class="wpp_something_advanced_wrapper">

                 <li>
                    <input toggle_logic="reverse" class="wpp_show_advanced"  <?php echo checked('true', $denali_theme_settings['never_show_property_slideshow']); ?> type="checkbox"  name='denali_theme_settings[never_show_property_slideshow]' id="never_show_property_slideshow"  value="true" />
                    <label for="never_show_property_slideshow"><?php _e("Never display a slideshow on property pages.",'denali'); ?></label>
                  </li>

                  <li class="<?php echo $denali_theme_settings['never_show_property_slideshow'] == 'true' ? 'hidden' : '' ?> wpp_development_advanced_option">
                    <?php
                    $slideshow_size = $wp_properties['configuration']['feature_settings']['slideshow']['property']['image_size'];
                    $image_dimensions = WPP_F::image_sizes($slideshow_size);
                    ?>
                    <?php printf(__('Property Slideshow Size: %1dpx by %1dpx.', 'denali'), $image_dimensions['width'], $image_dimensions['height'] ); ?>
                    <span class="description"><?php _e('The slideshow size is set on the the Properties -> Settings -> Slideshow page.','denali'); ?></span>
                  </li>

                </ul>

              </td>
            </tr>


            <tr valign="top">
            <th>
              <?php _e('3) Featured Image in Header','denali'); ?>
              <div class="description"><?php _e('If neither an Attention Grabber widget area, nor the slideshow, can be displayed - the featured image can be displayed.','denali'); ?></div>
            </th>
            <td>
              <ul>
                <li>
                <?php WPP_F::image_sizes_dropdown("blank_selection_label= No Static Header Image &name=denali_theme_settings[singular_header_image_size]&selected={$denali_theme_settings['singular_header_image_size']}"); ?>
                </li>
                <li>
                  <input <?php echo checked('true', $denali_theme_settings['hide_singular_header_if_image_too_small']); ?> type="checkbox"  name='denali_theme_settings[hide_singular_header_if_image_too_small]' id="hide_singular_header_if_image_too_small"  value="true" />
                  <label for="hide_singular_header_if_image_too_small"><?php _e("If the image size you selected above does not exist, and there is no slideshow, do not show header area at all on property pages.",'denali'); ?></label>
                </li>
              </ul>
              <div class="denali_help_wrap">
                <div class="denali_help_switch"><?php _e('More about property header images.','denali'); ?></div>
                <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0919.png" class='denali_help_image denali_help_element' />
              </div>
            </td>
            </tr>

          </tbody>
        </table>

      </div>


      <div id="tab_inquiry">
      <table class="form-table">
      <tbody>
        <tr>
          <th></th>
          <td>
            <ul>
              <li>
                  <input type='hidden' name='denali_theme_settings[show_property_comments]' value='false' /><input type='checkbox' id="show_property_comments" name='denali_theme_settings[show_property_comments]' value='true'  <?php if($denali_theme_settings['show_property_comments'] == 'true') echo " CHECKED " ?>/>
                  <label for="show_property_comments"><?php _e("Don't treat property comments as inquiries.",'denali');?></label>
                  <br />
                  <span class="description"><?php _e("If enabled, property comments will be displayed on front-end and handled as comments.  If left disabled, comments will be treated as inquiries. You can enable/disable comments on individual property pages.",'denali');?></span>
              <div class="denali_help_wrap">
                <div class="denali_help_switch"><?php _e('More about inquiries','denali'); ?></div>
                <img src="<?php echo get_bloginfo('template_url'); ?>/img/admin/2011-05-11_0856.png" class='denali_help_image denali_help_element' />
              </div>
              </li>
            </ul>
          </td>

          </tr>
          <tr>
            <th><?php _e("WP-CRM Inquiry Forms",'denali');?></th>
            <td>
            <?php

            if(!$wp_crm) {
              echo '<p>' . sprintf(__('You can use <a class="small" href="%s">WP-CRM plugin</a> to have more flexibility over the inquiry data and form customization. Install WP-CRM to be able to use it for property inquiries.','denali'),admin_url('plugin-install.php?tab=search&amp;type=term&amp;s=WP-CRM+correspondence+andypotanin')) . '</p>';
            } elseif(!class_exists('class_contact_messages')) {
              echo '<p>' . __('You have WP-CRM, but you need to purchase "Shortcode Contact Forms" premium feature for flexibility over the inquiry data and form customization.','denali') . '</p>';
            } elseif(!$shortcode_forms) {
              echo '<p>' . __('Please visit CRM -> Settings -> Shortcode Forms to add a contact form.','denali') . '</p>';
            } else {  ?>
              <table class="widefat">
              <?php foreach($property_types as $property_slug => $property_title) {

                if(isset($denali_theme_settings['wp_crm']['inquiry_forms'][$property_slug])) {
                  $selection = $denali_theme_settings['wp_crm']['inquiry_forms'][$property_slug];
                } else {
                  $selection = 'denali_default_form';
                }

              ?>

                <tr>
                  <th>
                    <label for="denali_theme_settings_inquiry_crm_form_<?php echo $property_slug; ?>"><?php echo $property_title; ?></label>
                  </th>
                  <td>

                <select id="denali_theme_settings_inquiry_crm_form_<?php echo $property_slug; ?>" name="denali_theme_settings[wp_crm][inquiry_forms][<?php echo $property_slug; ?>]" class="denali_theme_settings_inquiry_crm_forms">
                  <option></option>
                  <option <?php selected($selection, 'denali_default_form'); ?> value="denali_default_form"><?php _e('Default Form','denali'); ?></option>
                <?php foreach($shortcode_forms as  $form) {  ?>
                  <option <?php selected($selection, $form['current_form_slug']); ?> value="<?php echo esc_attr($form['current_form_slug']); ?>"><?php echo $form['title']; ?> <?php echo (count($form['fields']) < 1 ? __('(No Fields)','denali') : ''); ?></option>
                <?php } ?>
                </select>
              </td>


            <?php } ?>
            </table>
            <div class="description"><?php _e('Please visit <a target="_blank" href="http://usabilitydynamics.com/products/wp-crm/">Usability Dynamics, Inc.</a> to learn more about WP-CRM.','denali'); ?></div>
            <?php } ?>


            </td>

          </tr>

          <tr class="denali_default_inquiry_form_fields">
            <th><?php _e('Default Form Fields','denali'); ?></th>
            <td>
            <p><?php _e('Add any additional input fields you would like to be displayed on the property inquiry forms. Name and e-mail address are required and already displayed.','denali'); ?>

         <table class="ud_ui_dynamic_table widefat" id="wpp_d_inquiry_fields">
          <thead>
            <tr>
              <th><?php _e('Field Name','denali'); ?></th>
              <th style="width:50px;"><?php _e("Slug",'denali');?></th>
              <th style="width:90px;"><?php _e("Required",'denali');?></th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>

            <?php foreach($denali_theme_settings['wpp_d_inquiry_fields'] as $field_slug => $field_data): $field_value = $field_data['name']; ?>
              <tr new_row="false" slug="<?php echo $field_slug; ?>" class="wpp_dynamic_table_row">
                <td><input type="text" value="<?php echo $field_value; ?>" name="denali_theme_settings[wpp_d_inquiry_fields][<?php echo $field_slug; ?>][name]" class="slug_setter"></td>
                <td><input type="text" class="slug" readonly="readonly" value="<?php echo $field_slug; ?>"></td>
                <td><input type="checkbox" value="on" name="denali_theme_settings[wpp_d_inquiry_fields][<?php echo $field_slug; ?>][required]" <?php checked('on', $field_data['required']); ?>></td>
                <td><span class="wpp_delete_row wpp_link"><?php _e('Delete','denali'); ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          <tfoot>
            <tr>
              <td colspan="4"><input type="button" value="<?php _e('Add Row','denali'); ?>" class="wpp_add_row button-secondary"></td>
            </tr>
          </tfoot>
        </table>

        <ul>
        <?php if(class_exists('class_agents')) { ?>
          <li>
            <input <?php checked('on', $denali_theme_settings['wpp_d_show_agent_dropdown_on_inquiry']); ?> type="checkbox" name="denali_theme_settings[wpp_d_show_agent_dropdown_on_inquiry]" value="on" id="wpp_d_show_agent_dropdown_on_inquiry" />
            <label for="wpp_d_show_agent_dropdown_on_inquiry"><?php _e('Show agent dropdown on inquiry form listing all agents associated with the property.','denali'); ?></label>
          </li>
        <?php } ?>
        </ul>

        </td>
        </tr>
        </table>
      </div>

      <div id="tab_help">
        <div class="denali_inner_tab">
          <div class="denali_settings_block">
            <?php _e("Restore Backup of Denali Configuration", 'denali'); ?>: <input name="denali_theme_settings[settings_from_backup]" type="file" />
            <span id="show_download_denali_settings_wrapper" class="denali_link"><?php _e('Download Backup of Current Denali Configuration.', 'denali');?></span>
            <div id="download_denali_settings_wrapper">
              <input id="denali_backup_nonce_url" type="hidden" value="<?php echo wp_nonce_url( "themes.php?page=functions.php&action=download-denali-backup", 'download-denali-backup'); ?>">
              <ul>
                <li><label><input id="denali_backup_widgets_settings" type="checkbox" value="true" /> <?php _e('Include <a target="_blank" href="../wp-admin/widgets.php">Widgets</a> Settings', 'denali');?></label></li>
                <li><label><input id="denali_backup_menus_settings" type="checkbox" value="true" /> <?php _e('Include <a target="_blank" href="../wp-admin/nav-menus.php">Menus</a> Settings', 'denali');?></label></li>
              </ul>
              <input type="button" id="denali_backup" value="<?php _e('Download', 'denali');?>" /> <span id="denali_backup_cancel" class="denali_link"><?php _e('Cancel','denali') ?></span>
            </div>
          </div>
          <div class="denali_settings_block">
            <?php _e('Look up the <b>$denali_theme_settings</b> global settings array.  This array stores all the default settings, which are overwritten by database settings, and custom filters.','denali') ?>
            <input type="button" value="<?php _e('Show $denali_theme_settings','denali') ?>" id="denali_show_settings_array"> <span id="denali_show_settings_array_cancel" class="denali_link hidden"><?php _e('Cancel','denali') ?></span>
            <pre id="denali_show_settings_array_result" class="denali_class_pre hidden"><?php echo htmlspecialchars( print_r( $denali_theme_settings, true ) ); ?></pre>
          </div>
          <div class="denali_settings_block">
            <input type="checkbox" <?php echo checked('true', $denali_theme_settings['ignore_theme_update']); ?> name='denali_theme_settings[ignore_theme_update]' id="denali_ignore_theme_update"  value="true" />
            <label for="denali_ignore_theme_update"><?php _e('Ignore new Denali Version updates.','denali'); ?></label><br/>
            <span class="description"><?php _e('If enabled, Denali theme will not check updates and you will not be able to update theme. It\'s recommended if you have customized your theme a lot and not sure that new version will not break anything.','denali'); ?></span><br/>
            <span class="description"><?php _e('Note: Denali doesn\'t do update automatically. You only will be noticed about new updates if this option is disabled.','denali'); ?></span>
          </div>
        </div>
      </div>


      <br style="clear:both;" />
      <p class="submit">
      <input type="submit" value="<?php _e('Save Changes', 'denali');?>" class="button-primary" name="Submit"/>
      </p>
      </form>
    </div>
    <?php
  }


  /**
   * Filter.
   * Adds additional options on Edit Agent Page.
   * Real Estate Agents premium feature must be installed and activated.
   *
   * @param array $options
   * @retun array
   * @peshkov@UD
   * @since 3.2.2
   */
  static function wpp_agent_options( $options, $user ) {
    ob_start();
    ?>
    <input type="hidden" value="off" name="agent_fields[notify_agent_on_property_inquiry]" />
    <input <?php checked( $user->notify_agent_on_property_inquiry, 'on' ); ?> type="checkbox" id="notify_agent_on_property_inquiry" value="on" name="agent_fields[notify_agent_on_property_inquiry]" />
    <label for="notify_agent_on_property_inquiry"><?php _e( 'Allow property inquiries to be sent directly to agent.', 'denali' ); ?></label>
    <?php
    $content = ob_get_clean();
    if( !is_array( $options ) ) $options = array();
    $options[] = $content;
    return $options;
  }


  /**
   * Configured default Denali widget areas
   *
   * Disables WPP widget areas, and adds them back in - so they are in order in relation to other widgets.
   *
   * @updated Denali 3.0.0
   * @since Denali 1.0
   *
   */
 static function widgets_init() {
    global $wp_properties, $denali_theme_settings;

    //** Disable WPP sidebar registration */
    $wp_properties['configuration']['do_not_register_sidebars'] = 'true';

    $show_on_front = get_option('show_on_front');
    $page_for_posts = get_option('page_for_posts');

    //** Determine if we need a Blog: Home widget area */
    if($show_on_front == 'page' && !empty($page_for_posts)) {
      $denali_theme_settings['have_blog_home'] = true;
    }

    if(!current_theme_supports('header-property-search') || $denali_theme_settings['hide_header_property_search'] == 'true') {
      $disable_header_search = true;
    }

    if($denali_theme_settings['break_out_global_property_search_areas'] == 'true') {

      if(!$disable_header_search) {
        register_sidebar( array(
          'name' => __( 'Header: Property Search' ,'denali'),
          'id' => 'global_property_search',
          'description' => __( 'Widget area is displayed in the header and is made visible when a user clicks the "Property Search" dropdown tab. The "Property Search" label will be changed to the Title of the first widget added to this section.','denali'),
          'before_widget' => '<div id="%1$s" class="denali_widget clearfix  %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<h5 class="widgettitle">',
          'after_title' => '</h5>',
        ));
      }

      register_sidebar( array(
        'name' => __( 'Home: Slideshow Overlay Search' ,'denali'),
        'id' => 'home_slideshow_overlay_property_search',
        'description' => __( 'Widget area is displayed on home page next to slideshow, if a slideshow is used. Place your property search widget in here.' ),
        'before_widget' => '<div id="%1$s" class="denali_widget clearfix  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h5 class="widgettitle">',
        'after_title' => '</h5>',
      ));


    } elseif(!$disable_header_search) {

      //** Default and legacy setting, unless header search is disabled */

      register_sidebar( array(
        'name' => __( 'Header & Home: Property Search' ),
        'id' => 'global_property_search',
        'description' => __( 'This area is displayed in the header under "Find Your Property" and on home page next to slideshow. Place your property search widget in here. The "Property Search" label will be changed to the Title of the first widget added to this section.' ),
        'before_widget' => '<div id="%1$s" class="denali_widget clearfix  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h5 class="widgettitle">',
        'after_title' => '</h5>',
      ));

    }

    register_sidebar( array(
      'name' => __( 'Home: Attention Grabber' ),
      'id' => 'home_page_attention_grabber',
      'description' => __( 'Display directly below the navigation on the home page if this area has widgets - it will replace the home page slideshow area with a custom widget area. ' ),
      'before_widget' => '<div id="%1$s" class="denali_widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h5 class="widgettitle">',
      'after_title' => '</h5>',
    ));

    // Area Featured Listings.
    register_sidebar( array(
      'name' => __( 'Home: Sidebar' ),
      'id' => 'home_sidebar',
      'description' => __( 'Sidebar located on the right side on the home page page below the property slideshow and search widget.' ),
      'before_widget' => '<div id="%1$s"  class="denali_widget clearfix  %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h5 class="widgettitle">',
      'after_title' => '</h5>',
    ) );


    if($denali_theme_settings['disable_horizontal_widget_area'] != 'true') {
      register_sidebar( array(
        'name' => __( 'Home: Horizontal Bottom Area' ),
        'id' => 'home_bottom_sidebar',
        'description' => __( 'Widget area is located below home page content on left side. This is a good place for the "Featured Properties" widget.' ),
        'before_widget' => '<div id="%1$s" class="denali_widget clearfix %2$s">',
        'after_widget' => '</div>'
      ) );
    }

    if($denali_theme_settings['have_blog_home']) {

      register_sidebar( array(
        'name' => __( 'Blog Home: Attention Grabber' ),
        'id' => 'post_page_attention_grabber',
        'description' => __( 'Display directly below the navigation on the blog home page if this area has widgets.' ),
        'before_widget' => '<div id="%1$s" class="denali_widget  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h5 class="widgettitle">',
        'after_title' => '</h5>',
      ));

      register_sidebar( array(
        'name' => __( 'Blog Home: Sidebar' ),
        'id' => 'posts_page_sidebar',
        'description' => __( 'Sidebar located on the "Posts page" home page if you have one selected under Settings -> Reading -> Front page displays. "' ),
        'before_widget' => '<div id="%1$s"  class="denali_widget clearfix  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h5 class="widgettitle">',
        'after_title' => '</h5>',
      ));
    }


    register_sidebar( array(
      'name' => __( 'Inside Pages: Attention Grabber' ),
      'id' => 'inside_attention_grabber',
      'description' => __( 'This area is displayed on all inside pages, such as posts, pages, categories, etc. directly below the navigation.  It replaces the default image or slideshow.' ),
      'before_widget' => '<div id="%1$s" class="denali_widget  %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h5 class="widgettitle">',
      'after_title' => '</h5>',
    ) );


    register_sidebar( array(
      'name' => __( 'Inside Pages: Sidebar' ),
      'id' => 'right_sidebar',
      'description' => __( 'This widget area shows up on the right side of all "inside" pages, or any page other than the home page or a property page.  These pages include posts, pages, categories, etc.' ),
      'before_widget' => '<div id="%1$s" class="denali_widget clearfix  %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h5 class="widgettitle">',
      'after_title' => '</h5>',
    ) );

    // Register a sidebar for each property type
    if(is_array($wp_properties['property_types'])) {
      foreach($wp_properties['property_types'] as $property_slug => $property_title) {

        register_sidebar( array(
          'name'=> sprintf(__('%s: Attention Grabber','denali'), $property_title),
          'id' => "wpp_header_{$property_slug}",
          'description' =>  sprintf(__('Widget area is located on the top of all  %s pages. All the widgets in here are rendered as tabs - the widget titles are used as the tab titles, and the actual content is displayed only when a tab is activated.','denali'), $property_title),
          'before_widget' => '<div id="%1$s"  class="wpp_widget wpp_header_property_widget_area %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<h3 class="widget-title wpp_header_title">',
          'after_title' => '</h3>',
        ));

        register_sidebar( array(
          'name'=> sprintf(__('%s: Right Sidebar','denali'), $property_title),
          'id' => "wpp_sidebar_{$property_slug}",
          'description' =>  sprintf(__('Sidebar located on the right of all %s pages.','denali'), $property_title),
          'before_widget' => '<li id="%1$s"  class="wpp_widget wpp_sidebar_widget_area %2$s">',
          'after_widget' => '</li>',
          'before_title' => '<h3 class="widget-title">',
          'after_title' => '</h3>',
        ));

        register_sidebar( array(
          'name'=> sprintf(__('%s: Below Content','denali'), $property_title),
          'id' => "wpp_foooter_{$property_slug}",
          'description' =>  sprintf(__('Widget area is located below the content on all %s pages.  All the widgets in here are rendered as tabs - the widget titles are used as the tab titles, and the actual content is displayed only when a tab is activated.','denali'), $property_title),
          'before_widget' => '<div id="%1$s"  class="wpp_widget wpp_footer_property_widget_area %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<h3 class="widget-title wpp_header_title">',
          'after_title' => '</h3>',
        ));

      }
    }

    register_sidebar( array(
      'name' => __( 'Property Listing: Sidebar' ),
      'id' => 'property_overview_sidebar',
      'description' => __( 'Sidebar located on the the property overview and search result pages whenever a [property_overview] shortcode is detected within page content. "' ),
      'before_widget' => '<div id="%1$s"  class="denali_widget clearfix  %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h5 class="widgettitle">',
      'after_title' => '</h5>',
    ) );


    if($denali_theme_settings['disable_property_horizontal_widget_area'] != 'true') {
      register_sidebar( array(
        'name' => __( 'All Single Property Pages: Below Content' ),
        'id' => 'denali_property_footer',
        'description' => __( 'Appears on every single property page below the content and below the Property Footer if one is set.' ),
        'before_widget' => '<div id="%1$s" class="denali_widget clearfix  %2$s">',
        'after_widget' => '</div>'
      ) );
    }

    if($denali_theme_settings['disable_footer_bottom_left_block_widget_area'] != 'true') {
     register_sidebar( array(
      'name' => __( 'Footer: Bottom Left Block' ),
      'id' => 'latest_listings',
      'description' => __( 'This widget area is in the bottom left corner.  It works best with listing properties, showing only thumbnails.' ),
      'before_widget' => '<div id="%1$s" class="denali_widget clearfix %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h5 class="widgettitle">',
      'after_title' => '</h5>',
      ));
    }

    do_action('denali_widgets_init');

  }


  /**
   * Get Widget Titles and Instances in an area
   *
   * @since Denali 1.0
   */
  function widget_area_tabs($widget_area = false) {
    global $wp_registered_widgets, $wp_registered_sidebars;

    /** Check if widget are is active before doing anything else */
    if(!denali_theme::is_active_sidebar($widget_area)) {
      return false;
    }

    $sidebars_widgets = wp_get_sidebars_widgets();
    $sidebar = $wp_registered_sidebars[$widget_area];
    $load_options = array();

    if(empty($sidebars_widgets)) {
      return false;
    }
    elseif(empty($sidebars_widgets[$widget_area]) || !is_array($sidebars_widgets[$widget_area])) {
      return false;
    }

    foreach($sidebars_widgets[$widget_area] as $count=> $id) {
      if ( !isset($wp_registered_widgets[$id]) ) {
        continue;
      }
      $callback = $wp_registered_widgets[$id]['callback'];
      $number = $wp_registered_widgets[$id]['params'][0]['number'];
      $option_name = $callback[0]->option_name;
      $type =  $wp_registered_widgets[$id]['name'];
      $name = trim($wp_registered_widgets[$id]['name']);
      /** Get params */
      $params = array_merge(
        array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
        (array) $wp_registered_widgets[$id]['params']
      );
      $params = apply_filters( 'dynamic_sidebar_params', $params );

      if(!isset($load_options[$option_name])) {
        $all_options = get_option($option_name);
        $load_options[$option_name] = $all_options;
      }

      $these_settings = $load_options[$option_name][$number];

      $title = trim($these_settings['title']);

      /** Determine if widget is callable and has content */
      if ( is_callable($callback) ) {
        ob_start();
        call_user_func_array($callback, $params);
        $content = ob_get_contents();
        ob_end_clean();
        if(!empty($content)) {
          $return[$count]['title'] = (!empty($title) ? $title : $name);
          $return[$count]['id'] = $wp_registered_widgets[$id]['id'];
          $return[$count]['callable'] = true;
        }
      }

    }

    if(is_array($return)) {
      return $return;
    }

    return false;

  }


  /**
   * Add CSS to comment page for phone number
   *
   * @since Denali 1.0
   */
  function comment_page_css() {
    echo '<style type="text/css">th#phone_number{width: 120px;}</style>';
  }


  /**
   * Add Phone number column
   *
   * @since Denali 1.0
   */
  function add_inquiry_columns($columns) {
    global $denali_theme_settings;

    //* Load some default settings */
    if(!isset($denali_theme_settings['wpp_d_inquiry_fields'])) {
      $denali_theme_settings['wpp_d_inquiry_fields']['phone_number']['name'] = __('Phone Number','denali');
    }

    foreach($denali_theme_settings['wpp_d_inquiry_fields'] as $slug => $data) {
      $columns[$slug] = $data['name'];
    }

    return $columns;
  }


  /**
   * Display phone number in column in comment row
   *
   * @since Denali 1.0
   */
  function manage_comments_custom_column($column_name, $comment_ID) {
    global $denali_theme_settings;

    // Load some default settings
    if(!isset($denali_theme_settings['wpp_d_inquiry_fields']))
      $denali_theme_settings['wpp_d_inquiry_fields']['phone_number']['name'] = __('Phone Number','denali');

    foreach($denali_theme_settings['wpp_d_inquiry_fields'] as $slug => $data) {
      if($column_name == $slug) {
        echo get_comment_meta($comment_ID, $slug, true);
      }
    }
  }


  /**
   * Determine if the current comment is Inquiry.
   * Used as hook for filter 'pre_render_inquiry_form'.
   *
   * @since Denali 2.1
   * @author Maxim Peshkov
   */
  function pre_render_inquiry_form ($inquiry) {
    global $denali_theme_settings;

    /* Determine if we are no using property comments as inquiries */
    if($denali_theme_settings['show_property_comments'] == 'true') {
      $inquiry = false;
    }

    return $inquiry;
  }


  /**
   * Handles remaining functionality after comment creation
   * to avoid sending notification to moderator/postauthor (actually, admin)
   * if the current comment is Inquiry
   * AND if comment's post is property and real agent exist in delivery
   * OR admin has disabled admin notifications.
   *
   * @since Denali 2.1
   * @author peshkov@UD
   */
  function pre_send_admin_inquiry_notification ($comment_ID, $approved) {
    global $post, $denali_theme_settings;

    /* Determine if the current comment is spam */
    if($approved === 'spam') {
      return;
    }

    /* Determine if post is not property */
    if($post->post_type != 'property') {
      return;
    }

    /* Bail if we are no using property comments as inquiries */
    if($denali_theme_settings['show_property_comments'] == 'true') {
      return;
    }

    /* Determine if Real Agent exist in delivery */
    if(empty($_REQUEST['wpp_agent_contact_id'])) {
      return;
    }

    /*
     * If all conditions are passed
     * Handle remaining functionality after comment creation
     * this functionality is duplicated from /wp-comments-post.php
     */
    $user = wp_get_current_user();
    $comment = get_comment($comment_ID);
    if ( !$user->ID ) {
      $comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
      setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
      setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
      setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
    }

    $location = empty($_POST['redirect_to']) ? get_comment_link($comment_ID) : $_POST['redirect_to'] . '#comment-' . $comment_ID;
    $location = apply_filters('comment_post_redirect', $location, $comment);

    wp_redirect($location);
    exit;
  }


  /**
   * Process property inquiry saving
   *
   * @since Denali 1.0
   */
  function wp_insert_comment($id, $comment) {
    global $wpdb, $current_user, $denali_theme_settings;

    // Bail if we are no using property comments as inquiries
    if($denali_theme_settings['show_property_comments'] == 'true') {
      return;
    }

    // Bail if comment is not about a property
    if($wpdb->get_var("SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = '{$comment->comment_post_ID}'") != 'property') {
      return;
    }

    $property_id = $comment->comment_post_ID;

    if (is_user_logged_in()) {
      get_currentuserinfo();
      $inquiry['name'] = $current_user->display_name;
      $inquiry['email'] = $current_user->user_email;
    } else {
      $inquiry['name'] = $_REQUEST['author'];
      $inquiry['email'] = $_REQUEST['email'];
    }

    $inquiry['message'] = $_REQUEST['comment'];
    $inquiry['property'] = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE ID = '{$property_id}' ") . " ({$property_id})";
    $inquiry['property_link'] = get_permalink($property_id);

    // Load some default settings
    if(!isset($denali_theme_settings['wpp_d_inquiry_fields']))
      $denali_theme_settings['wpp_d_inquiry_fields']['phone_number']['name'] = __('Phone Number','denali');

    foreach($denali_theme_settings['wpp_d_inquiry_fields'] as $slug => $data) {
        // Check if phone number is set, if so - save
        if(!empty($_REQUEST[$slug])) {
          $new_value = $_REQUEST[$slug];
          add_comment_meta($id, $slug, $new_value);
          $inquiry[$slug] = $new_value;
        }
    }

    do_action('wpp_insert_property_comment', $comment, $inquiry);

  }


  /**
   * Sends notification to agent specified during inquiry
   *
   *
   * @since Denali 1.0
   */
  function send_agent_inquiry_notification($comment, $inquiry) {
    global $wpdb;

    if(!isset($_REQUEST['wpp_agent_contact_id'])) {
      $_REQUEST['class_agents_sent_agent_notification'] = false;
      return;
    }

    if(!method_exists('class_agents','send_agent_notification')) {
      return;
    }

    $subject = __('Inquiry About:','denali') . " " . $inquiry['property'];

    foreach($inquiry as $key => $value) {
      $message_lines[] = UD_F::de_slug($key) . ": {$value}";
    }

    $message = __('Inquiry message:','denali') . "\n\n" . implode("\n", (array)$message_lines);

    $agent_ids = explode(",", $_REQUEST['wpp_agent_contact_id']);

    $send_failed=false;
    foreach($agent_ids as $id) {
      $id = (int)$id;
      if (!class_agents::send_agent_notification($id, $subject, $message)){
        $send_failed = true;
      }
    }

    $_REQUEST['class_agents_sent_agent_notification'] = ((empty($agent_ids)) || $send_failed) ? false : true;

    return;
  }


  /**
   *
   * @param type $location
   * @return type
   */
  function comment_post_redirect($location){

    if (isset($_REQUEST['class_agents_sent_agent_notification'])){
      $parsed = parse_url($location);
      $parsed['query'] = ((isset($parsed['query']))?$parsed['query']."&":'') . "inquiry_sent=".(($_REQUEST['class_agents_sent_agent_notification'])?'2':'1');
      if ($denali_theme_settings['show_property_comments'] != 'true'){
        $parsed['fragment'] = "inquiry_form";
      }
      $location = denali_theme::glue_url($parsed);
    }

    return $location;

  }


  /**
   *
   * @param type $parsed
   * @return boolean
   */
  static function glue_url($parsed) {
      if (!is_array($parsed)) {
          return false;
      }

      $uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
      $uri .= isset($parsed['user']) ? $parsed['user'].(isset($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
      $uri .= isset($parsed['host']) ? $parsed['host'] : '';
      $uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';

      if (isset($parsed['path'])) {
          $uri .= (substr($parsed['path'], 0, 1) == '/') ?
              $parsed['path'] : ((!empty($uri) ? '/' : '' ) . $parsed['path']);
      }

      $uri .= isset($parsed['query']) ? '?'.$parsed['query'] : '';
      $uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';

      return $uri;
  }


  /**
   * Returns a copyright range of years where
   * the first date is a creation date of the first post and
   * the last date is the date when the last post was created or modified
   * @global type $wpdb
   * @return string
   * @author odokienko@ud
   */
  function denaly_copyright() {
    global $wpdb;
    $copyright_dates = $wpdb->get_results("
      SELECT
      YEAR(min(post_date_gmt)) AS firstdate,
      GREATEST(YEAR(max(post_date_gmt)),YEAR(max(post_modified_gmt))) AS lastdate
      FROM
      $wpdb->posts
      WHERE
      post_status = 'publish'
    ");
    $output = '';
    if($copyright_dates) {
      $copyright = $copyright_dates[0]->firstdate;
      if($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {
        $copyright .= '-' . $copyright_dates[0]->lastdate;
      }
      $output = $copyright;
    }
    return $output;
  }


  /**
   *
   * @return string
   */
  function powered_by(){
    return '<a href="https://usabilitydynamics.com/products/the-denali-premium-theme/">The Denali</a> theme by <a href="https://usabilitydynamics.com">Usability Dynamics, Inc.</a>';
  }


  /**
   * Replace comment fields when comment form is being used for property inquiries
   *
   *
   * @since Denali 1.0
   */
  function comment_form_defaults($defaults) {
    global $post, $wpdb, $denali_theme_settings;

    $defaults['comment_field'] = str_replace(' cols="45"', '', $defaults['comment_field']);

    if($denali_theme_settings['show_property_comments'] == 'true') {
      return $defaults;
    }

    if($post->post_type !=  'property') {
      return $defaults;
    }

    // Remove website URL field
    unset($defaults['fields']['url']);

    // Rename "Comment" to "Message"
    $defaults['comment_field'] = '<p class="comment-form-comment">' .
    '<label for="comment">' . __( 'Message','denali') . '</label>' .
    '<textarea id="comment" name="comment" rows="8" aria-required="true"></textarea>' .
    '</p><!-- #form-section-comment .form-section -->';

    // Load some default settings
    if(!isset($denali_theme_settings['wpp_d_inquiry_fields']))
      $denali_theme_settings['wpp_d_inquiry_fields']['phone_number']['name'] = __('Phone Number','denali');

    /* If user logged in we clear all default fields */
    if (is_user_logged_in()) {
      $defaults['fields'] = array();
    }

    foreach($denali_theme_settings['wpp_d_inquiry_fields'] as $slug => $data) {
      unset($required);
      unset($class);

      $label = $data['name'];

      if($data['required'] == 'on') {
        $class = 'wpp_required_field';
        $label = $label . '<span class="required">*</span>';
        $required = " aria-required='true' ";
      }

      $defaults['fields'][$slug] = '<p class="comment-form-'.$slug.' '.$class.'">' .
      '<label for="'.$slug.'">' .$label . '</label> ' .
      '<input id="'.$slug.'" name="'.$slug.'" type="text" size="30" '.$required.' />' .
      '</p>';
    }
    /** Show agent */
    if(is_array($post->wpp_agents)) {
      $agents = array();
      /** Determine if form should contains dropdown list of agents */
      if($denali_theme_settings['wpp_d_show_agent_dropdown_on_inquiry'] == 'on') {
        foreach($post->wpp_agents as $agent_id) {
          /** Does agent accept notifications? */
          if(get_user_meta($agent_id, 'notify_agent_on_property_inquiry', true) != 'on') {
            continue;
          }
          $agents[] = $agent_id;
          $agent_name = $wpdb->get_var("SELECT display_name FROM {$wpdb->users} WHERE ID = '$agent_id'");
          if($agent_name) {
            $agent_options[]  = "<option value='$agent_id'>$agent_name</option>";
          }
        }
        if(count($agent_options) > 1) {
          $agent_dropdown = "<select id='wpp_agent' name='wpp_agent_contact_id'>" . implode('', $agent_options) . "</select>";
          $defaults['fields']['wpp_agent'] = '<p class="comment-form-wpp_agent">' .'<label for="wpp_agent">'.__('Send to Agent','denali') . '</label>' . $agent_dropdown . '</p>';
        } elseif(count($agents) == 1) {
          $defaults['fields']['wpp_agent'] = "<input type='hidden' name='wpp_agent_contact_id' value='{$agents[0]}' />";
        }

      } else {
        foreach($post->wpp_agents as $agent_id) {
          /** Does agent accept notifications? */
          if(get_user_meta($agent_id, 'notify_agent_on_property_inquiry', true) != 'on') {
            continue;
          }
          $agents[] = $agent_id;
        }
        if(!empty($agents)) {
          $agents = implode(',',$agents);
          $defaults['fields']['wpp_agent'] = "<input type='hidden' name='wpp_agent_contact_id' value='{$agents}' />";
        }
      }
    }

    /** Rename submitbutton */
    $defaults['label_submit'] = __('Submit Enquiry','denali');

    return $defaults;
  }


  /**
   * Checks Denali's new version (makes request to TCT server once per day)
   * Works like filter
   *
   * @param object $value contains information about current and new available versions of themes
   * @return object
   */
  static function check_denali_updates($value){
    global $denali_theme_settings;

    $denali_theme = get_theme('Denali');

    if(empty($denali_theme) || $denali_theme_settings['ignore_theme_update'] == 'true') {
      return $value;
    }

    //** The option contains information about available theme's version and about time of last request to server */
    $versionData = get_option('denali_version_updates');

    //** Determine if option doesn't exist */
    if(!$versionData) {
      $versionData = array();
      add_option('denali_version_updates', $versionData);
    }

    //** Checks time of last request to UD Server */
    //** If it is more then one day ago, - do request and update option 'denali_version_updates' */
    if( !isset($versionData['last_checked']) || (time() - $versionData['last_checked']) > 86400 ) {
      $url = "http://updates.usabilitydynamics.com/themes/denali?refer=".urlencode(get_bloginfo('siteurl'));

      $result = wp_remote_get($url);
      if( is_wp_error( $response )) {
        return $value;
      }
      $result = (array)$result;
      if( empty( $result['body'] ) ) {
        return $value;
      }

      $versionData = json_decode($result['body'], true);
      $versionData['last_checked'] = time();
      update_option('denali_version_updates', $versionData);
    }

    //** If the current version is older then available new one, */
    //** Add ability to update it (by adding information to object) */
    if ( version_compare($denali_theme['Version'], $versionData['version'], '<') ) {
      if(empty($value->checked)) {
        $value->checked = array();
      }

      $value->checked['denali'] = $denali_theme['Version'];

      if(empty($value->response)) {
        $value->response = array();
      }

      //* Get template of theme */
      if( is_object( $denali_theme ) && get_class( $denali_theme ) == 'WP_Theme' ) {
        $template = $denali_theme->get_template();
      } else {
        $denali_theme = (array) $denali_theme;
        $template = !empty( $denali_theme['template'] ) ? $denali_theme['template'] : '';
      }

      if( !empty( $template ) ) {
        $value->response[$template] = array(
          'new_version' => $versionData['version'],
          'url' => 'http://sites.usabilitydynamics.com/the-denali',
          'package' => 'http://updates.usabilitydynamics.com/themes/denali/download?refer='.urlencode(get_bloginfo('siteurl'))
        );
      }
    }

    return $value;
  }

  /**
   * Prints Error admin notice if WP-Property plugin is not activated.
   * @author peshkov@UD
   */
  static function show_wpp_error_notice() {
    $default_theme = false;
    $themes = get_themes();
    foreach($themes as $k => $v) {
      if(in_array($k, array("Twenty Eleven","Twenty Ten"))) {
        $default_theme = true;
      }
    }
    if ( $default_theme ) {
      echo '<div class="error"><p>';
      _e("Sorry, but Denali theme was switched to default Worpress theme. You have to activate WP-Property plugin for using Denali theme. Please, activate WP-Property plugin and then try again. To avoid issues you need to reload the current page.","denali");
      echo "</p></div>";
    } else {
      echo '<div class="error"><p>';
      _e("You have to activate WP-Property plugin for using Denali theme. In the other way it can cause different bugs and issues. Please activate WP-Property plugin or switch theme to another one.","denali");
      echo "</p></div>";
    }
  }


  /**
   * Adds option for showing notice on Theme Settings updating
   * And shows Notice 'Clear W3 Cache' if W3 Total Cache plugin is used
   *
   */
  static function show_clear_W3_total_cache_notice() {

    if(class_exists('W3_Plugin_TotalCache')) {

      // Checks Denali Settings Request and Add option
      if(wp_verify_nonce($_REQUEST['_wpnonce'], 'denali_settings')) {
          add_option('denali_theme_clear_cache_notice', 'true');
      }

      $clear_notice = get_option('denali_theme_clear_cache_notice');
      if(!empty($clear_notice)) {
        $note = '';
        ob_start();
        ?>
        <p><?php _e('Looks like Denali theme Settings were updated. But W3 Total Cache plugin is used. Please, clear cache to be sure that the changes are involved ','denali'); ?>
        <input type="button" value="Clear Page Cache" onclick="denali_delete_clearcache_option();document.location.href = 'admin.php?page=w3tc_general&amp;flush_pgcache';" class="button " />
        <?php _e('or','denali') ?>
        <input type="button" value="Hide Notice" onclick="denali_delete_clearcache_option();denali_hide_notice();" class="button " />
        </p>
        <script type="text/javascript">
           function denali_delete_clearcache_option() {
                jQuery.ajax({
                    url: ajaxurl,
                    async: false,
                    type: 'POST',
                    data: 'action=denali_delete_option_clearcache'
                });
            }

           function denali_hide_notice() {
                jQuery('#denali_w3_total_cache_notice').slideToggle('slow', function(){
                    jQuery(this).remove();
                });

            }
        </script>
        <?php
        $note .= ob_get_contents();
        ob_end_clean();

        // Print notice
        echo sprintf('<div id="denali_w3_total_cache_notice" class="updated fade">%s</div>', $note);
      }
    } else {
      // Try to delete option
      delete_option('denali_theme_clear_cache_notice');
    }
  }


  /**
   * Ajax function. Deletes 'denali_theme_clear_cache_notice' option,
   * which is used for showing notice to clear W3 Cache if W3 Total Cache plugin is used.
   */
  static function delete_option_clearcache () {
    delete_option('denali_theme_clear_cache_notice');
    echo json_encode(array('status'=>'success'));
    exit();
  }


  /**
   * Generates dummy agent(s)
   *
   *
   * @since Denali 1.0
   */
  function generate_dummy_agents( ) {
     global $wpdb;

     $dummy_properties = $wpdb->get_col("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'dummy_property'");

     if(!email_exists('agent.john@usabilitydynamics.com')) {

      $agent_id = class_agents::create_agent('agent.john', 'Agent John', 'agent.john@usabilitydynamics.com');

      if($agent_id) {
        update_user_meta($agent_id, 'first_name', 'Agent');
        update_user_meta($agent_id, 'last_name', 'John');
        update_user_meta($agent_id, 'widget_bio', "Agent John is a sample agent created for demonstration purposes only. Take a look at all of John\'s properties here.");
        update_user_meta($agent_id, 'phone_number', '800-270-0781');
        update_user_meta($agent_id, 'website_url', 'http://UsabilityDynamics.com');
      }

     } else {

      $agent_id = email_exists('agent.john@usabilitydynamics.com');
     }

     foreach($dummy_properties as $post_id){
      delete_post_meta($post_id, 'wpp_agents');
      add_post_meta($post_id, 'wpp_agents', $agent_id);
     }

      //** Create dedicated agent page */
      if(!$wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'agent_johns_listings' ")) {
        //** Create Agent Page */
        $insert_id = wp_insert_post(  array(
          'post_title' => 'Agent John',
          'post_status' => 'publish',
          'post_name' => 'agent_johns_listings',
          'post_content' => "This is an automatically generated page for Agent John.  [property_overview template=grid wpp_agents={$agent_id}]",
          'post_type' => 'page'
        ));
      }
  }


  /**
   * Generates dummy properties
   *
   * Only ran if static image must be displayed, checks if static image is big enough to fill area, if setting is set to do so
   *
   * @since Denali 1.0
   */
  function generate_dummy_properties( ) {
    global $user_ID, $wp_properties, $wpdb;

    /* Determine if the dummy properties already exist */
    $posts = $wpdb->get_col("
      SELECT `post_title`
      FROM {$wpdb->posts}
      WHERE `post_title` IN ('122 Bishopsgate', '2 Bedroom Home')
    ");
    /* Check array to avoid issues in future */
    if(!is_array($posts)) {
      $posts = array();
    }

    /* If Property doesn't exist we create it */
    if ( !in_array( '122 Bishopsgate', $posts ) ) {

      self::generate_dummy_property( array(
        'post_title' => '122 Bishopsgate',
        'post_content' => 'Take notice of this amazing home! It has an original detached 2 garage/workshop built with the home and on a concrete slab along with regular 2 car attached garage. Very nicely landscaped front and back yard. Hardwood floors in Foyer, den, dining and great room. Great room is open to large Kitchen. Carpet in all upstairs bedrooms. Home is located in the Woodlands in the middle of very nice community. You and your family will feel right at home. A must see.',
        'tagline' => 'Need Room for your TOYS! Take notice of this unique Home!',
        'location' => '122 Bishopsgate, Jacksonville, NC 28540, USA',
        'price' => '195000',
        'bedrooms' => '4',
        'bathrooms' => '4',
        'phone_number' => '8002700781',
        'img_index' => '1',
      ) );

    }

    /* If Property doesn't exist we create it */
    if (!in_array('2 Bedroom Home', $posts)) {

      self::generate_dummy_property( array(
        'post_title' => '2 Bedroom Home',
        'post_content' => 'Donec volutpat elit malesuada eros porttitor blandit. Donec sit amet ligula quis tortor molestie sagittis tincidunt at tortor. Phasellus augue leo, molestie in ultricies gravida; blandit et diam. Curabitur quis nisl eros! Proin quis nisi quam, sit amet lacinia nisi. Vivamus sollicitudin magna eu ipsum blandit tempor. Duis rhoncus orci at massa consequat et egestas lectus ornare? Duis a neque magna, quis placerat lacus. Phasellus non nunc sapien, id cursus mi! Mauris sit amet nisi vel felis molestie pretium.',
        'tagline' => 'Great starter home in beautiful St. Paul, Minnesota.',
        'location' => '332 S Main St, St Paul, Minnesota',
        'price' => '119000',
        'bedrooms' => '3',
        'bathrooms' => '2',
        'phone_number' => '8002700781',
        'img_index' => '2',
      ) );

    }

  }


  /**
   * Creates dummy property
   *
   * @param array $data
   * @author peshkov@UD
   * @since 3.2
   */
  static function generate_dummy_property( $data ) {
    global $wp_properties;

    $defaults = array(
      'post_title' => 'Dummy Listing',
      'post_content' => 'Donec volutpat elit malesuada eros porttitor blandit. Donec sit amet ligula quis tortor molestie sagittis tincidunt at tortor. Phasellus augue leo, molestie in ultricies gravida; blandit et diam. Curabitur quis nisl eros! Proin quis nisi quam, sit amet lacinia nisi. Vivamus sollicitudin magna eu ipsum blandit tempor. Duis rhoncus orci at massa consequat et egestas lectus ornare? Duis a neque magna, quis placerat lacus. Phasellus non nunc sapien, id cursus mi! Mauris sit amet nisi vel felis molestie pretium.',
      'tagline' => 'Donec volutpat elit malesuada eros porttitor blandit',
      'location' => '122 Bishopsgate, Jacksonville, NC 28540, USA',
      'property_type' => 'single_family_home',
      'img_index' => '1', // Available: '1', '2'
      'price' => '',
      'bedrooms' => '',
      'bathrooms' => '',
      'phone_number' => '',
    );

    $data = wp_parse_args( $data, $defaults );

    //** STEP 1. Create dummy property */

    $insert_id = wp_insert_post(  array(
      'post_title' => $data[ 'post_title' ],
      'post_status' => 'publish',
      'post_content' => $data[ 'post_content' ],
      'post_type' => 'property',
    ));

    $property_type = '';
    if( is_array( $wp_properties[ 'property_types' ] ) && !empty( $wp_properties[ 'property_types' ] ) ) {
      reset( $wp_properties[ 'property_types' ] );
      $property_type = key_exists( $defaults[ 'property_type' ], $wp_properties[ 'property_types' ] ) ? $defaults[ 'property_type' ] : key( $wp_properties[ 'property_types' ] );
    }
    update_post_meta( $insert_id, 'property_type', $property_type );

    if( !empty( $wp_properties[ 'configuration' ][ 'address_attribute' ] ) && key_exists( $wp_properties[ 'configuration' ][ 'address_attribute' ], $wp_properties[ 'property_stats' ] ) ) {
      update_post_meta( $insert_id, $wp_properties[ 'configuration' ][ 'address_attribute' ], $data[ 'location' ] );

      if( method_exists( 'WPP_F', 'revalidate_address' ) ) {
        WPP_F::revalidate_address( $insert_id );
      }
    }

    if( !empty( $wp_properties[ 'property_stats' ][ 'tagline' ] ) || !empty( $wp_properties[ 'property_meta' ][ 'tagline' ] ) ) {
      update_post_meta( $insert_id, 'tagline', $data[ 'tagline' ] );
    }

    if( !empty( $wp_properties[ 'property_stats' ][ 'price' ] ) || !empty( $wp_properties[ 'property_meta' ][ 'price' ] ) ) {
      update_post_meta( $insert_id, 'price', $data[ 'price' ] );
    }

    if( !empty( $wp_properties[ 'property_stats' ][ 'bedrooms' ] ) || !empty( $wp_properties[ 'property_meta' ][ 'bedrooms' ] ) ) {
      update_post_meta( $insert_id, 'bedrooms', $data[ 'bedrooms' ] );
    }

    if( !empty( $wp_properties[ 'property_stats' ][ 'bathrooms' ] ) || !empty( $wp_properties[ 'property_meta' ][ 'bathrooms' ] ) ) {
      update_post_meta( $insert_id, 'bathrooms', $data[ 'bathrooms' ] );
    }

    if( !empty( $wp_properties[ 'property_stats' ][ 'phone_number' ] ) || !empty( $wp_properties[ 'property_meta' ][ 'phone_number' ] ) ) {
      update_post_meta( $insert_id, 'phone_number', $data[ 'phone_number' ] );
    }

    update_post_meta( $insert_id, 'dummy_property', true);

    //** STEP 2. Create and Move temporary image files */

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $upload_dir = wp_upload_dir();

    $dummy_images = array(
      TEMPLATEPATH . "/img/dummy_data/property_{$data[ 'img_index' ]}_img_0.jpg",
      TEMPLATEPATH . "/img/dummy_data/property_{$data[ 'img_index' ]}_img_1.jpg",
      TEMPLATEPATH . "/img/dummy_data/property_{$data[ 'img_index' ]}_img_2.jpg"
    );

    foreach( $dummy_images as $dummy_path ) {
      if( @copy($dummy_path, $upload_dir['path'] . "/" . basename( $dummy_path ) ) ) {
        $filename = $upload_dir['path'] . "/" . basename( $dummy_path );
        $wp_filetype = wp_check_filetype(basename($filename), null );

        $attach_id = wp_insert_attachment(  array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
        'post_status' => 'inherit'
        ), $filename, $insert_id );

        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
      }
    }

    //** Last attached file is set as thumbnail */
    if( isset( $attach_id ) ) {
      update_post_meta( $insert_id, '_thumbnail_id', $attach_id );
    }

  }


  /**
   * Installs a Denali child theme.
   *
   * Copies files from /denali-child folder into the them folder so denali child can be used.
   *
   * @since Denali 1.0
   */
  function install_child_theme( ) {
    global $user_ID, $wp_properties, $wpdb, $wp_theme_directories;

    if(denali_theme::denali_child_theme_exists()) {
      return true;
    }

    $destination_root = $wp_theme_directories[0];

    $original = TEMPLATEPATH . '/denali-child';
    $original_images = TEMPLATEPATH . '/img';

    if(!file_exists($original)) {
     return false;
    }

    if(!is_writable($destination_root)) {
     return false;
    }

    $destination = $destination_root . '/denali-child';
    $destination_images = $destination_root . '/denali-child/img';

    //** Create destination folder */
    if (!@mkdir($destination, 0755)) {
      return false;
    } else {
      @mkdir($destination_images, 0755);
    }

    //** Copy folders from denali/denali-child into denali-chlld
    if ($original_handle = opendir($original . '/')) {
      while (false !== ($file = readdir($original_handle))) {

       if ($file != "." && $file != "..") {

         $file_path = $original . '/'. $file;

         /* Determine if it's directory, We don't copy it */
         if (is_dir($file_path)) {
           continue;
         }

         if(copy($file_path, $destination . '/' . $file)) {
           $copied[] = $file;

           //** Determine if Denali directory has custom name and set actual parent directory name in child-theme. peshkov@UD */
           if($file == 'style.css' && file_exists( $destination . '/' . $file ) && !is_child_theme() ) {
             $fp = @fopen( $destination . '/' . $file, 'r+' );
             if( $fp ) {
               $template = get_template();
               $content = fread( $fp, filesize( $destination . '/' . $file ) );
               $content = preg_replace( '/(Template:\s*)denali([\r\n\s]+)/', '$1' . $template . '$2', $content );
               $content = preg_replace( '/\.\.\/denali\//', '../' . $template . '/', $content );
               ftruncate( $fp, 0 );
               fseek( $fp, 0 );
               fwrite( $fp, $content);
               fclose( $fp );
             }
           }

         }  else {
           $not_copied[] = $file;
         }
       }

      }
    }

    //** Copy image files */
    if ($images_handle = opendir($original_images . '/')) {
      while (false !== ($file = readdir($images_handle))) {

        if ($file == "." || $file == "..") {
          continue;
        }

        $file_path = $original_images . '/'. $file;

        /* Determine if it's directory, We don't copy it */
        if (is_dir($file_path)) {
          continue;
        }

        if(copy($file_path, $destination_images . '/' . $file)) {
          $copied[] = $file;
        }  else {
          $not_copied[] = $file;
        }

      }
    }

    if(count($copied) > 0) {
      return true;
    }

    return false;

  }


  /**
   * Check if default denali child theme exists.
   *
   *
   * @since Denali 1.0
   */
  function denali_child_theme_exists() {
    global $user_ID, $wp_properties, $wpdb, $denali_theme_settings;

    if(file_exists(ABSPATH . '/wp-content/themes/denali-child')) {
      $denali_theme_settings['install_denali_child_theme'] = 'true';
      update_option('denali_theme_settings', $denali_theme_settings);
      return true;
    }
    return false;
  }


  /**
   * Checks if sidebar is active. Same as default function, but allows hooks
   *
   * @since Denali 3.0
   */
  function is_active_sidebar($sidebar) {
    global $wp_properties;

    if($wp_properties['configuration']['developer_mode'] == 'true') {

      if(isset($_REQUEST[$sidebar])) {
        return ($_REQUEST[$sidebar] == 'hide' ? false : true);
      }

    }
    return is_active_sidebar($sidebar);
  }


  /**
   * Hack.
   * Fixes adding extra widgets on first Denali activation.
   *
   * @author peshkov@UD
   * @global array $sidebars_widgets
   */
  function after_switch_theme() {
    global $sidebars_widgets;

    $old_sidebars_widgets = get_theme_mod( 'sidebars_widgets' );
    if ( !is_array( $old_sidebars_widgets ) && is_array($sidebars_widgets) ) {
      foreach($sidebars_widgets as $k => $v) {
        if(in_array($k, array('wp_inactive_widgets'))) continue;
        $sidebars_widgets[$k] = array();
      }
    }
  }


  /**
   * Execute console log if function exists in WPP
   *
   * @since Denali 3.0
   *
   */
  function console_log($text) {
    if(is_callable('WPP_F::console_log')) {
      WPP_F::console_log($text);
    }
  }


  /**
   * Enqueue specific scripts and styles on FEPS form and spc pages
   *
   * @param type $args
   * @author peshkov@UD
   * @since 3.2.1
   */
  function feps_shortcode_action( $args ) {
    /** Register js/css for custom inputs design */
    wp_enqueue_script( 'custom-inputs', get_bloginfo('template_url') . "/third-party/customInputs/custom.inputs.js" );
    wp_enqueue_style( 'custom-inputs', get_bloginfo('template_url') . "/third-party/customInputs/custom.inputs.css" );
  }

}  /* end denali_theme class */


if ( ! function_exists( 'denali_posted_on' ) ) {
  /**
   * Prints HTML with meta information for the current post date/time and author.
   *
   * @since Denali 1.0
   */
  function denali_posted_on() {
      printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'denali' ),
          'meta-prep meta-prep-author',
          sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
              get_permalink(),
              esc_attr( get_the_time() ),
              get_the_date()
          ),
          sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
              get_author_posts_url( get_the_author_meta( 'ID' ) ),
              sprintf( esc_attr__( 'View all posts by %s', 'denali' ), get_the_author() ),
              get_the_author()
          )
      );
  }
}


/**
 * Draws the header image for a given page
 *
 * Outside of class for ease-of-use within templates.
 *
 * Determines if this requested from front-page, property page, or other page and returns a header image or slideshow.
 *
 * @todo Improve function to use conditional better, i.e. is_archive(), is_single(), is_property(), etc.
 *
 * @since Denali 1.0
 */
if ( ! function_exists( 'denali_header_image' ) ) {
  function denali_header_image($args = '') {
    global $post, $wp_properties, $denali_theme_settings;

    $header_slideshow = $wp_properties['configuration']['feature_settings']['slideshow']['property']['image_size'];
    $wrapper_type = 'image_wrapper';
    $wrapper_class = array();
    $main_class = array();
    $search_form = "";

    //** get header image size */
    $header_image = ($denali_theme_settings['singular_header_image_size'] ?  $denali_theme_settings['singular_header_image_size'] : 'header_image' );

    if(is_callable('WPP_F::image_sizes')) {
      $singular_header_image = WPP_F::image_sizes($header_image);
    }

    if(!is_array($singular_header_image)) {
      $singular_header_image = false;
    }

    /** Determine if this is home page, or blog page */

    $is_active_slideshow_search = (
      ($denali_theme_settings['break_out_global_property_search_areas'] != 'true' && denali_theme::is_active_sidebar('global_property_search'))
      ||
      ($denali_theme_settings['break_out_global_property_search_areas'] == 'true' && denali_theme::is_active_sidebar('home_slideshow_overlay_property_search'))
    );

    if(is_front_page()) {
      $show_on_blog_or_home_page = true;
      if($denali_theme_settings['hide_slideshow_search'] != 'true' && $is_active_slideshow_search) {
        $show_search_form = true;
      }
    }
    elseif(is_posts_page()) {
      $show_on_blog_or_home_page = true;
      if($denali_theme_settings['hide_slideshow_search_from_posts_page'] != 'true' && $is_active_slideshow_search) {
        $show_search_form = true;
      }
    }

    //** If this is the front page but is not the overview page, we should use the default header */
    if($show_on_blog_or_home_page && current_theme_supports('home_page_attention_grabber_area')) {

      if( class_exists('class_wpp_slideshow') && $global_slideshow = global_slideshow(true)) {
        denali_theme::console_log('AG: Home or Blog home page with no attention grabber exists, slideshow found.');
        $main_class[] = 'slide';
        $main_class[] = 'wpp_global_slideshow';
        $wrapper_type = 'wpp_slideshow_global_wrapper';
        $image_html = $global_slideshow;
      }
      else {
        $get_header_image = get_header_image();
        denali_theme::console_log('AG: Not a singular page, but generic header image found, rendering.');
        $image_html = "<img class=\"denali_header_image\" src='{$get_header_image}' alt='{$post->post_title}' />";
        $main_class[] = 'get_header_image';
        $main_class[] = 'static_header_image';
      }

      if($show_search_form) {
        $main_class[] = 'overlay_search_form';
        $wrapper_class[] = 'overlay_search_form';

        $class .= 'includes_search_form';
        ob_start();
        /** Determine if home global property search widget areas should be separated */
        if($denali_theme_settings['break_out_global_property_search_areas'] == 'true') {
          dynamic_sidebar( 'home_slideshow_overlay_property_search' );
        } else {
          dynamic_sidebar( 'global_property_search' );
        }
        $search_form = "<div id='global_property_search_home'>" . ob_get_contents() . "</div>";
        ob_end_clean();
      }

    } else {

      //** This is already checked for in attention template part, but just in case */
      if(get_post_meta($post->ID, 'hide_header', true) == 'true') {
        return;
      }

      if ( class_exists('class_wpp_slideshow') ) {
        $slideshow_exists = class_wpp_slideshow::display_slideshow(array('type' => 'single', 'image_size' => $header_slideshow));
      }

      //** get URL to post thumbnail ID */
      $featured_image = wpp_get_image_link(get_post_thumbnail_id( $post->ID ), $header_image, array('return'=>'array'));

      if (($post->post_type == 'property' || is_singular()) && (has_post_thumbnail( $post->ID ) || $slideshow_exists))  {
        denali_theme::console_log('AG: Singular page with either a Featured Image or a slideshow. ');

        /** If a property page or a single page with a thumbnail, we show the Featured Image */
        if($slideshow_exists && $denali_theme_settings['never_show_property_slideshow'] != 'true' ) {
          denali_theme::console_log('AG: Slideshow exists, and is not disabled, rendering.');

          $wrapper_type = 'property_slideshow';
          $image_html = $slideshow_exists;
          $main_class[] = 'wpp_single_slideshow';

        } else {
          denali_theme::console_log('AG: AG: Slideshow does not exist, or is disabled. Checking if to display the Featured Image.');

          if (empty($featured_image)) {
            denali_theme::console_log('AG: Featured Image does not exist.');
          }

          //** Finding reasons not to display a static header image. */
          if($post->post_type == 'property') {
            denali_theme::console_log('AG: This is a single property page, checking options.');

            if(!$singular_header_image) {
              denali_theme::console_log('AG: Property Header Image size is not set, therefore No Static Header Image is selected, leaving.');
              return;
            }

            /** Determine if fetured image has needed size and don't show it if not (See 'Single Content Pages' Tab settings) */
            if($denali_theme_settings['hide_singular_header_if_image_too_small'] == 'true') {
              denali_theme::console_log('AG: Header image size set, and a Feature Image exists, checking minimum size. ');
              if($featured_image['width'] < $singular_header_image['width']) {
                denali_theme::console_log("Featured Image has width {$featured_image['width']} while requirement is {$singular_header_image['width']}. leaving.");
                return;
              }
              elseif ($featured_image['height'] > $singular_header_image['height']) {
                denali_theme::console_log("Featured Image has height  {$featured_image['height']} while requirement is {$singular_header_image['height']}. leaving.");
                return;
              }
            }
          }

          denali_theme::console_log('AG: Singlular page passed inspection - displaying header image.');

          $image_html = "<img class=\"denali_header_image primary_image\" src='{$featured_image[link]}' alt='{$post->post_title}' />";
          $main_class[] = 'single_primary_image';
          $main_class[] = 'static_header_image';

        }

      } elseif ($get_header_image = get_header_image()) {
        denali_theme::console_log('AG: Not a singular page, but generic header image found, rendering.');
        $image_html = "<img class=\"denali_header_image\" src='{$get_header_image}' alt='{$post->post_title}' />";
        $main_class[] = 'get_header_image';
        $main_class[] = 'static_header_image';
      }
    }

    if(is_array($main_class)) {
      $main_class = array_unique($main_class);
      $main_class = implode(' ', $main_class);
    }

    if(is_array($wrapper_class)) {
      $wrapper_class = array_unique($wrapper_class);
      $wrapper_class = implode(' ', $wrapper_class);
    }


    if(empty($main_class)) {
      $main_class = 'default_wrapper';
    }

    if($image_html) {
      ?>
      <div class="sld-flexible-wrapper <?php echo $wrapper_class ?>">
        <div class="sld-flexible <?php echo $main_class ?>">
          <div class="sld-top"></div>
          <div class="<?php echo $wrapper_type ?> <?php echo $class?>"><?php echo $image_html ?></div>
          <div class="sld-bottom"></div>
        </div>
        <?php echo $search_form ?>
      </div>
      <?php
    }
  }
}


/**
 * Handles comments
 *
 * Based on denali 1.1 comment handler
 *
 * @since Denali 1.0
 */
if ( ! function_exists( 'denali_comment' ) ) {
  function denali_comment( $comment, $args, $depth ) {
      $GLOBALS['comment'] = $comment;
      switch ( $comment->comment_type ) :
          case '' :
      ?>
      <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
          <div id="comment-<?php comment_ID(); ?>">
          <div class="comment-author vcard">
              <?php echo get_avatar( $comment, 40 ); ?>
              <?php printf( __( '%s <span class="says">says:</span>', 'denali' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
          </div><!-- .comment-author .vcard -->
          <?php if ( $comment->comment_approved == '0' ) : ?>
              <em><?php _e( 'Your comment is awaiting moderation.', 'denali' ); ?></em>
              <br />
          <?php endif; ?>

          <div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
              <?php
                  /* translators: 1: date, 2: time */
                  printf( __( '%1$s at %2$s', 'denali' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'denali' ), ' ' );
              ?>
          </div><!-- .comment-meta .commentmetadata -->

          <div class="comment-body"><?php comment_text(); ?></div>

          <div class="reply">
              <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
          </div><!-- .reply -->
      </div><!-- #comment-##  -->

      <?php
              break;
          case 'pingback'  :
          case 'trackback' :
      ?>
      <li class="post pingback">
          <p><?php _e( 'Pingback:', 'denali' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'denali'), ' ' ); ?></p>
      <?php
              break;
      endswitch;
  }
}


/**
 * Display Folow icons in the footer if required data is inputed
 */
if ( ! function_exists( 'denali_footer_follow' ) ) {
  function denali_footer_follow($denali_theme_settings = false, $args = false) {

    if(!$denali_theme_settings) {
      global $denali_theme_settings;
    }

    $defaults = array(
      'return_raw' => false,
      'return_array' => false,
      'echo' => false
    );

    $args = wp_parse_args( $args, $defaults );

    $template_dir = get_bloginfo('stylesheet_directory');

    $icons['twitter']['icon'] = '/img/follow_t.png';
    $icons['twitter']['url'] = $denali_theme_settings['social_twitter'];
    $icons['twitter']['option'] = 'social_twitter';
    $icons['twitter']['label'] = 'Twitter';

    $icons['facebook']['icon'] = '/img/follow_f.png';
    $icons['facebook']['url'] = $denali_theme_settings['social_facebook'];
    $icons['facebook']['option'] = 'social_facebook';
    $icons['facebook']['label'] = 'Facebook';

    $icons['linkedin']['icon'] = '/img/follow_in.png';
    $icons['linkedin']['url'] = $denali_theme_settings['social_linkedin'];
    $icons['linkedin']['option'] = 'social_linkedin';
    $icons['linkedin']['label'] = 'LinkedIn';

    $icons['rss']['icon'] = '/img/follow_rss.png';
    $icons['rss']['url'] = $denali_theme_settings['social_rss_link'];
    $icons['rss']['option'] = 'social_rss_link';
    $icons['rss']['label'] = 'RSS';

    $icons['youtube']['icon'] = '/img/follow_y.png';
    $icons['youtube']['url'] = $denali_theme_settings['social_youtube_link'];
    $icons['youtube']['option'] = 'social_youtube_link';
    $icons['youtube']['label'] = 'YouTube';

    $icons = apply_filters('denali_social_icons', $icons);

    foreach($icons as $network => $data) {

      $thumb_url = $template_dir . $data['icon'];

      $icons[$network]['thumb_url'] = $thumb_url;

      if(!empty($data['url'])) {
        $html[] = '<a href="'. $data['url'] . '"><img class="denali_social_link" src="'. $thumb_url .'" /></a>';
      }

    }

    if($args['return_raw']) {
      return $icons;
    }

    if(!is_array($icons)) {
      return false;
    }


    if(!is_array($html)) {
      return false;
    }

    $html = implode('', $html);

    if($args['echo']) {
      echo $html;
      return;
    }

    if($args['return_array']) {
      return $icons;
    }

    return $html;



  }
}


/**
 * Conditional tag to determine if current page is selected to be the primary posts page
 *
 * @since Denali 1.7
 */
if ( ! function_exists( 'is_posts_page' ) ) {
  function is_posts_page() {
    global $wp_query;

    if($wp_query->is_posts_page)
      return true;

    return false;
  }
}


/**
 * Conditional tag to determine if current page is selected to be the primary posts page
 *
 * @since Denali 1.7
 */
if ( ! function_exists( 'hide_page_title' ) ) {
  function hide_page_title() {
    global $post;
    if(get_post_meta($post->ID, 'hide_page_title', true) == 'true') {
      return true;
    }
    return false;
  }
}


/**
 * Displays the post information, typically called below title.
 *
 * @since Denali 3.0.0
 */
if ( ! function_exists( 'denali_loop_entry_utility' ) ) {
  function denali_loop_entry_utility() {
    ?>
    <div class="entry-utility"><?php _e('Posted by','denali'); ?> <?php the_author_posts_link()?>
      <?php if ( count( get_the_category() ) ) : ?>
        <span class="cat-links">
          <?php printf( __( '<span class="%1$s"> in</span> %2$s', 'denali' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' )); ?>
        </span>
        <span class="meta-sep">|</span>
      <?php endif; ?>
      <?php
        $tags_list = get_the_tag_list( '', ', ' );
        if ( $tags_list ):
      ?>
      <span class="tag-links">
        <?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'denali' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
      </span>
      <span class="meta-sep">|</span>
      <?php endif; ?>
      <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'denali' ), __( '1 Comment', 'denali' ), __( '% Comments', 'denali' ) ); ?></span>
      <?php edit_post_link( __( 'Edit', 'denali' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
    </div><!-- .entry-utility -->
    <?php
  }
}


/**
 * Displays property attributes in overview
 *
 * @since Denali 3.0.0
 */
if ( ! function_exists( 'denali_draw_overview_stats' ) ) {
  function denali_draw_overview_stats() {
    global $denali_theme_settings, $wp_properties, $property;

    if(empty($denali_theme_settings['property_overview_attributes']['stats_by_icon']) || empty($denali_theme_settings['property_overview_attributes']['stats_icons'])) {
      return false;
    }

    $icons = array_flip((array)$denali_theme_settings['property_overview_attributes']['stats_icons']);

    $stats = array();
    foreach( (array)$wp_properties['frontend_property_stats'] as $slug => $v ) {
      if( isset($icons[$slug]) && in_array($icons[$slug], (array)$denali_theme_settings['property_overview_attributes']['stats_by_icon'])) {
        $stats[] = $slug;
      }
    }

    foreach($stats as $attribute) {
      if($attribute == 'property_type') {
        $attribute = 'property_type_label';
      }
      if($property[$attribute]) {
        ?>
        <li class="property_<?php echo $attribute; ?>" title="<?php echo $wp_properties['property_stats'][$attribute]; ?>">
          <i class="denali-icon <?php echo $icons[$attribute]; ?>"></i>
          <span class="denali-icon-value"><?php echo $property[$attribute]; ?></span>
        </li>
        <?php
      }
    }
  }
}