<?php
/**
 * Header - Dropdown Links
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */ 
 
 
 //** Make sure header links to exist */
 if(!is_array($denali_header_links)) { return; }

?>

 <ul class="log_menu denali_header_dropdown_links">
  <?php foreach($denali_header_links as $link_data) { ?>
    <li class="<?php echo esc_attr($link_data['class']); ?> denali_tab_wrapper">
      <a href="<?php echo esc_attr($link_data['href']); ?>" <?php echo ($link_data['id'] ? 'section_id="' . $link_data['id'] . '"' : ''); ?> ><?php echo $link_data['title']; ?></a>
    </li>
  <?php } ?>
</ul>