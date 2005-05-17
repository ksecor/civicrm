{include file="CRM/Custom/Form/Field.tpl"}

{if $customField}

<hr>
Viewing custom fields for custom group "{$groupTitle}"
<hr>

<div id="field_page">
 <p>
    <div class="form-item">
    {strip}
    <table>
    <tr class="columnheader">
        <th>Name</th>
        <th>Label</th>
	    <th>Data Type</th>
        <th>Weight</th>
        <th>Is Active</th>
        <th>&nbsp;</th>
    </tr>
    {foreach from=$customField item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>
        <td>{$row.label}</td>
        <td>{$row.data_type}</td>
        <td>{$row.weight}</td>
        <td>{if $row.is_active eq 1} Yes {else} No {/if}</td>
        <td>{$row.action}</td>
    </tr>
    {/foreach}
    </table>
    {/strip}
    
    {if $action eq 16 or $action eq 4}
        <div class="action-link">
        <a href="{crmURL q="reset=1&action=add&gid=$gid"}">&raquo; New Custom Field</a>
        </div>
    {/if}
    </div>
 </p>
</div>

{else}
    {if $action eq 16}
    <div class="message status">
    <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
    There are no custom fields for custom group "{$groupTitle}", <a href="{crmURL p='civicrm/admin/custom/group/field q="action=add&gid=$gid"}">add one</a>.
    </div>
    {/if}
{/if}
{*else*}
   {*if $action ne 1*} {* When we are adding an item, we should not display this message *}
   {*<div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no extended property Fields for this property group. You can <a href="{crmURL p='civicrm/extproperty/field' q="action=add&gid=$gid"}">add one</a>.
   </div>
   {/if}
{/if*}
