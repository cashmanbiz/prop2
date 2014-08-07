<?php
/*
Plugin Name: WPQR QR-Code Generator
Plugin URI: http://qrtool.de/en/qr-code-wordpress-plugin/
Description: QR-Code widget and shortcode in one QR-Code gernerator plugin. Use the QR-Code widget in your sidebars or generate QR-Codes in pages and articles
Author: QRtool
Version: 0.2.6
Author URI: http://qrtool.de
License: CC+
Warranties: None.
*/

function wpqr_qr_code_generator_init() {
 load_plugin_textdomain( 'wpqr-qr-code', false, dirname( plugin_basename( __FILE__ ) ) . '/locales/' );
}
//add_action('plugins_loaded', 'wpqr_qr_code_generator_init');

function wpqr_qr_code_generator_admin_actions() {
	add_options_page ( "WPQR QR-Code Generator", "QR-Code", 1, "wpqr_qr-generator-settings", "wpqr_qr_code_generator_admin" );
	//add_posts_page( page_title, menu_title, capability, handle, [function]);
}
function wpqr_qr_code_generator_admin(){
	wpqr_qr_code_generator_init();
    print '<h3>'.__('WPQR QR-Code Generator', 'wpqr-qr-code').'</h3>';
    print '<p>'.__('Insert <b>[qr-code]</b> anywhere in WordPress to display a QR-Code that leads to the page/article being displayed', 'wpqr-qr-code').'</p>';
    print '<p>'.__('You can customize your QR-Code by using the shortcode with the folling attributes <b>[qr-code align="left" class="my-qr-code" color="#000000" background_color="#FFFFFF" size="4" margin="4" level="M" post_id="0"]</b> ', 'wpqr-qr-code').'</p>';
    print '<h4>'.__('Attributes available', 'wpqr-qr-code').':</h4>';
    print '<ul>';
    print '<li><b>align</b> : left|standard|center|right</li>';
    print '<li><b>class</b> : '.__('any valid CSS class name', 'wpqr-qr-code').'</li>';
    print '<li><b>color</b> : '.__('forground color of QR-Code in hex i.e. #000000 for black', 'wpqr-qr-code').'</li>';
    print '<li><b>background_color</b> : '.__('background color of QR-Code in hex i.e. #FFFFFF for white', 'wpqr-qr-code').'</li>';
    print '<li><b>post_id</b> : '.__('If specified and greater than 0, the QR-Code will lead to the url of the post id. Otherwise the QR-Code will lead to the page currently beeing displayed.', 'wpqr-qr-code').'</li>';
    print '<li><b>size</b> : '.__('The size of the QR-Code. It can range from 1=smallest to 10=largest.', 'wpqr-qr-code').'</li>';
    print '<li><b>margin</b> : '.__('The margin around the QR-Code. It can range from 0=smallest to 10=largest.', 'wpqr-qr-code').'</li>';
    print '<li><b>level</b> : '.__('Possible values L|M|Q|H which stand for the error correction level of the QR-Code. L=7%,M=15%,Q=25%,H=30%', 'wpqr-qr-code').'</li>';
    print '</ul>';
    
}

add_action ( 'admin_menu', 'wpqr_qr_code_generator_admin_actions' );



