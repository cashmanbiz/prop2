/**
 * Determine if console is active and
 * Prints data to console
 *
 * @param mixed data
 * @author Maxim Peshkov
 */
function denali_log(data) {
  if(typeof console == 'object') {
    console.log(data);
  }
}

/* Check if developer mode is enabled to display console messages */
if (typeof(denali_config) === 'undefined') {
  denali_config = false;
}else{
  denali_log("Global JS: Denali Global JS Loaded.");
}

/* Check if using IE */
var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false;

if(typeof document.documentMode != 'undefined') {
  ie7 = (document.documentMode == 7) ? true : false;
}

/* Perform actions that can be done as soon as the document is done loading (not counting external elements such as images)*/


jQuery(document).ready(function() {

  /* Enable Fancybox, if function exists, for all links with fancybox_image class and gallery itmes */
  if(typeof jQuery.fn.fancybox == 'function') {
    jQuery("a.fancybox_image, .gallery-item a").fancybox({
      'transitionIn'  :   'elastic',
      'transitionOut' :   'elastic',
      'speedIn'       :   600,
      'speedOut'      :   200,
      'overlayShow'   :   false
    });
  }

  /* Cycle through all tabbed widget areas and remove any tabs that do not have corresponding content areas */
  jQuery(".denali_widget_area_tabs.have_tabs").each(function() {

    var tabbed_widget_area = this;
    var tabs_wrapper = jQuery(".denali_widget_tabs", tabbed_widget_area);
    var valid_tab_count =  jQuery("a.denali_tab_link", tabs_wrapper).length;


    jQuery("a.denali_tab_link", tabs_wrapper).each(function() {

      var tab_wrapper = jQuery(this).closest("li.denali_tab");
      var this_link = jQuery(this).attr("href").replace("#", "");
      var this_container_element = jQuery("#" + this_link, tabbed_widget_area);

      /* Removed widget titles since they are displayed as tabs */
      jQuery(".widget-title,.widgettitle",tabbed_widget_area).remove();

    });

    /* Convert Widget Area into regular (whout tabs) if only one tab left */
    if(valid_tab_count < 1) {
      jQuery(tabbed_widget_area).removeClass("have_tabs");
      jQuery(tabbed_widget_area).addClass("no_tabs");

      /* Display the element */
      jQuery(tabbed_widget_area).css('position','static');
      jQuery(tabbed_widget_area).css('left','0');

    } else {

      /* Create Tabs and Show Widget Area if everything checks out */
      denali_render_tabbed_widget_area(tabbed_widget_area);
    }


  });


  /* Run certain functions if global or property slideshow exists on  this page */
  if(jQuery('#global_property_search_home').length != 0 && jQuery('.wpp_slideshow_global_wrapper').length != 0) {
    //denali_fix_slideshow_dom();
  }
  if(jQuery('#global_property_search_home').length != 0) {
    denali_fix_search_on_slideshow();
  }

  /* Handle contact form submission */
  jQuery("#commentform").submit(function() {
    return denali_submit_contact_form();
  });



  /* IE7 Hack for properties sorter's and pagination's elements positions   */
  if(ie7) {
    var pp = jQuery('#properties_pagination');
    if(pp.length > 0) {
      var ppChilds = pp.children();
      var ppWidth = 0;
      for(i=0; i<ppChilds.length; i++) {
        var el = jQuery(ppChilds[i]);
        ppWidth += el.width() + parseInt(el.css('marginLeft')) + parseInt(el.css('marginRight')) + 1;
      }
      pp.width(ppWidth);
    }
  }

  /* Add class in li element in fields with select */
  jQuery('select.wpp_search_select_field').parent().addClass('select_class');

  /* Handle header dropdown menus */
  denali_header_dropdown_menus();

  /* Submit Contact Form */
  jQuery("#denali_contact_form").submit(function(event) {
    event.preventDefault();
    var data = {
      action: 'denali_contact_form_submit',
      nonce: denali_config.nonce,
      name: jQuery("#denali_contact_form #contact_name").val(),
      email: jQuery("#denali_contact_form #contact_email").val(),
      phone: jQuery("#denali_contact_form #contact_phone").val(),
      subject: jQuery("#denali_contact_form #contact_subject").val(),
      message: jQuery("#denali_contact_form #contact_message").val()
    }
    var loader = jQuery(".ajax_loader", this);
    loader.show();
    jQuery.post(
      denali_config.ajax_url,
      data,
      function(result) {
        loader.hide();
        if(result.success == 'true') {
          jQuery('.header_contact_div li.form').html("<p class='denali_contact_form_success'>"+denali_config.message_submission+"</div>");
        } else {
          var error_message = '';
          if(typeof result.errors == 'object') {
            error_message += '<ul class="errors">';
            for(var i in result.errors) {
              if(typeof result.errors != 'function') {
                error_message += '<li>' + result.errors[i] + '</li>';
              }
            }
            error_message += '</ul>';
          } else {
            error_message = "Sorry, something wrong. Please, try again later.";
          }
          jQuery("#denali_contact_form *").removeClass('denali_contact_form_validation_error');
          for(var i in result.errors) {
            var value = result.errors[i];
            jQuery("#denali_contact_form #contact_" + i).addClass('denali_contact_form_validation_error');
          }
          jQuery('.header_contact_div li.form .ajax_error').show();
          jQuery('.header_contact_div li.form .ajax_error').html(error_message);

          if(jQuery("#denali_contact_form").height() > jQuery(".header_contact_section").height()) {
            jQuery(".header_contact_section").height(jQuery("#denali_contact_form").height());
          }
        }
      },
      "json"
    );
    return false;
  });
  /* END Submit Contact Form */

  jQuery(document).bind('denali::header_section::toggle', function() {
    /** IE7 Hack. On toggling Header Section IE7 doesn't redraw Logo and header buttons position. We help him to do it. peshkov@UD */
    if( ie7 ) {
      var logo_wrapper = jQuery('.mid.theme_full_width');
      if(logo_wrapper.length > 0 ) {
        var h = logo_wrapper.height();
        logo_wrapper.height( h + 1 );
        logo_wrapper.height( h );
      }
    }
  });

});

