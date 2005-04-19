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

{* Check for Status message for the page (stored in session->getStatus). Status is cleared on retrieval. *}
{if $session->getStatus(false)}
<div class="message status">
  <img src="{$config->resourceBase}i/inform.gif" alt="status"> &nbsp; {$session->getStatus(true)}
</div>
{/if}

{include file=$tplFile}

<div class="message status">
  <img src="{$config->resourceBase}i/inform.gif" alt="status"> &nbsp; Please add your comments on the look and feel of these pages along with workflow issues on the <a href="http://objectledge.org/confluence/display/CRM/Demo">CiviCRM Comments Page</a>.
<p>
Please do not file bug reports at this time.
</div>

</div>