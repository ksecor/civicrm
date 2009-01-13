<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$config->lcMessages|truncate:2:"":true}">
 <head>
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

{if $config->customCSSURL}
<link rel="stylesheet" href="{$config->customCSSURL}" type="text/css" />
{else}
<link rel="stylesheet" href="{$config->resourceBase}css/civicrm.css" type="text/css" />
{/if}
<link rel="stylesheet" href="{$config->resourceBase}css/standalone.css" type="text/css" />
<link rel="stylesheet" href="{$config->resourceBase}css/skins/aqua/theme.css" type="text/css" />
<script type="text/javascript" src="{$config->resourceBase}packages/dojo/dojo/dojo.js" djConfig="isDebug: false, parseOnLoad: true, usePlainJson: true"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/dojo/dojo/commonWidgets.js"></script>
<style type="text/css">@import url({$config->resourceBase}packages/dojo/dijit/themes/tundra/tundra.css);</style>
<script type="text/javascript" src="{$config->resourceBase}js/calendar.js"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/lang/calendar-lang.php?{$config->lcMessages}"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/calendar-setup.js"></script> 

{$pageHTMLHead}
{include file="CRM/common/dojo.tpl"}
{include file="CRM/common/jquery.tpl"}
  <title>{$pageTitle}</title>
</head>
<body>

{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

<table border="0" cellpadding="0" cellspacing="0" id="content">
  <tr>
    {if $sidebarLeft}
    <td id="sidebar-left" valign="top">
         {$sidebarLeft}
    </td>
    {/if}
    <td id="main-content" valign="top">
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
      {if $displayRecent and $recentlyViewed}
        {include file="CRM/common/recentlyViewed.tpl"}
      {/if}
    
      <h1 class="title">{$pageTitle}</h1>
    
      {if $localTasks}
        {include file="CRM/common/localNav.tpl"}
      {/if}

      {include file="CRM/common/status.tpl"}

      <!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
      <br clear="both"/>
      {if $isForm}
        {include file="CRM/Form/$formTpl.tpl"}
      {else}
        {include file=$tplFile}
      {/if}

      {include file="CRM/common/footer.tpl"}
    </td>

  </tr>
</table>

{* We need to set jquery $ object back to $*}
<script type="text/javascript">jQuery.noConflict(true);</script>

</div> {* end crm-container div *}
</body>
</html>
