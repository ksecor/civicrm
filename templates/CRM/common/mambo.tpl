<link rel="stylesheet" href="components/com_civicrm/civicrm/css/mambo.css" type="text/css" />
<link rel="stylesheet" href="components/com_civicrm/civicrm/css/civicrm.css" type="text/css" />

<table border="0" cellpadding="0" cellspacing="0" id="content">
  <tr>
{if ! $config->userFrameworkFrontend}
    <td id="sidebar-left" valign="top">
       {$sidebarLeft}
    </td>
{/if}
    <td valign="top">
    <div class="breadcrumb">{$pageCrumb}</div>
    {if $displayRecent and $recentlyViewed}
        {include file="CRM/common/recentlyViewed.tpl"}
    {/if}
    
    <h1 class="title">{$pageTitle}</h1>
    
    {if $localTasks}
        {include file="CRM/common/localNav.tpl"}
    {/if}

    {include file="CRM/common/status.tpl"}

    <!-- .tlp file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
    {if $isForm}
        {include file="CRM/form.tpl"}
    {else}
        {include file=$tplFile}
    {/if}

{include file="CRM/common/feedback.tpl"}

</td>

</tr>
</table>
