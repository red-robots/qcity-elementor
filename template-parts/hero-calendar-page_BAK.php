<?php if ( is_archive() ) { 
/* CALENDAR PAGE HERO */
$obj = get_queried_object();
if( isset($obj->name) && $obj->name=='tribe_events' ) { 
$calendar_image = get_field('calendar_featured_image','option'); 
$calendar_buttons = get_field('calendar_buttons','option'); 
$calendar_page_ads = get_field('calendar_page_ads','option'); 
?>
<div id="customElementHead">
  <?php if($calendar_image) { ?>
  <div class="pageHero">
    <div class="image" style="background-image:url('<?php echo $calendar_image['url'] ?>');"></div>
  </div>
  <?php } ?>
  <?php if($calendar_buttons) { ?>
  <div class="calendar-buttons">
    <?php $bn=1; foreach ($calendar_buttons as $b) { 
      $link = $b['link'];
      $title = (isset($link['title']) && $link['title']) ? $link['title'] : '';
      $url = (isset($link['url']) && $link['url']) ? $link['url'] : '';
      $target = (isset($link['target']) && $link['target']) ? $link['target'] : '_self';
      $img = $b['image'];
      $bgcolor = ($b['bgcolor']) ? $b['bgcolor'] : '#c5a92e';
      $bgcolorHover = ($b['bgcolor_hover']) ? $b['bgcolor_hover'] : '#f16522';
      if($title && $url) { ?>
      <style>.c-btn-<?php echo $bn ?>{background-color:<?php echo $bgcolor ?>;}.c-btn-<?php echo $bn ?>:hover{background-color:<?php echo $bgcolorHover ?>;}</style>
      <a href="<?php echo $url ?>" target="<?php echo $target ?>" class="button c-btn-<?php echo $bn ?><?php echo ($img) ? ' has-image':''; ?>">
        <span><?php echo $title ?></span>
        <?php if ($img) { ?>
          <img src="<?php echo $img['url'] ?>" alt="<?php echo $img['title'] ?>">
        <?php } ?>
      </a> 
      <?php $bn++; } ?>
    <?php } ?>
  </div>
  <?php } ?>
</div>
<?php } ?>

<?php if ( $calendar_page_ads ) { ?>
<div class="calendar-page-ads">
  <div class="tribe-common-l-container tribe-events-l-container"><?php echo $calendar_page_ads ?></div>
</div>  
<?php } ?>

<?php } ?>