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
{include file=$tplFile}
</div>