{if $action neq 2}
{include file="CRM/Admin/Form/DedupeFind.tpl"}
{else}
<div class="form-item">
  <table>
    <tr class="columnheader"><th>{ts}Contact{/ts} 1</th><th>{ts}Contact{/ts} 2 ({ts}Duplicate{/ts})</th><th>{ts}Threshold{/ts}</th><th>&nbsp;</th></tr>
    {foreach from=$main_contacts item=main key=main_id}
        {capture assign=srcLink}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$main.srcID`"}">{$main.srcName}</a>{/capture}
        {capture assign=dstLink}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$main.dstID`"}">{$main.dstName}</a>{/capture}
        {capture assign=merge}<a href="{crmURL p='civicrm/contact/merge' q="reset=1&cid=`$main.srcID`&oid=`$main.dstID`"}">{ts}merge{/ts}</a>{/capture}
        <tr class="{cycle values="odd-row,even-row"}">
          <td>{$srcLink}</td>
          <td>{$dstLink}</td>
          <td>{$main.weight}</td>
          <td style="text-align: right;">{$merge}</td>
        </tr>
    {/foreach}
  </table>
  {if $cid}
    <table style="width: 45%; float: left; margin: 10px;">
      <tr class="columnheader"><th colspan="2">{ts 1=$main_contacts[$cid]}Merge %1 with{/ts}</th></tr>
      {foreach from=$dupe_contacts[$cid] item=dupe_name key=dupe_id}
        {if $dupe_name}
          {capture assign=link}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{$dupe_name}</a>{/capture}
          {capture assign=merge}<a href="{crmURL p='civicrm/contact/merge' q="reset=1&cid=$cid&oid=$dupe_id"}">{ts}merge{/ts}</a>{/capture}
          <tr class="{cycle values="odd-row,even-row"}"><td>{$link}</td><td style="text-align: right">{$merge}</td></tr>
        {/if}
      {/foreach}
    </table>
  {/if}
</div>
<div style="clear: both;"></div>

{/if}
