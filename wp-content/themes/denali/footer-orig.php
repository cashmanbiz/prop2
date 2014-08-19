<?php
  global $ds;

  if($ds['disable']['footer_description'] != 'true') {
    $site_description = get_bloginfo('description');
  }

  if($ds['disable']['denali_footer_follow'] != 'true') {
    $denali_footer_follow = denali_footer_follow();
  }

  if($ds['disable_footer_bottom_left_block_widget_area'] != 'true' && is_active_sidebar( "latest_listings" )) {
    $show_latest_listings = true;
  }

  if(current_theme_supports('footer_explore_block') && $ds['footer_explore_block_hide'] != 'true') {
    $show_expore_block = true;
  }

  if($ds['footer_menu_hide'] != 'true') {
    if($footer_menu = wp_nav_menu(array(
      'theme_location'  => 'footer-menu',
      'menu_class'      => 'footer-nav',
      'before'          => '<span class="menu"><span class="left_top"></span><span class="right_top"></span>',
      'after'           => '</span>',
      'echo'           => false,
      'fallback_cb'     => 'denali_list_pages'
    ))) echo ($footer_menu ? "<div class='menu-footer-container'>{$footer_menu}</div>" : '');
  }

?>
  </div>
  <div class="bottom"></div>
</div>

<div class="footer">
  <div class="inner_footer theme_full_width">

  <?php if ( $show_latest_listings ) { ?>
    <div class="foot_last_list equal_heights">
    <?php dynamic_sidebar( 'latest_listings' ); ?>
    </div>
  <?php } ?>

  <?php if($show_expore_block) { ?>
    <div class="foot_explore equal_heights <?php echo (!$show_latest_listings ? 'big' : ''); ?>">
  <?php if($ds['footer_explore_title_hide'] != 'true') { ?>
      <h5 class="explore_block_title"><?php _e("Explore",'denali'); ?></h5>
  <?php } ?>

    <?php if ($ds['options_explore'] == 'custom_html') {  echo do_shortcode(nl2br($ds['explore']['custom_html_content'])); } ?>

    <?php if ($ds['options_explore'] == 'pages') {
       ?> <ul> <?php
       wp_list_pages('title_li=&number=8&offset=0&depth=1');
       ?></ul><ul> <?php
       wp_list_pages('title_li=&number=8&offset=8&depth=1');
       ?> </ul> <?php
       $pages = get_posts();
    } ?>

    <?php  if ($ds['options_explore'] == 'cats'){
        $cats = explode("<br />",wp_list_categories('title_li=&echo=0&depth=1&style=none'));
        $cat_n = count($cats) - 1;
        for ($i=0;$i< $cat_n;$i++){
            if ($i<$cat_n/2){
                $cat_left = $cat_left.'<li>'.$cats[$i].'</li>';
            }elseif ($i>=$cat_n/2){
                $cat_right = $cat_right.'<li>'.$cats[$i].'</li>';
            }
        }
        ?>
      <ul class="left"><?php echo $cat_left;?></ul>
      <ul class="right"><?php echo $cat_right;?></ul>
   <?php } ?>

    </div><?php /* .foot_explore */ ?>

    <?php }; ?>

      <div class="foot_far_right equal_heights">
        <ul class="footer_right_elements">
          <?php echo (!empty($ds['phone']) ? "<li class='footer_phone'><span>{$ds[phone]}</span></li>": "");?>
          <?php if(!empty($site_description)) { ?><li class="site_description"><?php echo $site_description; ?></li><?php } ?>
          <?php if(!empty($denali_footer_follow)) { ?><li class="bottom_right_icons denali_footer_follow"><?php echo $denali_footer_follow; ?></li><?php } ?>
          <?php if($ds['show_equal_housing_icon'] == 'true') { ?><li class="equal_housing_wrapper"><span class="equal_housing_icon">&nbsp;</span></li><?php } ?>
          <li class="bot_right"><span class="copy"> Copyright &copy; <?php echo denali_theme::denaly_copyright();?>, <?php bloginfo(' name'); ?></span></li>
          <?php echo (($ds['disable']['powered_by_link'] != 'true') ? "<li class='theme_powered_by'>".denali_theme::powered_by()."</li>" : '');?>
        </ul>
    </div>
  </div>
</div>
<?php

if($ds['bottom_of_page_menu_hide'] != 'true') {
  if($bottom_of_page_menu = wp_nav_menu(array(
    'theme_location'  => 'bottom_of_page_menu',
    'menu_class'      => 'bottom_of_page_menu',
    'echo'           => false,
    'fallback_cb'     => 'denali_list_pages'
  ))) echo ($bottom_of_page_menu ? '<div class="bottom_of_page_menu"><div class="theme_full_width">' . $bottom_of_page_menu . '</div></div>' : '');
}
?>
</div><?php //** .wrapper */ ?>

<?php wp_footer(); ?>
</body>
</html>