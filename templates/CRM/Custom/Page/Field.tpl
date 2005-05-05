{include file="CRM/Custom/Form/Field.tpl"}

{if $rows}
<div id="notes">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
          <th>Field Label</th>
          <th>Data Type</th>
          <th>HTML Type</th>
          <th>Default Value</th>
	  <th>Required</th>
          <th>Status</th>
          <th></th>
       </tr>
{foreach from=$rows item=row}
       <tr class="{cycle values="odd-row,even-row"}">
         <td>{$row.label}</td>
         <td>{$row.data_type}</td>
         <td>{$row.html_type}</td>
         <td>{$row.is_required}</td>
         <td>{$row.is_active}</td>
         <td>{$row.action}</td>
       </tr>
{/foreach}
       </table>
       {/strip}
       {if $action eq 16 or $action eq 4}
        <br/>
       <div class="action-link">
         <a href="{crmURL q="reset=1&action=add&gid=$gid"}">New Extended Property Field</a>
       </div>
       {/if}
    </div>
 </p>
</div>
{else}
   {if $action ne 1} {* When we are adding an item, we should not display this message *}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no extended property Fields for this property group. You can <a href="{crmURL p='civicrm/extproperty/field' q="action=add&gid=$gid"}">add one</a>.
   </div>
   {/if}
{/if}
