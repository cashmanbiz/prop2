<!DOCTYPE html><?php global $ds; ?>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
<title><?php


  wp_title( '|', true, 'right' );

  // Add the blog name.
  bloginfo( 'name' );

  // Add the blog description for the home/front page.
  $site_description = get_bloginfo( 'description', 'display' );
  if ( $site_description && ( is_home() || is_front_page() ) ) {
    echo " | $site_description";
  }

  // Add a page number if necessary:
  if ( $paged >= 2 || $page >= 2 ) {
    echo ' | ' . sprintf( __( 'Page %s', 'denali' ), max( $paged, $page ) );
  }

?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php if(file_exists( STYLESHEETPATH . '/favicon.ico') ) { ?>
<link rel="shortcut icon" href="<?php bloginfo( 'stylesheet_directory' ); ?>/favicon.ico" type="image/x-icon" />
<?php } ?>
<?php wp_head(); ?>
<script type="text/javascript">
  var denali_config = {};
  denali_config.nonce = "<?php echo wp_create_nonce('denali_contact_form'); ?>";
  denali_config.ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
  denali_config.message_submission = "<?php _e('Thank you for your message.','denali'); ?>";
  denali_config.location_name = "<?php echo ( !empty( $ds['name'] ) ? addslashes( $ds['name'] ) : __( 'Our location.','denali' ) ); ?>";
<?php if($ds['longitude'] && $ds['latitude']) { ?>
  denali_config.location_coords = {'latitude': <?php echo $ds['latitude']; ?>,'longitude': <?php echo $ds['longitude']; ?>}
<?php } ?>
<?php if($wp_properties['configuration']['developer_mode'] == 'true') { ?>
  denali_config.developer = true;
<?php } ?>
</script>
<!--[if IE 7]>
  <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url'); ?>/ie7.css">
<![endif]-->
</head>
<body <?php body_class(); ?>>

  <div class="disbl denali_header_expandable_sections">

    <?php get_template_part('header','contact-us'); ?>

    <?php get_template_part('header','login'); ?>

    <?php get_template_part('header','property-search'); ?>

  </div>

  <div class="wrapper">
  <div class="body_upper_background"></div>
    <div class="mid theme_full_width">

      <?php get_template_part('header','dropdown-links'); ?>

      <?php if(current_theme_supports('header-card') && $ds['hide_header_card'] != 'true' ): ?>
        <div class="header_business_card">
        <?php echo (!empty($ds['phone']) ? "<span class='phone'>" . apply_filters("denali_call_us_text", ($ds['phone_number_prefix'] ? $ds['phone_number_prefix'] : __('call us','denali'))) . " <span class='number'>{$ds['phone']}</span></span>": "");?>
        <?php echo ($ds['hide_address_from_card'] != 'true' && !empty($ds['address']) ? " <span class='address'>{$ds['address']}</span>": "");?>
        </div>
      <?php endif; ?>

      <?php if ( class_exists( 'qTranslateWidget' ) && $ds['show_qtranslate_widget'] ): ?>
        <?php the_widget('qTranslateWidget', array('hide-title'=>true, 'type'=>'image')); ?>
      <?php endif; ?>

      <?php  if($ds['hide_logo'] != 'true' ): ?>
      <?php if (!empty($ds['logo'])){ ?>
        <span class="custom_logo"><a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><img src="<?php echo $ds['logo']?>" alt="<?php bloginfo('name'); ?>" /></a></span>
      <?php } else { ?>
        <span class="logo"><a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><span class="denali_text_logo"><?php echo $ds['logo_text']; ?></span></a></span>
      <?php } ?>
      <?php endif; ?>

    </div>

  <div id="body_container" class="container">
    <div class="midd">

    <?php wp_nav_menu(apply_filters('denali_header_menu', array(
      'theme_location'=> 'header-menu',
      'menu_class'    => 'main-nav',
      'container_class' => 'menu-header-menu-container denali-header-menu',
      'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul><div class="clear"></div>',
      'link_before'   => '<span class="menu"><span class="link_text">',
      'link_after'    => '</span></span>',
      'depth'         => 2  )));
    ?>

    <?php wp_nav_menu(apply_filters('denali_sub_header_menu', array(
      'theme_location'  => 'header-sub-menu',
      'container_class' => 'menu-header-submenu-container denali-sub-menu',
      'menu_class'      => 'header-sub-menu',
      'link_before'     => '<span class="menu"><span class="link_text">',
      'link_after'      => '</span></span>',
      'fallback_cb'     => false,
      'depth'           => 2  )));
    ?>