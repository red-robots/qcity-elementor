<?php
/**
 * View: Photo Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var string $placeholder_url The url for the placeholder image if a featured image does not exist.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$classes = get_post_class( [ 'tribe-common-g-col', 'tribe-events-pro-photo__event' ], $event->ID );

if ( ! empty( $event->featured ) ) {
	$classes[] = 'tribe-events-pro-photo__event--featured';
}
$taxonomy = 'tribe_events_cat';
$terms =  get_the_terms( get_the_ID(), $taxonomy);
$cat = ( isset($terms[0]) ) ? $terms[0] : '';
?>
<article <?php tribe_classes( $classes ) ?>>

	<?php $this->template( 'photo/event/featured-image', [ 'event' => $event ] ); ?>

	<div class="tribe-events-pro-photo__event-details-wrapper">
		<?php //$this->template( 'photo/event/date-tag', [ 'event' => $event ] ); ?>
		<div class="tribe-events-pro-photo__event-details">
      <?php if ($cat) { ?>
      <div class="category"><a href="<?php echo get_term_link($cat,$taxonomy); ?>"><?php echo $cat->name; ?></a></div> 
      <?php } ?>
      <?php $this->template( 'photo/event/title', [ 'event' => $event ] ); ?>
      <?php if (tribe_get_venue()) { ?>
      <div class="venue"><?php echo tribe_get_venue(); ?></div>
      <?php } ?>
			<?php $this->template( 'photo/event/date-time', [ 'event' => $event ] ); ?>
			<?php // $this->template( 'photo/event/cost', [ 'event' => $event ] ); ?>
		</div>
	</div>

</article>
