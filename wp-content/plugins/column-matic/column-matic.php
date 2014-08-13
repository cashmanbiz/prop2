<?php
/*
Plugin Name: Column-Matic
Text Domain: columnmatic
Domain Path: /language
Plugin URI: http://plugins.twinpictures.de/plugins/column-matic/
Description: Easily wrap any content into cozy columns with this handy little shortcode.
Version: 1.3.1
Author: twinpictures
Author URI: http://www.twinpictures/
License: GPL2
*/

/*  Copyright 2014 Twinpictures (www.twinpictures.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Class WP_Column_Matic
 * @package WP_Column_Matic
 * @category WordPress Plugins
 */

class WP_Column_Matic {
   /**
    * Current version
    * @var string
   */
   var $version = '1.3.1';

   /**
    * Used as prefix for options entry
    * @var string
   */
   var $domain = 'columnmatic';
	
   /**
    * Name of the options
    * @var string
   */
   var $options_name = 'WP_Column_Matic_options';

   /**
    * @var array
   */
   var $options = array(
		'custom_css' => ''
   );
   
   /**
   * PHP5 constructor
   */
   function __construct() {
       // set option values
       $this->_set_options();
       
       // load text domain for translations
       load_plugin_textdomain( 'columnmatic', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
	     
       // add actions
       add_action( 'admin_menu', array( $this, 'admin_menu' ) );
       add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_actions' ) );
       add_action( 'admin_init', array( $this, 'admin_init' ) );
       add_action( 'wp_head', array( $this, 'expand_css_injector' ) );
   
       add_shortcode('column', array($this, 'columnmatic_shortcode'));
       add_shortcode('end_columns', array($this, 'end_columnmatic_shortcode'));
   }
   
   //Custom CSS inject-o-matic
   function expand_css_injector(){
	  if( !empty( $this->options['custom_css'] ) ){
		 echo "\n<style>\n";
		 echo $this->options['custom_css'];
		 echo "\n</style>\n";
	  }
   }
    
   /**
	 * Callback admin_menu
   */
   function admin_menu() {
	  if ( function_exists( 'add_options_page' ) AND current_user_can( 'manage_options' ) ) {
		 // add options page
		 $page = add_options_page('Column-Matic Options', 'Column-Matic', 'manage_options', 'column-matic-options', array( $this, 'options_page' ));
	  }
   }

   /**
   * Callback admin_init
   */
   function admin_init() {
	  // register settings
	  register_setting( $this->domain, $this->options_name );
   }
   
   function columnmatic_shortcode( $atts, $content = null ) {
      extract( shortcode_atts( array(
         'class' => '',
         'width' => '',
         'height' => '',
         'padding' => '',
         'padding_top' => '',
         'padding_right' => '',
         'padding_bottom' => '',
         'padding_left' => '',
         'margin' => '',
         'margin_top' => '',
         'margin_right' => '',
         'margin_bottom' => '',
         'margin_left' => '',
         'border' => '',
         'border_top' => '',
         'border_right' => '',
         'border_bottom' => '',
         'border_left' => '',
         'background' => '',
         'color' => '',
         'float' => 'left',
         'display' => 'inline',
         'text_align' => '',
         // ...etc
         ), $atts ) );
   
      $str =  '<div ';
      
      if($class){
         $str .=  'class="'.$class.'" ';
      }
      
      $str .= 'style="';
      if($width){
         $str .= 'width: '.$width.'; ';
      }
      if($height){
         $str .= 'height: '.$height.'; ';
      }
      if($padding){
         $str .= 'padding: '.$padding.'; ';
      }
      if($padding_top){
         $str .= 'padding-top: '.$padding_top.'; ';
      }
      if($padding_right){
         $str .= 'padding-right: '.$padding_right.'; ';
      }
      if($padding_bottom){
         $str .= 'padding-bottom: '.$padding_bottom.'; ';
      }
      if($padding_left){
         $str .= 'padding-left: '.$padding_left.'; ';
      }
      if($margin){
         $str .= 'margin: '.$margin.'; ';
      }
      if($margin_top){
         $str .= 'margin-top: '.$margin_top.'; ';
      }
      if($margin_right){
         $str .= 'margin-right: '.$margin_right.'; ';
      }
      if($margin_bottom){
         $str .= 'margin-bottom: '.$margin_bottom.'; ';
      }
      if($margin_left){
         $str .= 'margin-left: '.$margin_left.'; ';
      }
      if($border){
         $str .= 'border: '.$border.'; ';
      }
      if($border_top){
         $str .= 'border-top: '.$border_top.'; ';
      }
      if($border_right){
         $str .= 'border-right: '.$border_right.'; ';
      }
      if($border_bottom){
         $str .= 'border-bottom: '.$border_bottom.'; ';
      }
      if($border_left){
         $str .= 'border-left: '.$border_left.'; ';
      }
      if($background){
         $str .= 'background: '.$background.'; ';
      }
      if($color){
         $str .= 'color: '.$color.'; ';
      }
      if($float){
         $str .= 'float: '.$float.'; ';
      }
      if($display){
         $str .= 'display: '.$display.'; ';
      }
	  if($text_align){
         $str .= 'text-align: '.$text_align.'; ';
      }
      $str .= '">' . do_shortcode($content) . '</div>';
      return $str;
   }

   function end_columnmatic_shortcode(){
      return "<div style='clear: both;'></div>";
   }
   
   // Add link to options page from plugin list
   function plugin_actions($links) {
       $new_links = array();
       $new_links[] = '<a href="options-general.php?page=column-matic-options">' . __('Settings', 'columnmatic') . '</a>';
       return array_merge($new_links, $links);
   }
    
   /**
   * Admin options page
   */
   function options_page() {
      $like_it_arr = array(
		     __('really tied the room together', 'columnmatic'),
		     __('made you feel all warm and fuzzy on the inside', 'columnmatic'),
		     __('restored your faith in humanity... even if only for a fleeting second', 'columnmatic'),
		     __('rocked your world', 'provided a positive vision of future living', 'columnmatic'),
		     __('inspired you to commit a random act of kindness', 'columnmatic'),
		     __('encouraged more regular flossing of the teeth', 'columnmatic'),
		     __('helped organize your life in the small ways that matter', 'columnmatic'),
		     __('saved your minutes--if not tens of minutes--writing your own solution', 'columnmatic'),
		     __('brightened your day... or darkened if if you are trying to sleep in', 'columnmatic'),
		     __('caused you to dance a little jig of joy and joyousness', 'columnmatic'),
		     __('inspired you to tweet a little @twinpictues social love', 'columnmatic'),
		     __('tasted great, while also being less filling', 'columnmatic'),
		     __('caused you to shout: "everybody spread love, give me some mo!"', 'columnmatic'),
		     __('helped you keep the funk alive', 'columnmatic'),
		     __('<a href="http://www.youtube.com/watch?v=dvQ28F5fOdU" target="_blank">soften hands while you do dishes</a>', 'columnmatic'),
		     __('helped that little old lady <a href="http://www.youtube.com/watch?v=Ug75diEyiA0" target="_blank">find the beef</a>', 'columnmatic')
	    );
       $rand_key = array_rand($like_it_arr);
       $like_it = $like_it_arr[$rand_key];
   ?>
      <div class="wrap">
          <div class="icon32" id="icon-options-custom" style="background:url( <?php echo plugins_url( 'images/column-matic-icon.png', __FILE__ ) ?> ) no-repeat 50% 50%"><br></div>
          <h2>Column-Matic</h2>
      </div>
      
      <div class="postbox-container metabox-holder meta-box-sortables" style="width: 69%">
          <div style="margin:0 5px;">
              <div class="postbox">
                  <div class="handlediv" title="<?php _e( 'Click to toggle' ) ?>"><br/></div>
                  <h3 class="hndle"><?php _e( 'Default Column-Matic Settings', 'columnmatic' ) ?></h3>
                  <div class="inside">
                      <form method="post" action="options.php">
                          <?php
                              settings_fields( $this->domain );
                              $this->_set_options();
                              $options = $this->options;
                          ?>
                          <fieldset class="options">
                              <table class="form-table">
                                 <tr>
                                     <th><?php _e( 'Custom Style', 'columnmatic' ) ?>:</th>
                                     <td><label><textarea id="<?php echo $this->options_name ?>[custom_css]" name="<?php echo $this->options_name ?>[custom_css]" style="width: 100%; height: 150px;"><?php echo $options['custom_css']; ?></textarea>
                                         <br /><span class="description"><?php _e( 'Custom CSS style for <em>ultimate flexibility</em>', 'columnmatic' ) ?></span></label>
                                     </td>
                                 </tr>
				 
                              </table>
                          </fieldset>
                          
                          <p class="submit">
                              <input class="button-primary" type="submit" value="<?php _e( 'Save Changes', 'columnmatic' ) ?>" />
                          </p>
                      </form>
                  </div>
              </div>
          </div>
      </div>
      
      <div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
          <div style="margin:0 5px;">
              <div class="postbox">
                  <div class="handlediv" title="<?php _e( 'Click to toggle', 'columnmatic' ) ?>"><br/></div>
                  <h3 class="hndle"><?php _e( 'About', 'columnmatic' ) ?></h3>
                  <div class="inside">
                      <h4><img src="<?php echo plugins_url( 'images/column-matic-icon.png', __FILE__ ) ?>" width="16" height="16"/> Column-Matic Version <?php echo $this->version; ?></h4>
                      <p><?php _e( 'Easily wrap any content into cozy columns with this handy little shortcode.', 'columnmatic') ?></p>
                      <ul>
                          <li><?php printf( __( '%sDetailed documentation%s, complete with working demonstrations of all shortcode attributes, is available for your instructional enjoyment.', 'columnmatic'), '<a href="http://plugins.twinpictures.de/plugins/column-matic/documentation/" target="_blank">', '</a>'); ?></li>
                          <li><?php printf( __( 'Free Open Source %sSupport%s', 'columnmatic'), '<a href="http://wordpress.org/support/plugin/column-matic" target="_blank">', '</a>'); ?></li>
                          <li><?php printf( __('If this plugin %s, please consider %sreviewing it at WordPress.org%s to help others.', 'columnmatic'), $like_it, '<a href="http://wordpress.org/support/view/plugin-reviews/column-matic" target="_blank">', '</a>' ) ?></li>
                          <li><a href="http://wordpress.org/extend/plugins/column-matic/" target="_blank">WordPress.org</a> | <a href="http://plugins.twinpictures.de/plugins/column-matic/" target="_blank">Twinpictues Plugin Oven</a></li>
                      </ul>
                  </div>
              </div>
          </div>
          <div class="clear"></div>
      </div>
   <?php
   }
   
   /**
    * Set options from save values or defaults
    */
   function _set_options() {
      // set options
      $saved_options = get_option( $this->options_name );

      // backwards compatible (old values)
      if ( empty( $saved_options ) ) {
          $saved_options = get_option( $this->domain . 'options' );
      }
      
      // set all options
      if ( ! empty( $saved_options ) ) {
          foreach ( $this->options AS $key => $option ) {
              $this->options[ $key ] = ( empty( $saved_options[ $key ] ) ) ? '' : $saved_options[ $key ];
          }
      }
   }

} // end class WP_Column_Matic

/**
 * Create instance
 */
$WP_Column_Matic = new WP_Column_Matic;
?>