/*
[qr-code align="left" class="my-qr-code" color="#000000" background_color="#FFFFFF" size="4" margin="4" level="M" post_id="0"]
*/
function wpqr_qr_code_generator( $atts, $content = null ) {
   
	$url_parts = parse_url('http://'.$_SERVER['SERVER_NAME']);
	$myDomain = $url_parts['host'];
	
  extract( shortcode_atts( array(
      'post_id' => 0,
      'align' => 'standard',
      'class' => 'qr-code',
      'color' => '#000000',
      'background_color' => '#FFFFFF',
      'size' => 4,
      'margin' => 4,
      'level' => 'M'
      ), $atts ) );
   
  if($post_id==0)global $post;
  else {
   	$post = get_post( $post_id ); 
  }
  
  $qr_url = get_permalink($post->ID);
  
  if($class!='')$class_p = ' class="'.$class.'"';
  else $class_p = '';
  
  switch($align){
  	case 'left':
  		$align_p = ' align="left"';
  		break;
  	case 'right':
  		$align_p = ' align="right"';
  		break;
  	case 'center':
  		$align_p = ' align="center"';
  		break;
  	default:
  		$align_p = '';
  		break;
  }
  switch($level){
  	case 'M':
  		$level_p = 'M';
  		break;
  	case 'H':
  		$level_p = 'H';
  		break;
  	case 'L':
  		$level_p = 'L';
  		break;
  	case 'Q':
  		$level_p = 'Q';
  		break;
  	default:
  		$level_p = 'M';
  		break;
  }
  switch($margin){
  	case '0':
  		$margin_p = '0';
  		break;
  	case '1':
  		$margin_p = '1';
  		break;
  	case '2':
  		$margin_p = '2';
  		break;
  	case '3':
  		$margin_p = '3';
  		break;
  	case '5':
  		$margin_p = '5';
  		break;
  	case '6':
  		$margin_p = '6';
  		break;
  	case '7':
  		$margin_p = '7';
  		break;
  	case '8':
  		$margin_p = '8';
  		break;
  	case '9':
  		$margin_p = '9';
  		break;
  	case '10':
  		$margin_p = '10';
  		break;
  	default:
  		$margin_p = '2';
  		break;
  }
  switch($size){
  	case '1':
  		$size_p = '1';
  		break;
  	case '2':
  		$size_p = '1';
  		break;
  	case '3':
  		$size_p = '2';
  		break;
  	case '4':
  		$size_p = '2';
  		break;
  	case '5':
  		$size_p = '3';
  		break;
  	case '6':
  		$size_p = '3';
  		break;
  	case '7':
  		$size_p = '4';
  		break;
  	case '8':
  		$size_p = '4';
  		break;
  	case '9':
  		$size_p = '4';
  		break;
  	case '10':
  		$size_p = '4';
  		break;
  	default:
  		$size_p = '4';
  		break;
  }
  $color = str_replace('#','',$color);
  if(strlen($color)==6 || strlen($color)==3)$color_p=$color;
  else $color_p = '000000';
  
  $background_color = str_replace('#','',$background_color);
  if(strlen($background_color)==6 || strlen($background_color)==3)$background_color_p=$background_color;
  else $background_color_p = 'FFFFFF';
  
  $qr_code = '';
  $qr_code .= '<a href="http://qrtool.de/qr-code-generator/" style="cursor:default;border:0;text-decoration:none;">';
  //$qr_code .= '<img alt="qr code generator"'.$align_p.$class_p.' src="http://qrtool.de/getQr.php?fg=a,s,'.$color_p.'&fg='.$color_p.'&bg='.$background_color_p.'&size='.$size_p.'&level='.$level_p.'&margin='.$margin_p.'&data='.urlencode($qr_url).'&choe=UTF-8"/>';
  //$hash = md5(DOMAIN.FG.BG.SIZE.LEVEL.MARGIN.urlencode( - qr data -) );
  $hash = md5($myDomain.$color_p.$background_color_p.$size_p.$level_p.$margin_p.urlencode($qr_url) );
  $qr_code .= '<img alt="qr code generator"'.$align_p.$class_p.' src="http://encode.qrtool.de/encode?ct=qr&fg='.$color_p.'&bg='.$background_color_p.'&size='.$size_p.'&level='.$level_p.'&margin='.$margin_p.'&data='.urlencode($qr_url).'&apiid='.$myDomain.'&stype=wpqr&hash='.$hash.'"/>';
  $qr_code .= '</a>';
  //$qr_code .= '<!-- QR-Code powered by http://qrtool.de/ -->';
	
	return $qr_code;
}
add_shortcode( 'qr-code', 'wpqr_qr_code_generator' );














/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'wpqr_qr_code_load_widgets' );

