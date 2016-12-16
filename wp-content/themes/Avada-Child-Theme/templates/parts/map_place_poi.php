<? global $me; ?>
<div class="map-container gps-map">
  <div id="propertygpsmap" style="width: 100%; height: 450px;"></div>
</div>
<script type="text/javascript">
  var gpsmap, geo, pMarker;
  var inited_gps = <?=($gps['lat']) ? 1 : 0?>;
  (function($){
    geo     = new google.maps.Geocoder();
    var styledMapType = new google.maps.StyledMapType( [
  {
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f5f5f5"
      }
    ]
  },
  {
    "elementType": "labels.icon",
    "stylers": [
      {
        "visibility": "on"
      }
    ]
  },
  {
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#bf5153"
      }
    ]
  },
  {
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#f5f5f5"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#bdbdbd"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#eeeeee"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#ff8d00"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#e5e5e5"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#9e9e9e"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#ffffff"
      }
    ]
  },
  {
    "featureType": "road.arterial",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#757575"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dadada"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#616161"
      }
    ]
  },
  {
    "featureType": "road.local",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#9e9e9e"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#e5e5e5"
      }
    ]
  },
  {
    "featureType": "transit.station",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#eeeeee"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#c9c9c9"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#9e9e9e"
      }
    ]
  }
],
    {name: '<?=__('Letisztult', 'gh')?>'});

    var mapopt = {
      scrollwheel: false,
      center: {lat: <?=($gps['lat']) ? $gps['lat'] : '0'?>, lng: <?=($gps['lng']) ? $gps['lng'] : '0'?>},
      zoom: <?=($gps) ? 15 : 5?>,
      mapTypeControlOptions: {
        mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
      }
    };

    gpsmap  = new google.maps.Map(document.getElementById('propertygpsmap'), mapopt);
    setGPSMarker(mapopt.center);
    gpsmap.mapTypes.set('styled_map', styledMapType);
    gpsmap.setMapTypeId('styled_map');

    if ( !inited_gps && mapopt.center.lat == 0 ) {
      findGPSNow('<?=$prop->RegionName(false)?>', <?=($gps_term_id)?$gps_term_id:0?>);
    }

    function findGPSNow( address, term ) {
      console.log('findGPSNow - START');
      geo.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
          setGPSMarker(results[0].geometry.location);
          $.post('<?=get_ajax_url('set_regio_gps')?>', {
            lat: results[0].geometry.location.lat(),
            lng: results[0].geometry.location.lng(),
            term: term
          }, function(r){
            console.log(r);
          },"data");
        } else {
          console.log('Geocode was not successful for the following reason: ' + status);
        }
      });
    }

  })(jQuery);



  function setGPSMarker(latLng, address) {
    var circle = new google.maps.Circle({
      map: gpsmap,
      radius: 500,
      fillColor: '#B1241C',
      fillOpacity: 0.2,
      strokeColor: '#e31f24',
      strokeOpacity: 0.8,
      strokeWeight: 5,
    });
    circle.setCenter(latLng);
    gpsmap.setZoom(15);
    gpsmap.setCenter(latLng);
  }
</script>
