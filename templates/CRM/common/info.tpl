{* Handles display of passed $infoMessage. *}
{if $infoMessage}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
    <dd>{$infoMessage}</dd>
  </dl>
</div>
{/if}