/* Perform actions that require images to be fully loaded */
jQuery(window).load(function() {
  denali_resize_dom_elements();
});


/* Run Equal Heights on Grid Template after pagination is complete */
jQuery(document).bind('wpp_pagination_change_complete', function(e, data) {
  jQuery(".wpp_property_overview_shortcode .wpp_grid_view .property_div img:visible").load(function(){
    denali_resize_dom_elements();
  });
});


/**
 * Generated jQuery UI Tabs from Tabbed Widget Areas
 *
 * Ran twice, once on document.ready and then on windows.load to avoid getting stuck on external assets
 *
 */
function denali_render_tabbed_widget_area(tabbed_widget_area) {

  function fire_event_on_changing_tab( ui ) {
    setTimeout(function(){
      if(typeof jQuery.scrollTo == 'function') {
        var position = jQuery(ui.newPanel).offset();
        jQuery.scrollTo(position.top - 40 + 'px', 1500);
      }
      /* Redraws supermap on tab selection */
      jQuery(document).trigger( 'wpp_redraw_supermaps' );
      /* Resizes elements if needed */
      denali_resize_dom_elements();
    }, 700);
  }

  var settings = {};
  if( typeof wpp.version_compare == 'function' && wpp.version_compare( jQuery.ui.version, '1.10', '>=' ) ) {
    settings.activate = function(event, ui) {
      fire_event_on_changing_tab( ui );
    }
  } else {
    settings.select = function(event, ui) {
      ui.newPanel = ui.panel;
      fire_event_on_changing_tab( ui );
    }
  }
  jQuery(tabbed_widget_area).tabs( settings );

  if(denali_config.developer) {
    denali_log("Global JS: Rendering Tabbed Widget Area.");
  }

  jQuery(tabbed_widget_area).css('position','static');
  jQuery(tabbed_widget_area).css('left','0');
}


/**
 * Applies equalHeights to various elements.
 *
 */
