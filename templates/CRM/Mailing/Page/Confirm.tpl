<div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
{if $success}
      <dd>{ts 1=$display_name 2=$email 3=$group}<strong>%1 (%2)</strong> has been successfully subscribed to the <strong>%3</strong> mailing list.{/ts}</dd>
{else}
      <dd>{ts}Oops. We encountered a problem in processing your subscription confirmation. Please contact the site administrator.{/ts}</dd>
{/if}
    </dl>
</div>
