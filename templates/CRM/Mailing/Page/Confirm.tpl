<div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
{if $success}
      <dd><label>{$display_name} ({$email})</label> {ts}has been successfully subscribed to{/ts} {$group}.</dd>
{else}
      <dd>{ts}We encountered a problem in processing your subscription. Please contact the site administrator{/ts}.</dd>
{/if}
    </dl>
</div>
