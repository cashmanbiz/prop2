<?php
/**
 * Header - Login Dropdown
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */


  //** Do not display this tab for logged in users */
  if(is_user_logged_in()) { return; }

  //** Check if this section is disabled in settings */
  if($ds['hide_header_login'] == 'true') { return; }

  $function = create_function('$c', '
    $c["login"]["id"] = "dropdown_header_login";
    $c["login"]["title"] = "'.__('Login','denali').'";
    $c["login"]["class"] = "option_tab dropdown_tab_login";
    $c["login"]["href"] = "#";
    return $c;
  ');
  add_filter('denali_header_links', $function, 50, 1);

?>

<div id="dropdown_header_login" class="header_dropdown_div header_login_div">
  <ul class="denali_dropdown_elements">
    <li class="continfo header_login_section header_dropdown_section">
      <?php echo (!empty($ds['name']) ? "<h5>{$ds['name']}</h5>"  : "") ?>
      <?php echo (!empty($ds['info']) ? "<p class='denali_header_info'>{$ds['info']}</p>"  : "") ?>
      <?php echo (!empty($ds['name']) ? "<p><span class='sena'>{$ds['address']}</span><br />"  : "") ?>
      <?php echo (!empty($ds['address']) ? "{$ds['address']} <br />"  : "") ?>
      <?php echo (!empty($ds['phone']) ? "Phone: {$ds['phone']} <br />"  : "") ?>
    </li>
    <li class="form header_login_section header_dropdown_section">
    <?php  $current_page = (is_singular() ? get_permalink($post->ID) : get_bloginfo('url')); wp_login_form(array('redirect' => $current_page, 'form_id' => 'header_login_form'));  ?>
    </li>
  </ul>
</div>
