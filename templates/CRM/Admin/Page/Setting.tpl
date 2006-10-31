{include file="CRM/common/dojo.tpl"}
<div id="mainTabContainer" dojoType="TabContainer" style="width: 100%; height: 600px" selectedTab="Components">

{foreach from=$allTabs key=tabName item=tabURL}
  <div id="{$tabName}" dojoType="ContentPane" href="{$tabURL}" label="{$tabName}" style="display: none" adjustPaths="false"></div>
{/foreach}
</div>
