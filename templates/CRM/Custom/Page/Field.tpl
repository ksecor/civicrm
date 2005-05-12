{include file="CRM/Custom/Form/Field.tpl"}

{if $customField}
<div id="field_page">
 <p>
    <div class="form-item">
    {strip}
    <table>
    <tr class="columnheader">
        <th>ID</th>
        <th>Custom Group ID</th>
        <th>Name</th>
        <th>Label</th>
	    <th>Data Type</th>
        <th>Html Type</th>
        <th>Default Value</th>
        <th>Is Required</th>
        <th>Weight</th>
        <th>Validation Id</th>
        <th>Help Pre</th>
        <th>Help Post</th>  
        <th>Mask</th>
        <th>Attributes</th>
        <th>javasript</th>
        <th>Is Active</th>
    </tr>
    {foreach from=$customField item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.id}</td>
        <td>{$row.custom_group_id}</td>
        <td>{$row.name}</td>
        <td>{$row.label}</td>
        <td>{$row.data_type}</td>
        <td>{$row.html_type}</td>
        <td>{$row.default_value}</td>
        <td>{$row.is_required}</td>
        <td>{$row.weight}</td>
        <td>{$row.validation_id}</td>
        <td>{$row.help_pre}</td>
        <td>{$row.help_post}</td>
        <td>{$row.mask}</td>
        <td>{$row.attributes}</td>
        <td>{$row.javascript}</td>
        <td>{$row.is_active}</td>
    </tr>
    {/foreach}
    </table>
    {/strip}
    
   {*if $action eq 16 or $action eq 4}
        <br/>
       <div class="action-link">
         <a href="{crmURL q="reset=1&action=add&gid=$gid"}">New Extended Property Field</a>
       </div>
       {/if*}
    </div>
 </p>
</div>

{else}
    <div class="message status">
    <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
    ADD NECCESSARY TEXT HERE
    </div>
{/if}
{*else*}
   {*if $action ne 1*} {* When we are adding an item, we should not display this message *}
   {*<div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no extended property Fields for this property group. You can <a href="{crmURL p='civicrm/extproperty/field' q="action=add&gid=$gid"}">add one</a>.
   </div>
   {/if}
{/if*}
