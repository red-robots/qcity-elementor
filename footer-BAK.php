<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
		get_template_part( 'template-parts/dynamic-footer' );
	} else {
		get_template_part( 'template-parts/footer' );
	}
}
?>
<?php wp_footer(); ?>

<?php  
  /* Submit An Event page (Non-Loggedin Users) */
  global $post;
  $id = (isset($post->ID) && $post->ID) ? $post->ID : 0;
  if($id==57) {
    $register_info = get_field('add_event_page_register','option');
    $register_info = ($register_info) ? email_obfuscator($register_info) : '';
    if($register_info) { ?>
      <div id="event-register-text" style="display:none;"><div class="reg-info"><?php echo $register_info ?></div></div>
      <script>
      jQuery(document).ready(function($){
        if( $('form#tribe_events_community_login').length ) {
          $('#event-register-text').insertBefore('.tribe-community-events');
          $('body').addClass('submit-event-page');
          // $('.tribe-ce-lostpassword').appendTo('.tribe-ce-register');
        }
      });
      </script>
    <?php
    }
  }
?>
<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/custom.js"></script>
</body>
</html>
