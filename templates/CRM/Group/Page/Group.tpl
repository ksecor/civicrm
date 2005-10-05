{* Actions: 1=add, 2=edit, browse=16, delete=8 *}
{if $rows}
<div id="group">
<p></p>
{if $action eq 16 or $action eq 32 or $action eq 64} {* browse *}
   {strip}
   <table>
   <tr class="columnheader">
    <th>{ts}Name{/ts}</th>
    <th>{ts}Description{/ts}</th>
    <th>{ts}Visibility{/ts}</th>
    <th></th>
   </tr>
   {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}{if NOT $row.is_active} disabled{/if}">
        <td>{$row.title}</td>	
        <td>
            {$row.description|mb_truncate:80:"...":true}
        </td>
        <td>{$row.visibility}</td>	
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
        <a href="{crmURL p='civicrm/group/add' q='reset=1'}">&raquo; {ts}New Group{/ts}</a>
    </div>
{/if} {* action ne add or edit *}
</div>
{else}
    <div class="status messages">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/group/add' q="reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Groups entered for this Contact. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
