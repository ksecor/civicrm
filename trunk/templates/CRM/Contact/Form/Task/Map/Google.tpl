<script src="http://maps.google.com/maps?file=api&v=2&key={$mapKey}" type="text/javascript"></script>
  {literal}
  <script type="text/javascript">
    function initMap() {

      //<![CDATA[
      var map     = new GMap2(document.getElementById("google_map"));
      var span    = new GSize({/literal}{$span.lng},{$span.lat}{literal});
      var center  = new GLatLng({/literal}{$center.lat},{$center.lng}{literal});

      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng( 0, 0 ), 0 );
      var bounds = new GLatLngBounds( );

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
{if $location.url and ! $profileGID}
      {literal}
	 var data = "{/literal}<a href='{$location.url}'>{$location.displayName}</a><br />{$location.location_type}<br />{$location.address}<br /><br />Get Directions FROM:&nbsp;<input type=text id=to size=20>&nbsp;<a href=\"javascript:gpopUp();\">&raquo; Go</a>";
{else}
      {capture assign="profileURL"}{crmURL p='civicrm/profile/view' q="reset=1&id=`$location.contactID`&gid=$profileGID"}{/capture}
      {literal}
	 var data = "{/literal}<a href='{$profileURL}'>{$location.displayName}</a><br />{$location.location_type}<br />{$location.address}<br /><br />Get Directions FROM:&nbsp;<input type=text id=to size=20>&nbsp;<a href=\"javascript:gpopUp();\">&raquo; Go</a>";
{/if}
{literal}
	 var address = "{/literal}{$location.address}{literal}";
{/literal}
{if $location.lat}
       	var point = new GLatLng({$location.lat},{$location.lng});
       	var marker = createMarker(point, data);
        map.addOverlay(marker);
        bounds.extend(point);
{/if}
      {/foreach}
      {if count($locations) gt 1}  
      	  map.setZoom(5);
      {else}
	  map.setZoom(15);
      {/if}
      map.setCenter(bounds.getCenter());
      {literal}

     //]]>  
   }

    function gpopUp() {
       {/literal}
       var to = "{$location.displayAddress}";
       {literal}
       var from   = document.getElementById('to').value;
       var URL  = "http://maps.google.com/maps?saddr=" + from + "&daddr=" + to;
       day = new Date();
       id = day.getTime();
       eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=780,height=640,left = 202,top = 100');");
    }

    if (window.addEventListener) {
        window.addEventListener("load", initMap, false);
    } else if (window.attachEvent) {
        document.attachEvent("onreadystatechange", initMap);
    }

  </script>
{/literal}

  <div id="google_map" style="width: 600px; height: 400px"></div>
