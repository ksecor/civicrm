{if $smarty.get.smartyDebug}
{debug}
{/if}
{if $smarty.get.sessionReset}
{$session->reset()}
{/if}
{if $smarty.get.sessionDebug}
{$session->debug()}
{/if}
<div id="crm-container">
<script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>

{if $session->getStatus(false)}
<div class="message status">
  {$session->getStatus(true)}
</div>
{/if}

{include file=$tplFile}
</div>