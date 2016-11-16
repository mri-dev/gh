<? global $me; ?>
<div class="map-container gps-map">
  <div id="propertygpsmap" style="width: 100%; height: 320px;"></div>
  <div class="map-gps-selected">
    <span class="lat" id="gpsmap_txt_lat">00.00000</span>
    <span class="sep">,</span>
    <span class="lng" id="gpsmap_txt_lng">00.00000</span>
  </div>
  <input type="hidden" id="gpsmap_lat" name="meta_input[_listing_gps_lat]" value="">
  <input type="hidden" id="gpsmap_lng" name="meta_input[_listing_gps_lng]" value="">
</div>

<script type="text/javascript">
  var gpsmap, geo, pMarker;
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
      center: {lat: 46.075493, lng: 18.228361},
      zoom: 12,
      mapTypeControlOptions: {
        mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
      }
    };

    gpsmap  = new google.maps.Map(document.getElementById('propertygpsmap'), mapopt);

    geo.geocode({ address: 'Magyarország <?=($me->RegionID())?', '.$me->RegionName().' megye':''?>'}, function(r,s){
      if (s == 'OK') {
        gpsmap.setCenter(r[0].geometry.location);
        gpsmap.setZoom(10);
      }
    });

    gpsmap.addListener('click', function(e) {
      if( !pMarker ) {
        pMarker = new google.maps.Marker({
            position: e.latLng,
            draggable: true
        });
        $('#gpsmap_lat').val(e.latLng.lat().toFixed(5));
        $('#gpsmap_txt_lat').text(e.latLng.lat().toFixed(5)).addClass('setted');
        $('#gpsmap_lng').val(e.latLng.lng().toFixed(5));
        $('#gpsmap_txt_lng').text(e.latLng.lng().toFixed(5)).addClass('setted');

        google.maps.event.addListener(pMarker, 'dragend', function(evt){
            //console.log(evt);
            $('#gpsmap_lat').val(evt.latLng.lat().toFixed(5));
            $('#gpsmap_txt_lat').text(evt.latLng.lat().toFixed(5)).addClass('setted');
            $('#gpsmap_lng').val(evt.latLng.lng().toFixed(5));
            $('#gpsmap_txt_lng').text(evt.latLng.lng().toFixed(5)).addClass('setted');
        });
        pMarker.setMap(gpsmap);
      }
    });

    gpsmap.mapTypes.set('styled_map', styledMapType);
    gpsmap.setMapTypeId('styled_map');

  })(jQuery);
</script>
