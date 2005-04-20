{if $action eq 1 or $action eq 2 or $action eq 4}
<form {$form.attributes}>
<fieldset>
<div class="form-item">
  {$form.title.label} {$form.title.html}
</div>
<div class="form-item">
  {$form.description.label} {$form.description.html}
</div>
<div class="form-item">
  {$form.extends.label}  {$form.extends.html}
</div>
<div class="form-item">
  {$form.is_active.html} {$form.is_active.label}
</div>
{if $action ne 4}
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
{/if} {* $action ne view *}
</fieldset>
</form>
{/if}

{if $rows}
<div id="notes">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
          <th>Group Title</th>
          <th>Description</th>
          <th>Status</th>
          <th>Used For</th>
          <th></th>
       </tr>
{foreach from=$rows item=row}
       <tr class="{cycle values="odd-row,even-row"}">
         <td>{$row.title}</td>
         <td>{$row.description}</td>
         <td>{$row.is_active}</td>
         <td>{$row.extends}</td>
         <td>{$row.action}</td>
       </tr>
{/foreach}
       </table>
       {/strip}
       {if $action eq 16 or $action eq 4}
        <br/>
       <div class="action-link">
         <a href="{crmURL p='civicrm/extproperty/group' q="action=add"}">New Extended Property Group</a>
       </div>
       {/if}
    </div>
 </p>
</div>
{else}
   <div class="message status">
   <img src="crm/i/Inform.gif" alt="status"> &nbsp;
   There are no extended property groups for this organization. You can <a href="{crmURL p='civicrm/extproperty/group' q='action=add'}">add one</a>.
   </div>
{/if}
