{include file="CRM/common/dojo.tpl"}
<div id="mainTabContainer" dojoType="TabContainer" style="width: 100%; height: 600px" selectedTab="Components">
   <div class="action-link"> 
       <a href="{crmURL p="civicrm/admin/setting/component?reset=1"}" id="component">&raquo; {ts}Components{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/path?reset=1'}" id="path">&raquo; {ts}File System Paths{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/site?reset=1'}" id="site">&raquo; {ts}Site URLs{/ts}</a><br />

        <a href="{crmURL p='civicrm/admin/setting/smtp?reset=1'}" id="site">&raquo; {ts}SMTP Server{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/mapping?reset=1'}" id="map">&raquo; {ts}Mapping and Geocoding{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/payment?reset=1'}" id="pay">&raquo; {ts}Online Payments{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/localisation?reset=1'}" id="localisation">&raquo; {ts}Localisation{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/address?reset=1'}" id="address">&raquo; {ts}Address Formatting{/ts}</a><br />
 
       <a href="{crmURL p='civicrm/admin/setting/date?reset=1'}" id="date">&raquo; {ts}Date Formatting{/ts}</a><br />

       <a href="{crmURL p='civicrm/admin/setting/misc?reset=1'}" id="misc">&raquo; {ts}Miscellaneous{/ts}</a><br />
 
       <a href="{crmURL p='civicrm/admin/setting/debug?reset=1'}" id="debug">&raquo; {ts}Debugging{/ts}</a>
   </div>

{foreach from=$allTabs key=tabName item=tabURL}
  <div id="{$tabName}" dojoType="ContentPane" href="{$tabURL}" label="{$tabName}" style="display: none" adjustPaths="false"></div>
{/foreach}
</div>
