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
  $siteKey = get_option('elementor_pro_recaptcha_site_key');
  if($id==57) {
    $register_info = get_field('add_event_page_register','option');
    $register_info = ($register_info) ? email_obfuscator($register_info) : '';
    if($register_info) { ?>
      <div id="event-register-text" style="display:none;"><div class="reg-info"><?php echo $register_info ?></div></div>
      <script src="https://www.google.com/recaptcha/api.js"></script>
      <script>
      jQuery(document).ready(function($){
        if( $('form#tribe_events_community_login').length ) {
          $('#event-register-text').insertBefore('.tribe-community-events');
          $('body').addClass('submit-event-page');
          $('form#tribe_events_community_login input#user_login').attr('required',true);
          $('form#tribe_events_community_login input#user_pass').attr('required',true);

          var reCaptcha = '<div id="g-recaptcha" class="g-recaptcha" data-sitekey="<?php echo $siteKey?>"></div>';
          $(reCaptcha).insertAfter('form#tribe_events_community_login .login-password');
        }

        $('#tribe_events_community_login').on('submit',function(e){
          var response = grecaptcha.getResponse();
          if(response.length == 0) {
           var reCaptchaError = '<div id="login_error" class="tribe-community-notice tribe-community-notice-error"><strong>Error verifying reCAPTCHA, please try again.</strong></div>';
           $('.tribe-community-events').prepend(reCaptchaError);
           return false;
          } else {
            $('#login_error').remove();
            return true;
          }
        });

      });
      </script>
    <?php
    }
  }
?>
<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/custom.js"></script>
<?php 
$sponsoredBy = '';
if ( is_single() ) { 
  $taxonomy = 'category';
  if( $terms = get_the_terms(get_the_ID(),$taxonomy) ) {
    $sponsor_id = get_field('category_sponsor', $taxonomy . '_' . $terms[0]->term_id);
    if($sponsor_id) {
      $sponsor_logo = get_field('sponsor_logo',$sponsor_id);
      $sponsor_website = get_field('sponsor_website',$sponsor_id);
      $sponsor_description = get_field('sponsor_description',$sponsor_id);
      $open_link = '';
      $close_link = '';
      $sponsorship_policy = get_field('sponsorship_policy','option');
      $sp_target = (isset($sponsorship_policy['target']) && $sponsorship_policy['target']) ? $sponsorship_policy['target'] : '_self';
      $sp_link = (isset($sponsorship_policy['url']) && $sponsorship_policy['url']) ? $sponsorship_policy['url'] : '';
      $sp_title = (isset($sponsorship_policy['title']) && $sponsorship_policy['title']) ? $sponsorship_policy['title'] : '';
      if($sponsor_website) {
        $open_link = '<a href="'.$sponsor_website.'" target="_blank">';
        $close_link = '</a>';
      } ?>
      <?php if ($sponsor_logo) { ?>
      <div id="sponsoredbyBlock" style="display:none;">
        <div class="sponsored-heading">Sponsored By:</div>
        <?php if ($sponsor_logo) { ?>
        <div class="sponsored-logo">
          <?php echo $open_link ?><img src="<?php echo $sponsor_logo['url'] ?>" alt="<?php echo $sponsor_logo['title'] ?>"><?php echo $close_link ?>
        </div> 
        <?php } ?>
        <?php if ($sponsor_description) { ?>
        <div class="sponsor-description"><?php echo $sponsor_description ?></div>
        <?php } ?>
        <?php if ($sp_title && $sp_link) { ?>
        <div class="sponsor-policy">
          <a href="<?php echo $sp_link ?>" target="<?php echo $sp_target ?>"><?php echo $sp_title ?></a>
        </div>
        <?php } ?>
      </div>

      <div id="sponsorPoweredBy" style="display:none;">
        <?php if ($sponsor_logo) { ?>
        <div class="sponsored-logo">
          <div class="sponsor-heading">Powered By</div>
          <?php echo $open_link ?><img src="<?php echo $sponsor_logo['url'] ?>" alt="<?php echo $sponsor_logo['title'] ?>"><?php echo $close_link ?>
        </div> 
        <?php } ?>
      </div>
      <?php } ?>
    <?php } ?>
  <?php } ?>
  <script>
  jQuery(document).ready(function($){
    if( $('#sponsoredbyBlock').length && $('.elementor-widget-author-box').length ) {
      $('#sponsoredbyBlock').appendTo('.elementor-widget-author-box');
      $('.elementor-widget-author-box #sponsoredbyBlock').show();
      $('#sponsorPoweredBy').prependTo('#postcontent').show();
    }
  });
  </script>
<?php } ?>
</body>
</html>
