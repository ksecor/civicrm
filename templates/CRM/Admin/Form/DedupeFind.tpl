{if $no_dupes}
<div class='status'>
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>{ts}There are no duplicates matching this rule.{/ts}</dd>
  </dl>
</div>
{else}
<div class="form-item">
  <table style="width: 45%; float: left; margin: 10px;">
    <tr class="columnheader"><th colspan="2">{ts}Potentially Duplicate Contacts{/ts}</th></tr>
    {foreach from=$main_contacts item=main_name key=main_id}
      {capture assign=link}<a href="{crmURL p='civicrm/contact/view' q="reset=1&gid=$gid&cid=$main_id"}">{$main_name}</a>{/capture}
      {capture assign=select}<a href="{crmURL p='civicrm/admin/dedupefind' q="reset=1&action=update&rgid=$rgid&gid=$gid&cid=$main_id"}">{ts}select{/ts}</a>{/capture}
      {if $cid and $cid == $main_id}
        <tr class="columnheader"><td>{$main_name}</td><td style="text-align: right;">→</td></tr>
      {else}
        <tr class="{cycle values="odd-row,even-row"}"><td>{$link}</td><td style="text-align: right;">{$select}</td></tr>
      {/if}
    {/foreach}
  </table>
  {if $cid}
    <table style="width: 45%; float: left; margin: 10px;">
      <tr class="columnheader"><th colspan="2">{ts 1=$main_contacts[$cid]}Merge %1 with{/ts}</th></tr>
      {foreach from=$dupe_contacts[$cid] item=dupe_name key=dupe_id}
        {capture assign=link}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{$dupe_name}</a>{/capture}
        {capture assign=merge}<a href="{crmURL p='civicrm/contact/merge' q="reset=1&cid=$cid&oid=$dupe_id"}">{ts}merge{/ts}</a>{/capture}
        <tr class="{cycle values="odd-row,even-row"}"><td>{$link}</td><td style="text-align: right">{$merge}</td></tr>
      {/foreach}
    </table>
  {/if}
</div>
<div style="clear: both;"></div>
{/if}
