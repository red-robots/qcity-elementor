<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php $viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' ); ?>
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<script>var qcitySiteURL = '<?php echo get_site_url() ?>'; var siteThemeURL='<?php echo get_stylesheet_directory_uri() ?>';</script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/assets/css/custom.css' ?>">

  <?php if ( is_singular('tribe_events') ) { ?>
  <script>var geodir_params='';</script>
  <?php } ?>
  
</head>
<body <?php body_class(); ?>>
<?php if (is_singular('tribe_events')) { ?><div id="singlePostDataInfo" data-postid="<?php echo get_the_ID(); ?>"></div><?php } ?>
<?php
hello_elementor_body_open();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
		get_template_part( 'template-parts/dynamic-header' );
	} else {
		get_template_part( 'template-parts/header' );
	}
}
