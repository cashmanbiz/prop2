<?php
/**
 * Content - Property Inquiry (or comments) Form.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Denali
 * @since Denali 3.0
 *
 */


  /**
   * if $_REQUEST['inquiry_sent'] is '2' then emails was successfully sent to agents,
   * otherwise ('1') we have two cases: either property has no Agents or
   * there were problems with sending emails.
   *
   * In both cases if $_REQUEST['inquiry_sent'] is set we can think that inquiry was sent
   */
  $is_inquiry_sent = isset($_REQUEST['inquiry_sent'])?true:false;

  //** Of course we can customise message for both cases above but not now */
  $inquiry_sent_message = __('Your inquiry was successfully sent!','denali');

  if(!empty($ds['wp_crm']['inquiry_forms'][$property['property_type']])) {
    global $wp_crm;

    $contact_form = $ds['wp_crm']['inquiry_forms'][$property['property_type']];

    if($contact_form != 'denali_default_form') {

      if(class_exists('class_contact_messages')) {
        $crm_form = class_contact_messages::shortcode_wp_crm_form(array('form' => $contact_form,'success_message'=>$inquiry_sent_message));
      }

    }
  } else {
    //** Default */
    $contact_form = 'denali_default_form';
  }

  if(!comments_open()) { return; }

  if($contact_form == 'no_form') { return; }

  if ($denali_theme_settings['show_property_comments'] == 'true' &&
      $denali_theme_settings['globally_disable_comments'] == 'true') {
    return;
  }

  if($denali_theme_settings['show_property_comments'] == 'true') {
    $title_reply = __('Comment about', 'denali') . ' ' . $post->post_title;
  } else {
    $title_reply = __('Enquire about', 'denali') .' '. $post->post_title;
  }


  if($denali_theme_settings['show_property_comments']!= 'true') {  ?>
     <a name="inquiry_form"></a> 
  <?php }

  if ($crm_form) {
	?>
	
    <div id="respond">
      <h3 id="reply-title"><?php comment_form_title( $title_reply ); ?></h3>
      <?php echo $crm_form; ?>
    </div><!-- #respond -->
    <?php
  }elseif (function_exists('wpp_inquiry_form')) {
    wpp_inquiry_form("title_reply=$title_reply&comment_notes_after=".(($is_inquiry_sent) ? urlencode('<p class="comment-notes success">' . $inquiry_sent_message . '</p>'):'')."&comment_notes_before=");
  } else {
    comment_form("title_reply=$title_reply&comment_notes_after=".(($is_inquiry_sent) ? urlencode('<p class="comment-notes success">' . $inquiry_sent_message . '</p>'):'')."&comment_notes_before=");
  }

  if($denali_theme_settings['show_property_comments'] == 'true') {  ?>
  <ol class="commentlist">
    <?php wp_list_comments( array( 'callback' => 'denali_comment' ), get_comments( array('post_id' => $post->ID, 'status' => 'approve', 'order' => 'ASC') ));?>
  </ol>
<?php }

