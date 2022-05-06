<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<main id="content" class="site-main" role="main">
	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<header class="page-header">
			<h1 class="entry-title"><?php esc_html_e( 'The page doesn&rsquo;t exist!', 'hello-elementor' ); ?></h1>
		</header>
	<?php endif; ?>
	<div class="page-content">
		<p>
      <?php esc_html_e( "Sorry, the page you were looking for could not be found.", "hello-elementor" ); ?><BR>
      <?php esc_html_e( "Or you can return to our home page, or contact us if you can't find what you are looking for.", "hello-elementor" ); ?>
    </p>
	</div>

</main>
