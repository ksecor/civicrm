{if $confirm}
<div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd><label>{$display_name} ({$email})</label> {ts}has been successfully unsubscribed.{/ts}</dd>
    </dl>
</div>
{else}
<div>
<form action="{$confirmURL}" method="post">
{ts 1=$display_name 2=$email}Are you sure you want to unsubscribe: %1 (%2){/ts}
<br/>
<center>
<input type="submit" name="_qf_unsubscribe_next" value="{ts}Unsubscribe{/ts}" class="form-submit" />
&nbsp;&nbsp;&nbsp;
<input type="submit" name="_qf_unsubscribe_cancel" value="{ts}Cancel{/ts}" class="form-submit" />
</center>
</form>
</div>
{/if}