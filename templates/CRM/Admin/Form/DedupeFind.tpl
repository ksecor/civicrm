<div class="form-item">
  {foreach from=$main_contacts item=main_name key=main_id}
  <table style="width: 45%; float: left; margin: 10px;">
    {capture assign=main}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_id"}">{$main_name}</a>{/capture}
    <tr class="columnheader"><th>{ts 1=$main}Merge %1 with{/ts}</th></tr>
    {foreach from=$dupe_contacts[$main_id] item=dupe_name key=dupe_id}
      {capture assign=dupe}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{$dupe_name}</a>{/capture}
      <tr class="{cycle values="odd-row,even-row"}"><td><input type="radio" name="radio" value="{$main_id}|{$dupe_id}" /> {$dupe}</td></tr>
    {/foreach}
    <tr class="columnheader"><th>{$form.buttons.html}</th></tr>
  </table>
  {/foreach}
</div>
<div style="clear: both;"></div>
