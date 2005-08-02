<!--iframe src="{$config->resourceBase}gmaps/index.html?url={$xmlURL}"
    width="900" height="600" scrolling="no" marginwidth="0"	
    marginheight="0" frameborder="0" /-->	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <script src="http://maps.google.com/maps?file=api&v=1&key={$googleMapKey}" type="text/javascript"></script>
  {literal}
  <script type="text/javascript">
    function onLoad() {

      //<![CDATA[
      var map = new GMap(document.getElementById("map"));
      map.addControl(new GSmallMapControl());
      map.addControl(new GMapTypeControl());
      map.centerAndZoom(new GPoint({/literal}{$center.lng},{$center.lat}{literal}), 4);

      // Creates a marker whose info window displays the given number
      function createMarker(point) {
        var marker = new GMarker(point);

        // Show this marker's index in the info window when it is clicked
        var html = "{/literal}{$location.displayName}<br>{$location.address}{literal}";
  
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });

        return marker;
      }

      var point = new GPoint({/literal}{$center.lng},{$center.lat}{literal});
      var marker = createMarker(point);
      map.addOverlay(marker);

     //]]>  
   }
  </script>

{/literal}
  </head>
  <body onload="onLoad()"; >
    <div id="map" style="width: 600px; height: 400px"></div>
  </body>
</html>
