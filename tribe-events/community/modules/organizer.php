<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox For Organizers
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating an organizer for user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/organizer.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  2.1
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

// If the user cannot create new organizers *and* if there are no organizers
// to select from then there's no point in generating this UI
if ( ! tribe( 'community.main' )->event_form()->should_show_linked_posts_module( Tribe__Events__Organizer::POSTTYPE ) ) {
  return;
}

if ( ! isset( $event ) ) {
  $event = Tribe__Events__Main::postIdHelper();
}
?>

<div id="event_tribe_organizer" class="tribe-section tribe-section-organizer">
  <div class="tribe-section-header">
    <h3 class="<?php echo tribe_community_events_field_has_error( 'organizer' ) ? 'error' : ''; ?>">
      <?php
      printf( __( '%s', 'tribe-events-community' ), 'Organizer Contact Details ' );
      //printf( __( '%s Details', 'tribe-events-community' ), tribe_get_organizer_label_singular() );
      echo tribe_community_required_field_marker( 'organizer' );
      ?>
    </h3>
  </div>

  <div class="field-note">This information will not be published.</div>

  <?php
  /**
   * Allow developers to hook and add content to the beginning of this section
   */
  do_action( 'tribe_events_community_section_before_organizer' );
  ?>

  <table class="tribe-section-content">
    <colgroup>
      <col class="tribe-colgroup tribe-colgroup-label">
      <col class="tribe-colgroup tribe-colgroup-field">
    </colgroup>

    <?php
    tribe_community_events_organizer_select_menu( $event );

    // The organizer meta box will render everything within a <tbody>
    $organizer_meta_box = new Tribe__Events__Linked_Posts__Chooser_Meta_Box( $event, Tribe__Events__Organizer::POSTTYPE );
    $organizer_meta_box->render();
    ?>
  </table>
  <div class="hidden-field">
    <input type="text" name="organizer_data" data-error=".organizer_data_error" value="" class="required" style="position:absolute;z-index: -999;">
    <div class="organizer_data_error"></div>
  </div>
  

  <?php
  /**
   * Allow developers to hook and add content to the end of this section
   */
  do_action( 'tribe_events_community_section_after_organizer' );
  ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script>
jQuery(document).ready(function($){

  if( $('#saved_tribe_organizer').length ) {
    $('select#saved_tribe_organizer').val('').trigger('change');
    $(document).on('change','select#saved_tribe_organizer',function(){
      var opt = $(this).val();
      if(opt!='-1') {
        $('input[name="organizer_data"]').val( $(this).val() );
        $('#organizer_data-error.error').remove();
      } else {
        $('input[name="organizer_data"]').val('');
      }
    });
  }

  $(document).on('click','#event_tribe_organizer a.tribe-add-post',function(e){
    $('input.tribe_organizer-name').addClass('required');
    $('input.organizer-phone').addClass('required');
    $('input.organizer-email').addClass('required');

    $('.new-tribe_organizer input').each(function(){
      $(this).on('keypress',function(){
        if( $(this).val().trim().replace('/\s+/g','') ) {
          $('input[name="organizer_data"]').val( $(this).val().trim().replace('/\s+/g','') );
          $('#organizer_data-error.error').remove();
        } else {
          $('input[name="organizer_data"]').val('');
        }
      });
    });
  });

  $(document).on('click','#event_tribe_organizer a.tribe-delete-this',function(e){
    var tbody = $('#event_tribe_organizer tbody').length;
    var tr1 = $('#event_tribe_organizer tr.saved-linked-post').length;
    var tr2 = $('#event_tribe_organizer .new-tribe_organizer').length;
    var opt = $('select#saved_tribe_organizer').val();
    var def = (opt=='-1') ? '': opt;
    if(tr1==1 && tr2==0) {
      $('input[name="organizer_data"]').val('');
    } 
    else if(tbody==1) {
      $('input[name="organizer_data"]').val('');
    }
  });

  $(".tribe-community-events.form form").validate({
    errorElement: "div",
    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    },
    submitHandler: function(form) {
      // if (grecaptcha.getResponse()) {
      //   alert('This is only a DEMO!');
      // } else {
      //   alert('Please confirm reCaptcha to proceed.');
      // }
    }
  });
});
</script>