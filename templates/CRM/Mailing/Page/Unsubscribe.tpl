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
      {if $groupExist}
        <div class="messages status">
          {ts 1=$display_name 2=$email} %1 (%2){/ts}<br/>
          {ts}Are you sure you want to be unsubscribed from mailing lists:{/ts}<br/>
        </div>
            <table class="selector" style="width: auto;">
                {counter start=0 skip=1 print=false}
                {foreach from=$groups item=group}
                <tr class="{cycle values="odd-row,even-row"}">
                 <td><strong>{$group.title}</strong></td>
                 <td>&nbsp;&nbsp;{$group.description}&nbsp;</td>
                </tr>
                {/foreach}  
            </table>
        <center>
          <input type="submit" name="_qf_unsubscribe_next" value="{ts}Unsubscribe{/ts}" class="form-submit" />&nbsp;&nbsp;&nbsp;
           <input type="submit" name="_qf_unsubscribe_cancel" value="{ts}Cancel{/ts}" class="form-submit" />
        </center>
      {else}
        <div class="messages status">
          {ts 1=$display_name 2=$email} %1 (%2){/ts}<br/>
          {ts}Sorry you are not on the mailing list. Probably you are already unsubscribed.{/ts}<br/>
        </div>
      {/if}
    </form>
</div>
{/if}