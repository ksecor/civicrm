<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

{if $mapProvider eq 'Google'}
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

{elseif $mapProvider eq 'Yahoo'}

<head>
<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=2.0&appid={$mapKey}"></script>
    {literal}
        <style type="text/css">
            #mapContainer { 
                            height: 600px; 
                            width: 600px; 
                          } 
           #mapContainer table {
                               border: none;
                              }         
           #mapContainer td {
                              padding: 0px;
                              vertical-align: top;
                              white-space: nowrap;
                             }
        </style> 
    {/literal}
</head>
<body>
<div id="mapContainer"></div>
{literal}
<script type="text/javascript">

  // Create a lat/lng object
   var myPoint = new YGeoPoint({/literal}{$center.lat},{$center.lng}{literal});

  // Create a map object 
  var map = new  YMap(document.getElementById('mapContainer'));
  
  // Add a pan control
   map.addPanControl();
  
  // Add a slider zoom control
   map.addZoomLong();
 
  // Display the map centered on a latitude and longitude 
  // map.drawZoomAndCenter(myPoint,13);


  function createYahooMarker(geopoint, data, img) { 

    var myImage = new YImage(); 
    myImage.src = 'http://us.i1.yimg.com/us.yimg.com/i/us/map/gr/mt_ic_cw.gif'; 
    myImage.size = new YSize(20,20); 
    myImage.offsetSmartWindow = new YCoordPoint(0,0); 
    var marker = new YMarker(geopoint,myImage); 
    //var swtext = "Marker <b> " + num + "</b>"; 
    //var label = "<img src=http://us.i1.yimg.com/us.yimg.com/i/us/ls/gr/1.gif>"; 
    var label = img;
    marker.addLabel(label); 
    YEvent.Capture(marker,EventsList.MouseClick, function() { marker.openSmartWindow(data) }); 
    return marker; 
  } 

  {/literal}
	var count=0;	
  {foreach from=$locations item=location}	
  {literal} 
     var GeoPoint = new YGeoPoint({/literal}{$location.lat},{$location.lng}{literal});

     var data = "{/literal}<a href={$location.url}>{$location.displayName}</a><br />{$location.location_type}<br />{$location.address}{literal}";
     var img  = '{/literal}{$location.contactImage}{literal}';

     var marker = createYahooMarker(GeoPoint, data, img); 
     map.addOverlay(marker); 
	count++;
  {/literal} 
  {/foreach}

	if (count>1)
	   map.drawZoomAndCenter(myPoint,13);
	else
 	   map.drawZoomAndCenter(myPoint,5);

  {literal}
</script> 
{/literal}
</body>

{/if}
<p>                                                                                                           
<div class="form-item">                     
    <p> 
    {$form.buttons.html}                                                                                      
    </p>    
</div>                            
</p>

</html>
