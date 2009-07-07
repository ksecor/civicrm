{if $smarty.get.snippet eq 2}
{include file="CRM/common/print.tpl"}
{else}
<div id="crm-container-snippet" bgColor="white">

{* Check for Status message for the page (stored in session->getStatus). Status is cleared on retrieval. *}
{if $session->getStatus(false)}
<div class="messages status">
  <dl>
  <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
  <dd>{$session->getStatus(true)}</dd>
  </dl>
</div>
{/if}

<!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
{if $isForm}
    {include file="CRM/Form/default.tpl"}
{else}
    {include file=$tplFile}
{/if}


</div> {* end crm-container-snippet div *}
{/if}