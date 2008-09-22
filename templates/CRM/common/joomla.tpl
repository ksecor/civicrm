{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{* Only include joomla.css in administrator (backend). Page layout style id's and classes conflict with typical front-end css and break the page layout. *}

{if ! $config->userFrameworkFrontend}
    <link rel="stylesheet" href="{$config->resourceBase}css/joomla.css" type="text/css" />
{/if}
{if $config->customCSSURL}
<link rel="stylesheet" href="{$config->customCSSURL}" type="text/css" />
{else}
<link rel="stylesheet" href="{$config->resourceBase}css/civicrm.css" type="text/css" />
{/if}
<link rel="stylesheet" href="{$config->resourceBase}css/skins/aqua/theme.css" type="text/css" />
<script type="text/javascript" src="{$config->resourceBase}packages/dojo/dojo/dojo.js" djConfig="isDebug: false, parseOnLoad: true, usePlainJson: true"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/dojo/dojo/commonWidgets.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/dojo/dijit/themes/tundra/tundra.css);</style>
<script type="text/javascript" src="{$config->resourceBase}js/calendar.js"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/lang/calendar-lang.php?{$config->lcMessages}"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/calendar-setup.js"></script> 

{include file="CRM/common/dojo.tpl"}

<table border="0" cellpadding="0" cellspacing="0" id="content">
  <tr>
{if $sidebarLeft}
    <td id="sidebar-left" valign="top">
       {$sidebarLeft}
    </td>
{/if}
    <td valign="top">
    {if $breadcrumb}
    <div class="breadcrumb">
      {foreach from=$breadcrumb item=crumb key=key}
        {if $key != 0}
           &raquo;
        {/if}
        <a href="{$crumb.url}">{$crumb.title}</a>
      {/foreach}
    </div>
    {/if}

    {if $pageTitle}
        <h1 class="title">{$pageTitle}</h1>
    {/if}

    {if $displayRecent and $recentlyViewed}
        {include file="CRM/common/recentlyViewed.tpl"}
    {/if}
   
    
{if $browserPrint}
{* Javascript window.print link. Used for public pages where we can't do printer-friendly view. *}
<div id="printer-friendly"><a href="javascript:window.print()" title="{ts}Print this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Print this page.{/ts}" /></a></div>
{else}
{* Printer friendly link/icon. *}
<div id="printer-friendly"><a href="{$printerFriendly}" title="{ts}Printer-friendly view of this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Printer-friendly view of this page.{/ts}" /></a></div>
{/if}

{*{include file="CRM/common/langSwitch.tpl"}*}

    <div class="spacer"></div>

    {if $localTasks}
        {include file="CRM/common/localNav.tpl"}
    {/if}

    {include file="CRM/common/status.tpl"}

    <!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
    {if $isForm}
        {include file="CRM/Form/$formTpl.tpl"}
    {else}
        {include file=$tplFile}
    {/if}

    {include file="CRM/common/footer.tpl"}

    </td>

  </tr>
</table>
</div> {* end crm-container div *}
