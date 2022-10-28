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
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if(basename($actual_link)=='calendar') {
  header("Location: " . get_site_url() . '/calendar/photo/?hide_subsequent_recurrences=1');
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
  <?php 
  $mobileLogo =  get_field('sitelogo_mobile','option'); 
  $mobileLogoURL = ( isset($mobileLogo['url']) && $mobileLogo['url'] ) ? $mobileLogo['url'] : '';
  if($mobileLogo && is_numeric($mobileLogo)) {
    $mobileLogoURL = wp_get_attachment_url($mobileLogo);
  } ?>
	<script>
		var qcitySiteURL = '<?php echo get_site_url() ?>';
		var siteThemeURL='<?php echo get_stylesheet_directory_uri() ?>';
		var logoMobile = '<?php echo $mobileLogoURL ?>';
    var qcityLogoSmall = '<?php echo getQcityMetroLogo() ?>';
	</script>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/assets/css/custom.css' ?>">
	<?php if ( is_singular('tribe_events') ) { ?>
	<script>var geodir_params='';</script>
	<?php } ?>

	<?php if ( is_singular( array( 'post', 'tribe_events' ) ) ) { 
	global $post; $pid = $post->ID; $thumbid = get_post_thumbnail_id($pid); 
	$content = ($post->post_content) ? shortenText(strip_tags($post->post_content),150," ","...") : ''; ?>
	<meta property="og:url" content="<?php echo get_permalink(); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo get_the_title(); ?>" />
	<meta property="og:description" content="<?php echo $content ?>" />
	<?php if( $post_image = wp_get_attachment_image_src($thumbid,'large') ) { ?>
	<meta property="og:image" content="<?php echo $post_image[0] ?>" />
	<?php } ?>
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

if( is_archive() ) { get_template_part( 'template-parts/hero-calendar-page' ); } ?>

