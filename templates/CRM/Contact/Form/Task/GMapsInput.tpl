{strip}
{literal}<?xml version="1.0" ?>{/literal}
<page>
<title>{$title}</title>
<query>{$query}</query>
<center lat="{$center.lat}" lng="{$center.lng}"/>
<span lat="{$span.lat}" lng="{$span.lng}"/>
<overlay panelStyle="{$panelStyle}">
{foreach from=$locations key=id item=location}
<location id="location_{$id}" infoStyle="/maps?file=gi&amp;hl=en">
  <point lat="{$location.lat}" lng="{$location.lng}"/>
  <icon class="local" image="http://maps.google.com/mapfiles/marker.png"/>
  <info>
     <address>
       <line>{$location.displayName}</line>
       <line>{$location.address}</line>
     </address>
  </info>
</location>
{/foreach}
</overlay>
</page>
{/strip}