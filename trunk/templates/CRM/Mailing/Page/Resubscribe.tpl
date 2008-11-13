{if $confirm}
<div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd><label>{$display_name} ({$email})</label> {ts}has been successfully resubscribed.{/ts}</dd>
    </dl>
</div>
{else}
<div>
<form action="{$confirmURL}" method="post">
{ts 1=$display_name 2=$email}Are you sure you want to resubscribe: %1 (%2){/ts}
<br/>
<center>
<input type="submit" name="_qf_resubscribe_next" value="{ts}Resubscribe{/ts}" class="form-submit" />
&nbsp;&nbsp;&nbsp;
<input type="submit" name="_qf_resubscribe_cancel" value="{ts}Cancel{/ts}" class="form-submit" />
</center>
</form>
</div>
{/if}