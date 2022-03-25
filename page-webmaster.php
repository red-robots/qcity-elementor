<?php
$locations = array();  
$GOOGLE_MAP_API = getGoogleMapAPI();
$posttype = 'gd_businesses';
$entries = getGDCustomPost($posttype,10,0,'post_title','ASC');
if($entries) {
  foreach($entries as $row) {
    $terms = ( isset($row->categories) && $row->categories ) ? $row->categories : '';
    $termID = ( isset($terms[0]->term_id) && $terms[0]->term_id ) ? $terms[0]->term_id : '';
    $termName = ( isset($terms[0]->name) && $terms[0]->name ) ? $terms[0]->name : '';
    $mapIcon = ($termID) ? getGDMapCatIcon($termID,'ct_cat_icon') : '';
    $locations[] = array(
          'title'=>$row->post_title,
          'lat'=>$row->latitude,
          'lng'=>$row->longitude,
          'termID'=>$termID,
          'termName'=>$termName,
          'mapIcon'=>$mapIcon
        );
  }
}

// echo "<pre>";
// print_r( json_encode($locations) );
if($locations) { ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
    <style>
      #map {
        height: 100%;
      }

      /* Optional: Makes the sample page fill the window. */
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .container {
        max-width: 500px;
        width: 100%;
        height: 500px;
        position: relative;
        margin: 5% auto 0;
      }
    </style> 
    <script>
      // let map;
      // function initMap() {
      //   map = new google.maps.Map(document.getElementById("map"), {
      //     center: { lat: -27.288385709648246, lng: -80.36394541505115 },
      //     zoom: 5,
      //   });
      // }

      // function initMap() {
      //   const myLatLng = { lat: 27.288385709648246, lng: -80.36394541505115 };
      //   const map = new google.maps.Map(document.getElementById("map"), {
      //     zoom: 8,
      //     center: myLatLng,
      //   });

      //   new google.maps.Marker({
      //     position: myLatLng,
      //     map,
      //     title: "Hello World!",
      //   });
      // }
      function initMap() {
        var centerLatLng = [35.22143567075173, -80.84279184526504]; /* NASCAR Hall of Fame 400 E M.L.K. Jr Blvd, Charlotte, NC 28202 */
        var locations = <?php echo json_encode($locations); ?>

        // var locations = [
        //   ['1789 SW Hampshire Ln, Port Saint Lucie, FL 34953', 27.288385709648246, -80.36394541505115, 4],
        //   ['2334 SE Wald St, Port Saint Lucie, FL 34984', 27.271911644107902, -80.33420468010816, 5],
        // ];
        

        //console.log(locations[0]['title']);

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: new google.maps.LatLng(centerLatLng[0], centerLatLng[1]),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        
        var infowindow = new google.maps.InfoWindow();

        var marker, i;
        
        for (i = 0; i < locations.length; i++) {  
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i]['lat'], locations[i]['lng']),
            map: map,
            icon: locations[i]['mapIcon']
          });
          
          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i][0]);
              infowindow.open(map, marker);
            }
          })(marker, i));
        }
      }

    </script> 
  </head>
  <body>
  
    <div class="container">
      <div id="map"></div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLE_MAP_API ?>&callback=initMap&v=weekly" async></script>
   
  </body>
</html>
<?php } ?>
