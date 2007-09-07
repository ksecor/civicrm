<div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
{if $result}
      <dd><label>{$display_name} ({$email})</label> {ts}has been successfully subscribed to{/ts} {$group}.</dd>
{/if}
    </dl>
</div>
