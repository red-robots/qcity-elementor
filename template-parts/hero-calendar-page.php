<?php if ( is_archive() ) { 
/* CALENDAR PAGE HERO */
$obj = get_queried_object();
if( isset($obj->name) && $obj->name=='tribe_events' ) { 
$calendar_image = get_field('calendar_featured_image','option'); 
$calendar_buttons = get_field('calendar_buttons','option'); 
$calendar_page_ads = get_field('calendar_page_ads','option'); 
$button = get_field('calendar_hero_button','option'); 
$btnLink = (isset($button['url']) && $button['url']) ? $button['url'] : '';
$btnTitle = (isset($button['title']) && $button['title']) ? $button['title'] : '';
$btnTarget = (isset($button['target']) && $button['target']) ? $button['target'] : '_self';
$calendar_featured_logo = get_field('calendar_featured_logo','option'); 
$calendar_featured_logo_link = get_field('calendar_featured_logo_link','option'); 
$calendar_featured_logo_text = get_field('calendar_featured_logo_text','option'); 
?>
<div id="customElementHead">
  <?php if($calendar_image) { ?>
  <div class="pageHero">
    <div class="image" style="background-image:url('<?php echo $calendar_image['url'] ?>');"></div>
    <?php if ($btnLink && $btnTitle) { ?>
    <div class="hero-button">
      <a href="<?php echo $btnLink ?>" class="heroBtn" target="<?php echo $btnTarget ?>"><?php echo $btnTitle ?></a>
    </div> 
    <?php } ?>
  </div>
  <?php } ?>
</div>
<?php } ?>

<?php if ($calendar_featured_logo) { ?>
<div class="event-featured-logo">
  <?php if ($calendar_featured_logo_text) { ?>
   <span class="feat-logo-text"><?php echo $calendar_featured_logo_text ?></span> 
  <?php } ?>
  <?php if ($calendar_featured_logo_link) { ?>
    <a href="<?php echo $calendar_featured_logo_link ?>" target="_blank"><img src="<?php echo $calendar_featured_logo['url'] ?>" alt="<?php echo $calendar_featured_logo['title'] ?>"></a>
  <?php } else { ?>
    <img src="<?php echo $calendar_featured_logo['url'] ?>" alt="<?php echo $calendar_featured_logo['title'] ?>">
  <?php } ?>
</div>
<?php } ?>

<?php if ( $calendar_page_ads ) { ?>
<div class="calendar-page-ads">
  <div class="tribe-common-l-container tribe-events-l-container"><?php echo $calendar_page_ads ?></div>
</div>  
<?php } ?>

<?php } ?>