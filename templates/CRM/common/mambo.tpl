<link rel="stylesheet" href="components/com_civicrm/civicrm/css/drupal.css" type="text/css" />
<link rel="stylesheet" href="components/com_civicrm/civicrm/css/bluemarine.css" type="text/css" />
<link rel="stylesheet" href="components/com_civicrm/civicrm/css/civicrm.css" type="text/css" />

<table border="0" cellpadding="0" cellspacing="0" id="content">
  <tr>
    <td id="sidebar-left" valign="top">
       {$sidebarLeft}
    </td>
    <td valign="top">
    <div class="breadcrumb"><a href="">Home</a> &raquo; <a href="">CiviCRM</a> &raquo; <a href="">Search Results</a></div>
    {if $recentlyViewed}
        {include file="CRM/common/recentlyViewed.tpl"}
    {/if}
    
    <h1 class="title">Page Title Goes Here</h1>
    
    {if $localTasks}
        {include file="CRM/common/localNav.tpl"}
    {/if}

    {include file="CRM/common/status.tpl"}

    <!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
    {if $isForm}
        {include file="CRM/form.tpl"}
    {else}
        {include file=$tplFile}
    {/if}

<div class="messages status" id="feedback-request">
     {ts 1='http://objectledge.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel' 2='http://demo.openngo.org/civicrm' 3='http://objectledge.org/confluence/display/CRM/Demo'}<p>This sandbox site is running code in active development towards our 1.1 release. Some functions may not be fully operational. Check out our <a href="%2" target="_blank">Demo site</a> if you'd like to try out our latest stable release.</p>
     <p>To see a list of features and fixes under development, please review the open issues in our <a href="%1" target="_blank">bug-tracking system</a>.</p>
     <p>Please add any comments on the look and feel of these pages, along with workflow issues, on the <a href="%3">CiviCRM Comments Page</a>.</p>{/ts}
</div>

</td>

</tr>
</table>
