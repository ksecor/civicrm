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
  {$form.data_type.label} {$form.data_type.html}
</div>
<div class="form-item">
  {$form.type.label} {$form.type.html}
</div>
<div class="form-item">
  {$form.default_value.label} {$form.default_value.html}
</div>
<div class="form-item">
  {$form.is_required.label} {$form.is_required.html}
</div>
<div class="form-item">
  {$form.is_active.label} {$form.is_active.html}
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
          <th>Field Label</th>
          <th>Description</th>
          <th>Data Type</th>
          <th>Form Field Type</th>
          <th>Default Value</th>
	  <th>Required</th>
          <th>Status</th>
          <th></th>
       </tr>
{foreach from=$rows item=row}
       <tr class="{cycle values="odd-row,even-row"}">
         <td>{$row.title}</td>
         <td>{$row.description}</td>
         <td>{$row.data_type}</td>
         <td>{$row.form_field_type}</td>
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
         <a href="{crmURL p='civicrm/extproperty/field' q="reset=1&action=add&gid=$gid"}">New Extended Property Field</a>
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
