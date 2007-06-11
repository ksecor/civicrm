<div class="form-item">
  {foreach from=$main_contacts item=main_name key=main_id}
  <table style="width: 45%; float: left; margin: 10px;">
    {capture assign=view}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_id"}">{ts}view{/ts}</a>{/capture}
    <tr class="columnheader"><th>{ts 1=$main_name}Merge %1 with{/ts}</th><th style="text-align: right">{$view}</th></tr>
    {foreach from=$dupe_contacts[$main_id] item=dupe_name key=dupe_id}
      {capture assign=view}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{ts}view{/ts}</a>{/capture}
      {capture assign=pipe}{$main_id}|{$dupe_id}{/capture}
      <tr class="{cycle values="odd-row,even-row"}"><td>{$form.merge_ids.$pipe.html} {$form.merge_ids.$pipe.label}</td><td style="text-align: right">{$view}</td></tr>
    {/foreach}
    <tr class="columnheader"><th colspan="2">{$form.buttons.html}</th></tr>
  </table>
  {/foreach}
</div>
<div style="clear: both;"></div>
