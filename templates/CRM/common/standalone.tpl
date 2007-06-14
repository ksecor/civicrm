<link rel="stylesheet" href="{$config->resourceBase}css/civicrm.css" type="text/css" />
<link rel="stylesheet" href="{$config->resourceBase}css/skins/aqua/theme.css" type="text/css" />
<script type="text/javascript" src="{$config->resourceBase}packages/dojo/dojo.js"></script>
<script type="text/javascript" src="{$config->resourceBase}js/calendar.js"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/lang/calendar-lang.php"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/calendar-setup.js"></script>

{include file="CRM/common/dojo.tpl"}


<div id="crm-content">
  {if $sidebarLeft}
    <div id="crm-sidebar-left" style="float: left">
         {$sidebarLeft}
    </div>
  {/if}
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
  {if $isForm}
    {include file="CRM/Form/$formTpl.tpl"}
  {else}
    {include file=$tplFile}
  {/if}

  <div style="clear: both">
    {include file="CRM/common/footer.tpl"}
  </div>

</div>
