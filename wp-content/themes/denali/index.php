<?php
/**
 * Template for home page which may be static or include latest posts.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Denali
*/

  //** Bail out if page is being loaded directly and denali_theme does not exist */
  if(!class_exists('denali_theme')) {
    die();
  }

   if(denali_theme::is_active_sidebar('home_sidebar')) {
    $home_sidebar = true;
   }
   
   if($home_sidebar) {
    $have_sidebar = true;
   }

?>

<?php get_header() ?>

<?php get_template_part('attention','home'); ?>

<div id="content" class="inner_content_wrapper <?php echo ($have_sidebar  ? ' have-sidebar' : 'wide-home no_columns'); ?>">

  <div class="home main">

    <?php get_template_part('loop', 'home'); ?>

    <?php get_template_part('content','home-bottom'); ?>

    <div class="cboth"></div>

  </div>

  <?php if($have_sidebar): ?>
    <div class="sidebar"><?php dynamic_sidebar( 'home_sidebar' ); ?></div>
  <?php endif; ?>

  <div class="cboth"></div>

</div>

<?php get_footer() ?>