{include file="CRM/Custom/Form/Field.tpl"}

{if $customField}

<hr>
{ts 1=$groupTitle}Viewing custom fields for custom group "%1"{/ts}
<hr>

<div id="field_page">
 <p>
    <div class="form-item">
    {strip}
    <table>
    <tr class="columnheader">
        <th>{ts}Name{/ts}</th>
        <th>{ts}Label{/ts}</th>
	<th>{ts}Data Type{/ts}</th>
        <th>{ts}Weight{/ts}</th>
        <th>{ts}Is Active{/ts}</th>
        <th>&nbsp;</th>
    </tr>
    {foreach from=$customField item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>
        <td>{$row.label}</td>
        <td>{$row.data_type}</td>
        <td>{$row.weight}</td>
        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
        <td>{$row.action}</td>
    </tr>
    {/foreach}
    </table>
    {/strip}
    
    {if $action eq 16 or $action eq 4}
        <div class="action-link">
        <a href="{crmURL q="reset=1&action=add&gid=$gid"}">&raquo; {ts}New Custom Field{/ts}</a>
        </div>
    {/if}
    </div>
 </p>
</div>

{else}
    {if $action eq 16}
    <div class="message status">
    <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
    {ts 1=$groupTitle}There are no custom fields for custom group "%1",{/ts} <a href="{crmURL p='civicrm/admin/custom/group/field q="action=add&gid=$gid"}">{ts}add one{/ts}</a>.
    </div>
    {/if}
{/if}
{*else*}
   {*if $action ne 1*} {* When we are adding an item, we should not display this message *}
   {*<div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   {ts}There are no extended property Fields for this property group. You can{/ts} <a href="{crmURL p='civicrm/extproperty/field' q="action=add&gid=$gid"}">{ts}add one{/ts}</a>.
   </div>
   {/if}
{/if*}
