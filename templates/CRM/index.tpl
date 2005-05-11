{if $smarty.get.smartyDebug}
{debug}
{/if}
{if $smarty.get.sessionReset}
{$session->reset()}
{/if}
{if $smarty.get.sessionDebug}
{$session->debug($smarty.get.sessionDebug)}
{/if}

<div id="crm-container">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{include file="CRM/common/status.tpl"}

<!-- .tpl file invoked: {$tplFile} -->
{include file=$tplFile}

<div class="message status" id="feedback-request">
     <p>We are now soliciting bug reports. If you find a bug, please review the open issues
     in our <a href="http://objectledge.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel" target="_blank">bug-tracking system</a>,
     and 'Create a New Issue' if the bug isn't already in the UNRESOLVED list.
     </p>
     <p>
     Please add your comments on the look and feel of these pages along, with workflow issues on the
     <a href="http://objectledge.org/confluence/display/CRM/Demo">CiviCRM Comments Page</a>.
     </p>
</div>

</div> {* end crm-container div *}
