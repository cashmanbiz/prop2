<?php
/**
 * Header - Contact Us
 *
 * Displays the bottom of page element on the home page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */

  global $ds;

  if(!current_theme_supports('header-property-contact')) { return; }

  if($ds['hide_header_contact'] == 'true') { return; }

  $label = (!empty($ds['contact_us_label']) ? $ds['contact_us_label'] : __( "Contact us", 'denali' ) );

  $function = create_function('$c', '
    $c["contact_us"]["id"] = "dropdown_header_contact_us";
    $c["contact_us"]["title"] = "'. addslashes( $label ) .'";
    $c["contact_us"]["class"] = "dropdown_tab_contact_us";
    $c["contact_us"]["href"] = "#";
    return $c;
  ');
  add_filter('denali_header_links', $function, 30, 1);

  if(empty($ds['wp_crm']['header_contact'])) {
    $contact_form = 'header_contact';
  } else {
    $contact_form = $ds['wp_crm']['header_contact'];
  }

  if($contact_form != 'denali_default_header_form' && class_exists('class_contact_messages')) {
    $crm_form = class_contact_messages::shortcode_wp_crm_form(array('form' => $contact_form));
  }
?>

<div id="dropdown_header_contact_us" class="header_dropdown_div header_contact_div">
  <ul class="denali_dropdown_elements">
    <li class="continfo header_contact_section header_dropdown_section">
        <?php echo (!empty($ds['name']) ? "<h5>" . $ds['name'] . "</h5>" : ""); ?>
        <?php echo (!empty($ds['info']) ? "<p class='denali_header_info'>" . nl2br(do_shortcode($ds['info'])) . "</p>" : ""); ?>

        <div class="cboth"></div>

        <?php echo (!empty($ds['latitude']) ? '<div id="denali_header_location_map" class="denali_header_location_map"></div>' : ''); ?>

        <p class="contact_info">
            <?php echo (!empty($ds['name']) ? "<span class='sena'>" . $ds['name'] . "</span><br />" : ""); ?>
            <?php echo (!empty($ds['address']) ? nl2br($ds['address']) .'<br />' : ""); ?>
            <?php echo (!empty($ds['phone']) ? $ds['phone'] .'<br />' : ""); ?>
            <?php echo (!empty($ds['fax']) ? __('Fax','denali') . ': '. $ds['fax'] .'<br />' : ""); ?>
         </p>
        <div class="cboth"></div>
    </li>

  <li class="form header_contact_section header_dropdown_section">

    <?php if($crm_form) {
      echo $crm_form;
    } else { ?>

    <form action="#" id="denali_contact_form" method="post">
    <div class="ajax_error hidden"></div>
    <div class = "contact">
      <div id = "contact_left">
        <label for="contact_name"><?php _e("Name",'denali'); ?>: <span>*</span></label>
        <input   id="contact_name"  type="text" />
      </div>
      <div id = "contact_right">
        <label for="contact_email"><?php _e("E-mail",'denali'); ?>: <span>*</span></label>
        <input  id="contact_email" type="text" />
      </div>
      <div class="clear"></div>
      <div id="contact_foot">
        <label for="contact_message"><?php _e("Message",'denali'); ?>: <span>*</span></label>
        <textarea id="contact_message" class="requiredField"></textarea>
      </div>
      <div class="clear"></div>
      <input type="submit" name="submitContact" id="submitContact" value="<?php _e("Send Message",'denali'); ?>" />
      <div class="ajax_loader"></div>
      <div class="clear"></div>
    </div>
    </form>

    <?php } ?>

  </li>
 </ul>
</div>