<?php
/**
 * View: Events Bar Views List
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/components/events-bar/views/list.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var array $public_views Array of data of the public views, with the slug as the key.
 */
?>
<div
	class="tribe-events-c-view-selector__content"
	id="tribe-events-view-selector-content"
	data-js="tribe-events-view-selector-list-container"
>
	<ul class="tribe-events-c-view-selector__list">
		<?php foreach ( $public_views as $public_view_slug => $public_view_data ) : ?>
			<?php 
      if($public_view_slug=='photo') {
        if( !str_contains($public_view_data->view_url,'?hide_subsequent_recurrences') ) {
          $public_view_data->view_url .= '?hide_subsequent_recurrences=1';
        }
      } else {
        $public_view_data->view_url = str_replace('?hide_subsequent_recurrences=1','',$public_view_data->view_url);
      }

      $this->template(
				'components/events-bar/views/list/item',
				[ 'public_view_slug' => $public_view_slug, 'public_view_data' => $public_view_data ]
			); 

      ?>
		<?php endforeach; ?>
	</ul>
</div>
