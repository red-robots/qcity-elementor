<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();

$event_id = get_the_ID();

/**
 * Allows filtering of the single event template title classes.
 *
 * @since 5.8.0
 *
 * @param array  $title_classes List of classes to create the class string from.
 * @param string $event_id The ID of the displayed event.
 */
$title_classes = apply_filters( 'tribe_events_single_event_title_classes', [ 'tribe-events-single-event-title' ], $event_id );
$title_classes = implode( ' ', tribe_get_classes( $title_classes ) );

/**
 * Allows filtering of the single event template title before HTML.
 *
 * @since 5.8.0
 *
 * @param string $before HTML string to display before the title text.
 * @param string $event_id The ID of the displayed event.
 */
$before = apply_filters( 'tribe_events_single_event_title_html_before', '<h1 class="' . $title_classes . '">', $event_id );

/**
 * Allows filtering of the single event template title after HTML.
 *
 * @since 5.8.0
 *
 * @param string $after HTML string to display after the title text.
 * @param string $event_id The ID of the displayed event.
 */
$after = apply_filters( 'tribe_events_single_event_title_html_after', '</h1>', $event_id );

/**
 * Allows filtering of the single event template title HTML.
 *
 * @since 5.8.0
 *
 * @param string $after HTML string to display. Return an empty string to not display the title.
 * @param string $event_id The ID of the displayed event.
 */
$title = apply_filters( 'tribe_events_single_event_title_html', the_title( $before, $after, false ), $event_id );

$taxonomy = 'tribe_events_cat';
$terms =  get_the_terms( $event_id, $taxonomy);
$cat = ( isset($terms[0]) ) ? $terms[0] : '';
?>

<section class="tribe-hero">
  <div class="inner">
    <div class="flexwrap">
      <div class="fxleft">
        <div class="event-cat">
          <div class="goback">
            <!-- <a href="<?php //echo esc_url( tribe_get_events_link() ); ?>"> <?php //printf( '&laquo; ' . esc_html_x( 'All %s', '%s Events plural label', 'the-events-calendar' ), $events_label_plural ); ?></a> -->
            <a href="<?php echo get_site_url() ?>/calendar/"><?php printf( '&laquo; ' . esc_html_x( 'All %s', '%s Events plural label', 'the-events-calendar' ), $events_label_plural ); ?></a>
          </div>  
          <div class="wrap">
            <?php if ($cat) { ?><div class="cat"><span><?php echo $cat->name; ?></span></div><?php } ?>
            <?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
          </div>
        </div>
        <div class="titlediv">
          <?php echo $title; ?>
        </div>
      </div>
      <div class="fxright">
        <?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>
      </div>
    </div>
  </div>
</section>

<div id="tribe-events-content" class="tribe-events-single">

  <div class="wrapper_full">
  	<!-- Notices -->
  	<?php tribe_the_notices() ?>

    <?php $is_show = false; ?>

    <?php if ( $is_show ) { ?>
      
  	<div class="tribe-events-schedule tribe-clearfix">
  		<?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
  		<?php if ( tribe_get_cost() ) { ?>
  			<span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ) ?></span>
  		<?php } ?>
  	</div>

  	<!-- Event header -->
  	<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
  		<!-- Navigation -->
  		<nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
  			<ul class="tribe-events-sub-nav">
  				<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
  				<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
  			</ul>
  			<!-- .tribe-events-sub-nav -->
  		</nav>
  	</div>
  	<!-- #tribe-events-header -->

    <?php } ?>

  	<?php while ( have_posts() ) :  the_post(); ?>
  		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <div class="flexwrap-event-info">

          <div class="flexcol first">
      			<!-- Event featured image, but exclude link -->
      			<?php //echo tribe_event_featured_image( $event_id, 'full', false ); ?>

      			<!-- Event content -->
      			<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
      			<div class="tribe-events-single-event-description tribe-events-content">
              <?php  
                $post_id = get_the_ID();
                $tooltip = '';
                if ( tribe_is_recurring_event( $post_id ) ) {
                  $tooltip .= '<div class="tribe-events-schedule tribe-clearfix">';
                  $tooltip .= '<div class="recurringinfo">';
                  $tooltip .= '<div class="event-is-recurring">';
                  $tooltip .= '<span class="tribe-events-divider">|</span>';
                  $tooltip .= sprintf( esc_html__( 'Recurring %s', 'tribe-events-calendar-pro' ), tribe_get_event_label_singular() );
                  $tooltip .= sprintf( ' <a href="%s">%s</a>',
                    esc_url( tribe_all_occurences_link( $post_id, false ) ),
                    esc_html__( '(See all)', 'tribe-events-calendar-pro' )
                  );
                  $tooltip .= '<div id="tribe-events-tooltip-'. $post_id .'" class="tribe-events-tooltip recurring-info-tooltip">';
                  $tooltip .= '<div class="tribe-events-event-body">';
                  $tooltip .= tribe_get_recurrence_text( $post_id );
                  $tooltip .= '</div>';
                  $tooltip .= '<span class="tribe-events-arrow"></span>';
                  $tooltip .= '</div>';
                  $tooltip .= '</div>';
                  $tooltip .= '</div>';
                  $tooltip .= '</div>';
                }
                echo $tooltip;
              ?>
              <?php if ( get_the_content() ) { ?>
               <div class="event-main-content">
                 <?php the_content(); ?>
               </div> 
              <?php } ?>
      			</div>
      			<!-- .tribe-events-single-event-description -->
      			<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
          </div>

          <div class="flexcol second">
      			<!-- Event meta -->
      			<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
      			<?php tribe_get_template_part( 'modules/meta' ); ?>
          </div>

        </div>


        <div class="related-stuff-section">
          <div class="wrapper_sm">
            <?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
          </div>
        </div>


  		</div> <!-- #post-x -->
  		<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
  	<?php endwhile; ?>

  	<!-- Event footer -->
  	<div id="tribe-events-footer">
  		<!-- Navigation -->
  		<nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
  			<ul class="tribe-events-sub-nav">
  				<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
  				<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
  			</ul>
  			<!-- .tribe-events-sub-nav -->
  		</nav>
  	</div>
  	<!-- #tribe-events-footer -->
  </div>
</div><!-- #tribe-events-content -->
