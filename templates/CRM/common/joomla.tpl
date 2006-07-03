{* Only include joomla.css in administrator (backend). Page layout style id's and classes conflict with typical front-end css and break the page layout. *}

{if ! $config->userFrameworkFrontend}
    <link rel="stylesheet" href="{$config->resourceBase}css/joomla.css" type="text/css" />
{/if}
<link rel="stylesheet" href="{$config->resourceBase}css/civicrm.css" type="text/css" />
<link rel="stylesheet" href="{$config->resourceBase}css/skins/aqua/theme.css" type="text/css" />

<table border="0" cellpadding="0" cellspacing="0" id="content">
  <tr>
{if ! $config->userFrameworkFrontend}
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

{include file="CRM/common/footer.tpl"}

</td>

</tr>
</table>
