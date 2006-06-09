 <head>
  <script src="http://maps.google.com/maps?file=api&v=1&key={$mapKey}" type="text/javascript"></script>
  {literal}
  <script type="text/javascript">
    function onLoad() {

      //<![CDATA[
      var map    = new GMap(document.getElementById("map"));
      var spec   = map.spec;
      var span   = new GSize({/literal}{$span.lng},{$span.lat}{literal});
      var center = new GPoint({/literal}{$center.lng},{$center.lat}{literal});
      var zoom   = spec.getLowestZoomLevel(center, span, map.viewSize);
      
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.centerAndZoom(center, zoom);
      
      // Creates a marker whose info window displays the given number
      function createMarker(point, data) {
        var marker = new GMarker(point);

        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(data);
        });

        return marker;
      }

      {/literal}
      {foreach from=$locations item=location}
      {literal} 
         var point = new GPoint({/literal}{$location.lng},{$location.lat}{literal});

	 var data = "{/literal}<a href={$location.url}>{$location.displayName}</a><br />{$location.location_type}<br />{$location.address}{literal}";
         
         var marker = createMarker(point, data);
         map.addOverlay(marker);

      {/literal} 
      {/foreach}
      {literal}

     //]]>  
   }
  </script>

{/literal}
  </head>
  <body onload="onLoad()"; >
    <div id="map" style="width: 600px; height: 400px"></div>
  </body>
