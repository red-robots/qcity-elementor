<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form
 * The wrapper template for the event submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/submit.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since    4.5
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

$post_id = get_the_ID();

$events_label_singular = tribe_get_event_label_singular();

if ( tribe_is_event( $post_id ) ) {
	$button_label = sprintf( __( 'Update %s', 'tribe-events-community' ), $events_label_singular );
} else {
	$button_label = sprintf( __( 'Submit %s', 'tribe-events-community' ), $events_label_singular );
}
$button_label = apply_filters( 'tribe_community_event_edit_button_label', $button_label );
?>

<div class="tribe-events-community-footer">
	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 */
	do_action( 'tribe_events_community_section_before_submit' );
	?>

	<input
		type="submit"
		id="post"
		class="tribe-button submit events-community-submit"
		value="<?php echo esc_attr( $button_label ); ?>"
		name="community-event"
	/>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 */
	do_action( 'tribe_events_community_section_after_submit' );
	?>
</div>



<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/jquery.validate.min.js"></script>
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

    $('.new-tribe_organizer tr input').each(function(){
      $(this).on('keypress blur focusout',function(){
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

  
  $(document).on('keyup focusout blur','.new-tribe_organizer tr input',function(e){
    var inputVal = [];
    $("#event_tribe_organizer [name*=organizer]").each(function(){
      var option = $(this).val().trim().replace('/\s+/g','');
      var ival = (option=='-1') ? '':option;
      if( $(this).val().trim().replace('/\s+/g','') ) {
        inputVal.push( ival );
      }
    });
    if( inputVal.length ) {
      $('input[name="organizer_data"]').val( inputVal );
    } else {
      $('input[name="organizer_data"]').val('');
    }
  });

  var errorMessages = '';
  $(".tribe-community-events.form form").validate({
    // rules: {
    //   organizer_data: "required"
    // },
    // messages: {
    //   organizer_data: "Organizer Contact Details is required.",
    // },
    errorElement: "div",
    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    },
    invalidHandler: function(form, validator) {
      var errors = validator.numberOfInvalids();
      if(errors) {
        document.head.insertAdjacentHTML("beforeend", `<style>.tribe-community-notice-error{display:none}</style>`);
      } 
    },
    submitHandler: function(form) {
      form.submit();
      document.head.insertAdjacentHTML("beforeend", `<style>.tribe-community-notice-error{display:none}</style>`);
    }
  });

  /* upload */
  $(document).on('change','#EventImage',function(e){
    if( $(this).val() ) {
      var tmppath = URL.createObjectURL(e.target.files[0]);
      $("#image-placeholder svg").hide();
      $("#image-placeholder #imagehere").html('<img src="'+tmppath+'" alt="">');
      $('label[for="uploadFile"]').html('Filename: ' + e.target.files[0].name);
      $('.tribe-remove-upload').show();
    }
  });
  $(document).on('click','.tribe-remove-upload a',function(e){
    e.preventDefault();
    $("#image-placeholder svg").show();
    $("#image-placeholder img").remove();
    $('.choose-file.tribe-button').show();
    $("#EventImage").show();
    $('.tribe-remove-upload').hide();
  });

  $(document).on('click','.choose-file.tribe-button',function(e){
    e.preventDefault();
    $(this).hide();
    $("#EventImage").trigger('click');
    $("#EventImage").hide();
  });
  
});
</script>