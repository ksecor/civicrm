 <head>
  <script src="http://maps.google.com/maps?file=api&v=2&key={$mapKey}" type="text/javascript"></script>
  {literal}
  <script type="text/javascript">
    function onLoad() {

      //<![CDATA[
      var map     = new GMap2(document.getElementById("map"));
      var span    = new GSize({/literal}{$span.lng},{$span.lat}{literal});
      var center  = new GLatLng({/literal}{$center.lat},{$center.lng}{literal});

      var geocoder = new GClientGeocoder();

      var oldZoom = 13;
      var newZoom = 17 - oldZoom; //Relation between zoom levels of v1 and v2 

      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());

      // Creates a marker whose info window displays the given number
      function createMarker(point, data) {
        var marker = new GMarker(point);

        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(data);
        });

        return marker;
      }
	function showAddress(address, data) {
	  geocoder.getLatLng(
	    address,
	    function(point) {
	      if (!point) {
	        alert(address + " not found");
	      } else {
	        map.setCenter(point, newZoom);
	        var marker = createMarker(point, data);
	        map.addOverlay(marker);
	      }
	    }
	  );
	}
      {/literal}
      {foreach from=$locations item=location}
      {literal} 

	 var data = "{/literal}<a href={$location.url}>{$location.displayName}</a><br />{$location.location_type}<br />{$location.address}{literal}";
	 var address = "{/literal}{$location.address}{literal}";
	 var geoCodeEnabled = {/literal}{$enableGeoCoding}{literal};

	 if (geoCodeEnabled) {
		 showAddress("{/literal}{$location.geoCodeAddress}{literal}", data); 
	 } else {
         	var point = new GLatLng({/literal}{$location.lat},{$location.lng}{literal});
		map.setCenter(center, newZoom);
         	var marker = createMarker(point, data);
	        map.addOverlay(marker);
	 }

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
