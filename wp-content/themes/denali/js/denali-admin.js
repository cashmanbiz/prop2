jQuery(document).ready(function() {

  jQuery("#wpp_settings_tabs").tabs({
    cookie: {
      expires: 30,
      name: 'denali_settings_page_tabs'
    },
    show: function(event, ui) {
      setTimeout(function(){
        jQuery('.horizontal-list-settings h3.equal_heights', jQuery(ui.panel)).equalHeights();
      }, 50);
    },
    activate: function(event, ui) {
      setTimeout(function(){
        jQuery('.horizontal-list-settings h3.equal_heights', jQuery(ui.panel)).equalHeights();
      }, 50);
    }
  });

  //** Denali Backup Settings */
  jQuery('#show_download_denali_settings_wrapper').click(function(event){
    event.preventDefault();
    jQuery('#download_denali_settings_wrapper').slideDown();
    return false;
  });

  jQuery('#denali_backup_cancel').click(function(event){
    event.preventDefault();
    jQuery('#download_denali_settings_wrapper').slideUp();
    return false;
  });

  jQuery('#denali_backup').click(function(event){
    event.preventDefault();
    var href = jQuery('#denali_backup_nonce_url').val(),
        widgets = jQuery('#denali_backup_widgets_settings').is(':checked'),
        menus = jQuery('#denali_backup_menus_settings').is(':checked');

    if(widgets) href += '&widgets_settings=true';
    if(menus) href += '&menus_settings=true';
    jQuery('#download_denali_settings_wrapper').slideUp();
    document.location.href = href;
    return false;
  });
  //** END Denali Backup Settings */

  //** Cycle through all checked checkboxes and highlight their parent elements */
  jQuery('li.denali_setup_option_block input[type=checkbox]').each(function() {
    var parent_row = jQuery(this).parents('ul.block_options');
    var parent_holder = jQuery(this).parents('li.denali_setup_option_block');

    if(jQuery(this).is(":checked")) {
      jQuery(parent_holder).addClass('selected_option');
    }

  });

  //** When a 'denali_setup_option_block' element is clicked, the child checkbox is checked, and the element is highlighted
  jQuery('li.denali_setup_option_block').click(function() {
    var parent_row = jQuery(this).parents('ul.block_options');
    var this_option_checkbox = jQuery('input[type=checkbox]', this);
    jQuery('li.denali_setup_option_block', parent_row).removeClass('selected_option');

    jQuery('input[type=checkbox]', parent_row).removeAttr("checked")
    jQuery(this_option_checkbox).attr('checked', true);

    jQuery(this).addClass('selected_option');

  });

  jQuery(".denali_theme_settings_inquiry_crm_forms").change(function() {
    denali_adjust_inquiry_tab();
  });

  jQuery("input[group=options_explore]").change(function(){
    if(jQuery("input#custom_html").is(":checked"))
      jQuery(".denali_theme_settings_explore_custom_html").show();
    else
      jQuery(".denali_theme_settings_explore_custom_html").hide();
  });

  jQuery(".denali_help_wrap .denali_help_switch, .denali_help_wrap .denali_help_element").click(function() {
    var parent= jQuery(this).parents('.denali_help_wrap');
    jQuery('.denali_help_element', parent).toggle();
  });

  jQuery("#denali_theme_settings_header_crm_form").change(function() {
    var value = jQuery(this).val();
    var wrapper = jQuery(this).closest(".wpp_something_advanced_wrapper");
    if(value == "denali_default_header_form") {
      jQuery(".denali_header_regular_form_settings", wrapper).show();
      jQuery(".denali_header_crm_form_settings", wrapper).hide();
    } else {
      jQuery(".denali_header_regular_form_settings", wrapper).hide();
      jQuery(".denali_header_crm_form_settings", wrapper).show();
    }
  });

  jQuery(".denali_force_http_prefix").change(function() {
    var value = jQuery(this).val();
    var found = ((value.search("http")) < 0 ? false : true);
    if(value == "" || found) {
      return;
    }
    jQuery(this).val("http://" + value);
  });

  jQuery(".denali_conditional_setting").change(function() {
    denali_adjust_dom_to_conditionals();
  });

  // Show settings array
  jQuery("#denali_show_settings_array").click(function() {
    jQuery("#denali_show_settings_array_cancel").show();
    jQuery("#denali_show_settings_array_result").show();
  });

  // Hide settings array
  jQuery("#denali_show_settings_array_cancel").click(function() {
    jQuery("#denali_show_settings_array_result").hide();
    jQuery(this).hide();
  });

});


function denali_adjust_inquiry_tab() {

  var display_default_settings = false;

  jQuery(".denali_theme_settings_inquiry_crm_forms").each(function() {

    if(jQuery(this).val() == 'denali_default_form') {
      display_default_settings = true;
    }

  });

  if(!display_default_settings) {
    jQuery(".denali_default_inquiry_form_fields").hide();
  } else {
    jQuery(".denali_default_inquiry_form_fields").show();
  }

}


/* Toggle conditional settings */
function denali_adjust_dom_to_conditionals(type) {

  jQuery(".denali_conditional_setting").each(function() {

    var affected_options = jQuery(this).attr("affected_options");
    var result_element = jQuery(".denali_conditional_setting_result[required_enabled_setting=" + affected_options + "]");

    if(jQuery(this).is(":checked")) {

      if(type == "instant") {
        result_element.hide();
      } else {
        result_element.fadeOut();
      }

    } else {

      if(type == "instant") {
        result_element.show();
      } else {
        result_element.fadeIn();
      }
    }

  });

}

