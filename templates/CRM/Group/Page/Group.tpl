{* Actions: 1=add, 2=edit, browse=16, delete=8 *}
<div id="group">
<p>
{if $action eq 16} {* browse *}
   {strip}
   <table>
   <tr class="columnheader">
    <th>{ts}Name{/ts}</th>
    <th>{ts}Description{/ts}</th>
    <th></th>
   </tr>
   {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.title}</td>	
        <td>
            {$row.description|mb_truncate:80:"...":true}
        </td>
        <td>{$row.action}</td>
     </tr>
   {/foreach}
   </table>
   {/strip}
{/if} {* browse action *}

{if $action eq 1 or $action eq 2}
   {include file="CRM/Group/Form/Edit.tpl"}
{/if}
{if $action eq 8}
   {include file="CRM/Group/Form/Delete.tpl"}
{/if}

{if $action ne 1 and $action ne 2 and $action ne 8}
    <div class="action-link">
        <a href="{crmURL p='civicrm/group/add' q='reset=1'}">{ts}&raquo; {ts}New Group{/ts}{/ts}</a>
    </div>
{/if} {* action ne add or edit *}
</p>
</div>

