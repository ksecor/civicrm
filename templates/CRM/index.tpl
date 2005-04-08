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
<script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>

{* Check for Status message for the page (stored in session->getStatus). Status is cleared on retrieval. *}
{if $session->getStatus(false)}
<div class="message status">
  <img src="crm/i/inform.gif" alt="status"> &nbsp; {$session->getStatus(true)}
</div>
{/if}

{include file=$tplFile}
</div>