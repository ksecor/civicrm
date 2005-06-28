{include file="CRM/common/status.tpl"}

<!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
{if $isForm}
    {include file="CRM/form.tpl"}
{else}
    {include file=$tplFile}
{/if}

<div class="message status" id="feedback-request">
     {ts 1='http://objectledge.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel' 2='http://objectledge.org/confluence/display/CRM/Demo'}
     <p>This site is running our v1.0 beta codebase. We strongly encourage visitors to try out the full range of available functionality - all features should be working.</p>
     <p>If you find a bug, please review the open issues in our <a href="%1" target="_blank">bug-tracking system</a>, and 'Create a New Issue' if the bug isn't already in the UNRESOLVED list. You can submit bugs anonymously - but we encourage you to create an account so we can contact you if there are questions about your bug submissions.</p>
     <p>Please add any comments on the look and feel of these pages, along with workflow issues, on the <a href="%1">CiviCRM Comments Page</a>.</p>
     {/ts}
</div>
