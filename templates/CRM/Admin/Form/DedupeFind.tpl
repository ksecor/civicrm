<div class="form-item">
  {foreach from=$main_contacts item=main_name key=main_id}
  <table style="width: 45%; float: left; margin: 10px;">
    {capture assign=link}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_id"}">{$main_name}</a>{/capture}
    <tr class="columnheader"><th colspan="2">{ts 1=$link}Merge %1 with{/ts}</th></tr>
    {foreach from=$dupe_contacts[$main_id] item=dupe_name key=dupe_id}
      {capture assign=link}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{$dupe_name}</a>{/capture}
      {capture assign=merge}<a href="{crmURL p='civicrm/contact/merge' q="reset=1&cid=$main_id&oid=$dupe_id"}">{ts}merge{/ts}</a>{/capture}
      <tr class="{cycle values="odd-row,even-row"}"><td>{$link}</td><td style="text-align: right">{$merge}</td></tr>
    {/foreach}
  </table>
  {/foreach}
</div>
<div style="clear: both;"></div>