/**
 * Register our widget.
 * 'wpqr_qr_code_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function wpqr_qr_code_load_widgets() {
	register_widget( 'wpqr_qr_code_Widget' );
}

/**
 * wpqr_qr_code Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class wpqr_qr_code_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function wpqr_qr_code_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'wpqr_qr_code', 'description' => __('Displys a QR-Code that leads mobile users to the page beeing viewed', 'wpqr-qr-code') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wpqr_qr_code-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'wpqr_qr_code-widget', __('QR-Code Widget', 'wpqr-qr-code'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		$url_parts = parse_url('http://'.$_SERVER['SERVER_NAME']);
		$myDomain = $url_parts['host'];
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$caption = $instance['caption'];
		$align = $instance['align'];
		$color = $instance['color'];
		$background_color = $instance['background_color'];
		$size = $instance['size'];
		$margin = $instance['margin'];
		$level = $instance['level'];
		$show_poweredby = isset( $instance['show_poweredby'] ) ? $instance['show_poweredby'] : false;
		$clear_after = isset( $instance['clear_after'] ) ? $instance['clear_after'] : false;



		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		
		$post = get_post( $post_id ); 
	  
	  
	  $qr_url = get_permalink($post->ID);
	  
	  if($class!='')$class_p = ' class="'.$class.'"';
	  else $class_p = '';
	  
	  switch($align){
	  	case 'left':
	  		$align_p = ' align="left"';
	  		$style_p = ' style="float:left;text-align:left;"';
	  		break;
	  	case 'right':
	  		$align_p = ' align="right"';
	  		$style_p = ' style="float:right;text-align:right;"';
	  		break;
	  	case 'center':
	  		$align_p = ' align="center"';
	  		$style_p = ' style="text-align:center;"';
	  		break;
	  	default:
	  		$align_p = '';
	  		$style_p = '';
	  		break;
	  }
	  switch($level){
	  	case 'M':
	  		$level_p = 'M';
	  		break;
	  	case 'H':
	  		$level_p = 'H';
	  		break;
	  	case 'L':
	  		$level_p = 'L';
	  		break;
	  	case 'Q':
	  		$level_p = 'Q';
	  		break;
	  	default:
	  		$level_p = 'M';
	  		break;
	  }
	  switch($margin){
	  	case '0':
	  		$margin_p = '0';
	  		break;
	  	case '1':
	  		$margin_p = '1';
	  		break;
	  	case '2':
	  		$margin_p = '2';
	  		break;
	  	case '3':
	  		$margin_p = '3';
	  		break;
	  	case '5':
	  		$margin_p = '5';
	  		break;
	  	case '6':
	  		$margin_p = '6';
	  		break;
	  	case '7':
	  		$margin_p = '7';
	  		break;
	  	case '8':
	  		$margin_p = '8';
	  		break;
	  	case '9':
	  		$margin_p = '9';
	  		break;
	  	case '10':
	  		$margin_p = '10';
	  		break;
	  	default:
	  		$margin_p = '2';
	  		break;
	  }
	  switch($size){
	  	case '1':
	  		$size_p = '1';
	  		break;
	  	case '2':
	  		$size_p = '1';
	  		break;
	  	case '3':
	  		$size_p = '2';
	  		break;
	  	case '4':
	  		$size_p = '2';
	  		break;
	  	case '5':
	  		$size_p = '3';
	  		break;
	  	case '6':
	  		$size_p = '3';
	  		break;
	  	case '7':
	  		$size_p = '4';
	  		break;
	  	case '8':
	  		$size_p = '4';
	  		break;
	  	case '9':
	  		$size_p = '4';
	  		break;
	  	case '10':
	  		$size_p = '4';
	  		break;
	  	default:
	  		$size_p = '4';
	  		break;
	  }
	  $color = str_replace('#','',$color);
	  if(strlen($color)==6 || strlen($color)==3)$color_p=$color;
	  else $color_p = '000000';
	  
	  $background_color = str_replace('#','',$background_color);
	  if(strlen($background_color)==6 || strlen($background_color)==3)$background_color_p=$background_color;
	  else $background_color_p = 'FFFFFF';
		
		
		if ( $caption || $show_poweredby)
			echo  '<div'.$class_p.$style_p.'>';
		$qr_code = '';
		$qr_code .= '<a href="http://qrtool.de/qr-code-generator/" style="cursor:default;border:0;text-decoration:none;">';
		//$qr_code .= '<img alt="qr code generator"'.$align_p.$class_p.' src="http://qrtool.de/getQr.php?fg=a,s,'.$color_p.'&fg='.$color_p.'&bg='.$background_color_p.'&size='.$size_p.'&level='.$level_p.'&margin='.$margin_p.'&data='.urlencode($qr_url).'&choe=UTF-8"/>';
  	$hash = md5($myDomain.$color_p.$background_color_p.$size_p.$level_p.$margin_p.urlencode($qr_url) );
  	$qr_code .= '<img alt="qr code generator"'.$align_p.$class_p.' src="http://encode.qrtool.de/encode?ct=qr&fg='.$color_p.'&bg='.$background_color_p.'&size='.$size_p.'&level='.$level_p.'&margin='.$margin_p.'&data='.urlencode($qr_url).'&apiid='.$myDomain.'&stype=wpqr&hash='.$hash.'"/>';
  	$qr_code .= '</a>';
  	//$qr_code .= '<!-- QR-Code powered by http://qrtool.de/ -->';
		
		echo $qr_code;
		
		/* Display caption from widget settings if one was input. */
		if ( $caption )
			echo  '<div style="clear:both;"></div><caption align="bottom">' . $caption . '</caption>';

		/* If show poweredby was selected, display the user's sex. */
		if ( $show_poweredby )
			printf( '<div style="clear:both;"></div><caption align="bottom">' . __('Powered by %1$s.', 'wpqr_qr_code.', 'wpqr-qr-code') . '</caption>', '<a href="http://qrtool.de/" title="Management for mobile marketing | QR-Codes &amp; WebApps">QRtool</a>' );
		
		if ( $caption || $show_poweredby)
			echo  '</div>';
		
		if($clear_after)
			echo '<div style="clear:both;"></div>';
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['caption'] = strip_tags( $new_instance['caption'] );

		/* No need to strip tags for the rest. */
		$instance['align'] = $new_instance['align'];
		$instance['color'] = $new_instance['color'];
		$instance['background_color'] = $new_instance['background_color'];
		$instance['size'] = $new_instance['size'];
		$instance['margin'] = $new_instance['margin'];
		$instance['level'] = $new_instance['level'];
		$instance['show_poweredby'] = $new_instance['show_poweredby'];
		$instance['clear_after'] = $new_instance['clear_after'];
		

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
		
		wpqr_qr_code_generator_init();
		
		/* Set up some default widget settings. */
		$defaults = array(
											'title' => __('Title', 'wpqr-qr-code'),
											'caption' => __('Scan QR-Code', 'wpqr-qr-code'),
											'size' => 4,
											'margin' => 4,
											'align' => 'standard',
											'level' => 'M',
											'color' => '000000',
											'background_color' => 'FFFFFF',
											'show_poweredby' => true,
											'clear_after' => true
										);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'wpqr-qr-code'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Caption: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'caption' ); ?>"><?php _e('Caption:', 'wpqr-qr-code'); ?></label>
			<input id="<?php echo $this->get_field_id( 'caption' ); ?>" name="<?php echo $this->get_field_name( 'caption' ); ?>" value="<?php echo $instance['caption']; ?>" style="width:100%;" />
		</p>

		<!-- Color: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'color' ); ?>"><?php _e('Color:', 'wpqr-qr-code'); ?></label>
			<input id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="<?php echo $instance['color']; ?>" style="width:100%;" />
		</p>

		<!-- BackgroundColor: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'background_color' ); ?>"><?php _e('Background color:', 'wpqr-qr-code'); ?></label>
			<input id="<?php echo $this->get_field_id( 'background_color' ); ?>" name="<?php echo $this->get_field_name( 'background_color' ); ?>" value="<?php echo $instance['background_color']; ?>" style="width:100%;" />
		</p>

		<!-- Align: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'align' ); ?>"><?php _e('Align:', 'wpqr-qr-code'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'align' ); ?>" name="<?php echo $this->get_field_name( 'align' ); ?>" class="widefat" style="width:100%;">
				<option value="standard" <?php if ( 'standard' == $instance['align'] ) echo 'selected="selected"'; ?>><?php _e('Standard', 'wpqr-qr-code'); ?></option>
				<option value="left" <?php if ( 'left' == $instance['align'] ) echo 'selected="selected"'; ?>><?php _e('Left', 'wpqr-qr-code'); ?></option>
				<option value="center" <?php if ( 'center' == $instance['align'] ) echo 'selected="selected"'; ?>><?php _e('Center', 'wpqr-qr-code'); ?></option>
				<option value="right" <?php if ( 'right' == $instance['align'] ) echo 'selected="selected"'; ?>><?php _e('Right', 'wpqr-qr-code'); ?></option>
			</select>
		</p>

		<!-- Level: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'level' ); ?>"><?php _e('Level:', 'wpqr-qr-code'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'level' ); ?>" name="<?php echo $this->get_field_name( 'level' ); ?>" class="widefat" style="width:100%;">
				<option value="L" <?php if ( 'L' == $instance['level'] ) echo 'selected="selected"'; ?>>L</option>
				<option value="M" <?php if ( 'M' == $instance['level'] ) echo 'selected="selected"'; ?>>M</option>
				<option value="Q" <?php if ( 'Q' == $instance['level'] ) echo 'selected="selected"'; ?>>Q</option>
				<option value="H" <?php if ( 'H' == $instance['level'] ) echo 'selected="selected"'; ?>>H</option>
			</select>
		</p>
		
		<!-- Size: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e('Size:', 'wpqr-qr-code'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" class="widefat" style="width:100%;">
				<option value="1" <?php if ( '1' == $instance['size'] ) echo 'selected="selected"'; ?>>1</option>
				<option value="2" <?php if ( '2' == $instance['size'] ) echo 'selected="selected"'; ?>>2</option>
				<option value="3" <?php if ( '3' == $instance['size'] ) echo 'selected="selected"'; ?>>3</option>
				<option value="4" <?php if ( '4' == $instance['size'] ) echo 'selected="selected"'; ?>>4</option>
		<!--		<option value="5" <?php if ( '5' == $instance['size'] ) echo 'selected="selected"'; ?>>5</option>
				<option value="6" <?php if ( '6' == $instance['size'] ) echo 'selected="selected"'; ?>>6</option>
				<option value="7" <?php if ( '7' == $instance['size'] ) echo 'selected="selected"'; ?>>7</option>
				<option value="8" <?php if ( '8' == $instance['size'] ) echo 'selected="selected"'; ?>>8</option>
		-->	</select>
		</p>
		
		<!-- Margin: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'margin' ); ?>"><?php _e('Margin:', 'wpqr-qr-code'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'margin' ); ?>" name="<?php echo $this->get_field_name( 'margin' ); ?>" class="widefat" style="width:100%;">
				<option value="0" <?php if ( '0' == $instance['margin'] ) echo 'selected="selected"'; ?>>0</option>
				<option value="1" <?php if ( '1' == $instance['margin'] ) echo 'selected="selected"'; ?>>1</option>
				<option value="2" <?php if ( '2' == $instance['margin'] ) echo 'selected="selected"'; ?>>2</option>
				<option value="3" <?php if ( '3' == $instance['margin'] ) echo 'selected="selected"'; ?>>3</option>
				<option value="4" <?php if ( '4' == $instance['margin'] ) echo 'selected="selected"'; ?>>4</option>
				<option value="5" <?php if ( '5' == $instance['margin'] ) echo 'selected="selected"'; ?>>5</option>
				<option value="6" <?php if ( '6' == $instance['margin'] ) echo 'selected="selected"'; ?>>6</option>
				<option value="7" <?php if ( '7' == $instance['margin'] ) echo 'selected="selected"'; ?>>7</option>
				<option value="8" <?php if ( '8' == $instance['margin'] ) echo 'selected="selected"'; ?>>8</option>
			</select>
		</p>

		<!-- Show poweredby? Checkbox -->
		<p>
			<input value="1" class="checkbox" type="checkbox" <?php checked( $instance['show_poweredby'], true ); ?> id="<?php echo $this->get_field_id( 'show_poweredby' ); ?>" name="<?php echo $this->get_field_name( 'show_poweredby' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_poweredby' ); ?>"><?php _e('Display powered by?', 'wpqr-qr-code'); ?></label>
		</p>
		
		<!-- clear_after? Checkbox -->
		<p>
			<input value="1" class="checkbox" type="checkbox" <?php checked( $instance['clear_after'], true ); ?> id="<?php echo $this->get_field_id( 'clear_after' ); ?>" name="<?php echo $this->get_field_name( 'clear_after' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'clear_after' ); ?>"><?php _e('Clear after QR-Code?', 'wpqr-qr-code'); ?></label>
		</p>

	<?php
	}
}