function denali_resize_dom_elements() {

  if(denali_config.developer) {
    denali_log("Global JS: Applying equalHeights()");
  }

  if( !jQuery(".inner_footer div.equal_heights:visible").hasClass( 'equalHeights_added' ) ) {
    jQuery(".inner_footer div.equal_heights:visible").addClass( 'equalHeights_added' ).equalHeights();
  }


  jQuery(".inner_footer").each(function() {
    if( !jQuery(".property_widget_block:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".property_widget_block:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

  /* Adjust Property Listing widgets in horizontal widget areas */
  jQuery(".content_horizontal_widget .denali_widget").each(function() {
    if( !jQuery(".property_widget_block:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".property_widget_block:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

  jQuery(".wpp_grid_view").each(function() {
    if( !jQuery(".property_div:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".property_div:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

  jQuery(".wpp_featured_properties_shortcode").each(function() {
    if( !jQuery(".wpp_featured_property_container:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".wpp_featured_property_container:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

  jQuery(".wpp_featured_properties_widget").each(function() {
    if( !jQuery(".property_widget_block:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".property_widget_block:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

  jQuery(".wpp_latest_properties_widget").each(function() {
    if( !jQuery(".property_widget_block:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".property_widget_block:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

  jQuery(".wpp_property_overview_shortcode .wpp_grid_view").each(function() {
    if( !jQuery(".property_div:visible", this).hasClass( 'equalHeights_added' ) ) {
      jQuery(".property_div:visible", this).addClass( 'equalHeights_added' ).equalHeights();
    }
  });

}


/**
 * Handles header dropdown menus.
 *
 */
function denali_header_dropdown_menus() {

  var all_tabs = jQuery('div.disbl div').length;
  var dropdown_wrapper = jQuery(".denali_header_dropdown_links");
  var dropdown_section_wrapper = jQuery(".denali_header_expandable_sections");
  var dropdown_sections = jQuery(".denali_header_expandable_sections .header_dropdown_div");

  /* Reset sections after they are loaded to normal hidden settings */
  jQuery(dropdown_sections).css('position','static');
  jQuery(dropdown_sections).css('left','0');
  jQuery(dropdown_sections).hide();

  jQuery('ul.log_menu li a').click(function(e) {

    var this_link = this;
    var open_section = jQuery(".denali_header_expandable_sections .header_dropdown_div:visible");
    var open_section_id = jQuery(open_section).attr("id");

    /* Do nothing if a regular link was clicked */
    if(jQuery(this_link).attr('href') != '#') {
      return;
    } else {
      e.preventDefault();
    }

    var this_tab = jQuery(this_link).closest(".denali_tab_wrapper");
    var section_id = jQuery(this).attr('section_id');
    var this_section = jQuery("#" + section_id, dropdown_section_wrapper);

    if(jQuery(this_section).is(":visible")) {
      var this_section_open = true;
      //denali_log("Global JS: this section is open");
    } else {
      var this_section_open = false;
      //denali_log("Global JS: this section is closed");
    }

    /* If clicked section is already open, we close it */
    if(this_section_open && (section_id == open_section_id)) {
      jQuery(this_section).slideUp("slow", function() {
        jQuery(document).trigger("denali::header_section::toggle");
      });
      //denali_log("Global JS: closing this section");
      return;
    }

    /* If a section is open, and we re switching sections, close open one first */
    if(open_section.length) {
      jQuery(open_section).slideUp("fast", function() {
        /* Open new section */
        jQuery(this_section).slideDown("slow", function() {
          denali_header_section_opened();
          jQuery(document).trigger("denali::header_section::toggle");
        });

      });
    } else {

      /* Open new section */
      jQuery(this_section).slideDown("slow", function() {
        denali_header_section_opened();
        jQuery(document).trigger("denali::header_section::toggle");
      });

    }

  });

}

/* Executed when a header dropdown section is opened. */
function denali_header_section_opened() {

  /* Render the Google Map is header location dropdown.  */
  if(jQuery("li.header_contact_section").is(":visible") && jQuery("li.header_contact_section").height() > 0) {
    denali_header_location_map();
    jQuery("li.header_contact_section").equalHeights();
  }

  if(jQuery("li.header_login_section").is(":visible") && jQuery("li.header_login_section").height() > 0) {
    jQuery("li.header_login_section").equalHeights();
  }

}


/* Renders the map in header dropdown */
function denali_header_location_map() {

  var header_map_fail = false;

  if(denali_config.developer) {
    denali_log("Global JS: Running: denali_header_location_map()");
  }

  if (typeof denali_config.location_coords == 'undefined') {
    if(denali_config.developer) {
      denali_log("Global JS: No coordinates set.");
    }
    header_map_fail = true;
  }

  if(typeof google === 'undefined') {
    if(denali_config.developer) {
      denali_log("Global JS: Google Maps not loaded.");
    }
    header_map_fail = true;
  }

  if(header_map_fail) {
    jQuery("#denali_header_location_map").hide();
    return;
  }

  /* Bil if no coordinates set, or map is already rendered */
  if(denali_config.location_coords.grid) {
    if(denali_config.developer) {
      denali_log("Global JS: Header map already drawn.");
    }
    return;
  }

  denali_config.location_coords.grid = new google.maps.LatLng(denali_config.location_coords.latitude,denali_config.location_coords.longitude);

  if(denali_config.developer) {
    denali_log("Global JS: Drawing header map.");
  }

  var myOptions = {
    zoom: 8,
    center: denali_config.location_coords.grid,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var denali_header_location_map = new google.maps.Map(document.getElementById("denali_header_location_map"), myOptions);

  var marker1 = new google.maps.Marker({
    position: denali_config.location_coords.grid,
    map: denali_header_location_map,
    title: denali_config.location_name
  });

  denali_header_location_map.setCenter(denali_config.location_coords.grid);

}


/**
 * Fixes height of slider where global slideshow is present to match search form
 *
 */
function denali_fix_slideshow_dom() {
  var widget_height = jQuery("#global_property_search_home").height();

  var slideshow_height = jQuery(".wpp_slideshow_global_wrapper").height();
  var difference = (widget_height - slideshow_height);

  // If sidebar exists

  if(jQuery('#content .sidebar').length != 0) {
    jQuery("#content .sidebar").css('margin-top', (difference - 25) + 'px');
  } else {
    jQuery("#content").css('margin-top', (difference - 15) + 'px');
  }

}

/**
 * Fixes display of search form:
 * Hides search elements that do not fit in the container
 *
 * @author Maxim Peshkov
 */
function denali_fix_search_on_slideshow() {
  var container = jQuery('ul.wpp_search_elements', '#global_property_search_home');
  if(!container.length > 0) {
    return false;
  }
  var h = jQuery('ul.wpp_search_elements', '#global_property_search_home').height();
  var fh = 0;
  var fields = jQuery('li.wpp_search_form_element', '#global_property_search_home');
  fields.each(function(i,e){
    if(!jQuery(e).hasClass('submit')) {
      fh = fh + jQuery(e).outerHeight(true);
      if(fh > h) {
        jQuery(e).hide();
      }
    }
  });
  jQuery('ul.wpp_search_elements').css('overflow', 'visible');
}

/**
 * Cycles through form elements and throws an error if any fields that require validation are empty.
 *
 */
function denali_submit_contact_form() {

  var form_is_good = true;

  // unset any validation failures from last run
  jQuery("#commentform *[aria-required='true']").removeClass('wpp_validation_fail');

  // Check if any required fields are not filled out

  jQuery("#commentform *[aria-required='true']").each(function(index,element) {

    if(jQuery(element).val() == '') {
      jQuery(element).addClass('wpp_validation_fail');
      form_is_good = false;
    }

    // Special provision for e-mail
    if(jQuery(element).attr('name') == 'email') {
      if(!denali_email_validate(jQuery(element).val())) {
        jQuery(element).addClass('wpp_validation_fail');
        form_is_good = false;
      }
    }

  });

  return form_is_good;

}

 /**
   * Validates e-mail address.
   *
   * Source: http://www.white-hat-web-design.co.uk/articles/js-validation.php
   *
   */
function denali_email_validate(email) {
   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if(reg.test(email) == false) {
      return false;
   }

   return true;
}