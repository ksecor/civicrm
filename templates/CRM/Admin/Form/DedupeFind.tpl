<div class="form-item">
  {foreach from=$found_dupes item=dupes key=main_id}
  <table style="width: 45%; float: left; margin: 10px;">
    {capture assign=main}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_id"}">{$display_names[$main_id]}</a>{/capture}
    <tr class="columnheader"><th colspan="2">{ts 1=$main}Merge %1 with{/ts}</th></tr>
    {foreach from=$dupes item=dupe_id}
      {capture assign=dupe}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{$display_names[$dupe_id]}</a>{/capture}
      <tr class="{cycle values="odd-row,even-row"}"><td><input type="radio" name="radio" value='{$main.id}_{$dupe_id}' /></td><td>{$dupe}</td></tr>
    {/foreach}
    <tr class="columnheader"><th colspan="2">{$form.buttons.html}</th></tr>
  </table>
  {/foreach}
</div>
<div style="clear: both;"></div>
