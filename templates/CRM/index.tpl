{if $smarty.get.smartyDebug}
{debug}
{/if}
{if $smarty.get.sessionReset}
{$session->reset()}
{/if}
{if $smarty.get.sessionDebug}
{$session->debug($smarty.get.sessionDebug)}
{/if}

{include file="CRM/common/status.tpl"}

<div id="crm-container">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>


<!-- .tpl file invoked: {$tplFile} -->
{include file=$tplFile}

<div class="message status" id="feedback-request">
     Please add your comments on the look and feel of these pages along, with workflow issues on the
     <a href="http://objectledge.org/confluence/display/CRM/Demo">CiviCRM Comments Page</a>.
     <p>Please do not file bug reports at this time.</p>
</div>

</div> {* end crm-container div *}
