<? global $me; ?>
<div class="map-container gps-map">
  <div id="propertygpsmap" style="width: 100%; height: 320px;"></div>
  <div class="map-gps-selected">
    <span class="lat <?=($gps['lat']) ? 'setted' : ''?>" id="gpsmap_txt_lat"><?=($gps['lat']) ? $gps['lat'] : '00.00000'?></span>
    <span class="sep">,</span>
    <span class="lng <?=($gps['lng']) ? 'setted' : ''?>" id="gpsmap_txt_lng"><?=($gps['lng']) ? $gps['lng'] : '00.00000'?></span>
    <div class="selected-address" id="gpsmap_txt_address"></div>
  </div>
  <input type="hidden" id="gpsmap_lat" name="meta_input[_listing_gps_lat]" value="<?=$gps['lat']?>">
  <input type="hidden" id="gpsmap_lng" name="meta_input[_listing_gps_lng]" value="<?=$gps['lng']?>">
  <input type="hidden" name="pre[meta_input][_listing_gps_lat]" value="<?=$gps['lat']?>">
  <input type="hidden" name="pre[meta_input][_listing_gps_lng]" value="<?=$gps['lng']?>">
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
      center: {lat: <?=($gps['lat']) ? $gps['lat'] : '46.075493'?>, lng: <?=($gps['lng']) ? $gps['lng'] : '18.228361'?>},
      zoom: <?=($gps) ? 14 : 12?>,
      mapTypeControlOptions: {
        mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
      }
    };

    gpsmap  = new google.maps.Map(document.getElementById('propertygpsmap'), mapopt);

    if (inited_gps == 0) {
      geo.geocode({ address: 'Magyarország <?=($me->RegionID())?', '.$me->RegionName().' megye':''?>'}, function(r,s){
        if (s == 'OK') {
          gpsmap.setCenter(r[0].geometry.location);
          gpsmap.setZoom(10);
        }
      });
    } else {
      setGPSMarker(mapopt.center);
    }

    gpsmap.addListener('click', function(e,r) {
      setGPSMarker({
        lat: parseFloat(e.latLng.lat().toFixed(5)),
        lng: parseFloat(e.latLng.lng().toFixed(5))
      });
    });

    gpsmap.mapTypes.set('styled_map', styledMapType);
    gpsmap.setMapTypeId('styled_map');

  })(jQuery);

  function setGPSMarker(latLng, address) {
    if(pMarker) {
      pMarker.setPosition(latLng);
    } else {
      pMarker = new google.maps.Marker({
          position: latLng,
          draggable: true
      });
      pMarker.setMap(gpsmap);
      google.maps.event.addListener(pMarker, 'dragend', function(evt){
          //console.log(evt);
          jQuery('#gpsmap_lat').val(evt.latLng.lat().toFixed(5));
          jQuery('#gpsmap_txt_lat').text(evt.latLng.lat().toFixed(5)).addClass('setted');
          jQuery('#gpsmap_lng').val(evt.latLng.lng().toFixed(5));
          jQuery('#gpsmap_txt_lng').text(evt.latLng.lng().toFixed(5)).addClass('setted');
      });
    }

    jQuery('#gpsmap_lat').val(latLng.lat);
    jQuery('#gpsmap_txt_lat').text(latLng.lat).addClass('setted');
    jQuery('#gpsmap_lng').val(latLng.lng);
    jQuery('#gpsmap_txt_lng').text(latLng.lng).addClass('setted');

    gpsmap.setZoom(14);
    gpsmap.setCenter(latLng);

    jQuery('#gpsmap_txt_address').text(address);
  }
</script>
