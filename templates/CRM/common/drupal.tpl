{if $recentlyViewed}
    {include file="CRM/common/recentlyViewed.tpl"}
{/if}

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
     {ts 1='http://objectledge.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel' 2='http://objectledge.org/confluence/display/CRM/Demo'}<p>This site is running the 1.1 Beta release. All functions should be operational.</p>
     <p>To see a list of known bugs and issues, and to report new bugs, please use our <a href="%1" target="_blank">bug-tracking system</a>.</p>
     <p>Please add any comments on the look and feel of these pages, along with workflow issues, on the <a href="%2">CiviCRM Comments Page</a>.</p>
     <p><a href="http://online.htmlvalidator.com/php/onlinevallite.php?url=referer">Validate HTML on this page using CSE HTML Validator Lite.</a>{/ts}
</div>
