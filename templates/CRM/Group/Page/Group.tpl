{* Actions: 1=add, 2=edit, browse=16, delete=8 *}
{if $action eq 16}
<div id="help">
    {ts}Use Groups to organize contacts (e.g. these contacts are members of our 'Steering Committee'). You can also create 'smart' groups whose membership is based on contact characteristics (e.g. this group consists of all people in our database who live in a specific locality).{/ts} {help id="manage_groups"}
</div>
{if $action ne 1 and $action ne 2 and $action ne 8 and $groupPermission eq 1}
    <div class="action-link">
        <a href="{crmURL p='civicrm/group/add' q='reset=1'}" id="newGroup">&raquo; {ts}New Group{/ts}</a>
    </div>
{/if} {* action ne add or edit *}
{include file="CRM/Group/Form/Search.tpl"}
{/if}
 
{if $rows}
<div id="group">
{if $action eq 16 or $action eq 32 or $action eq 64} {* browse *}  
{include file="CRM/common/pager.tpl" location="top"}
{include file="CRM/common/pagerAToZ.tpl}
   {strip}
   <table enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead>  
     <tr class="columnheader">
      <th field="Name" dataType="String" scope="col">{ts}Name{/ts}</th>
      <th field="ID" dataType="Integer" scope="col">{ts}ID{/ts}</th>
      <th field="Description" dataType="String" scope="col">{ts}Description{/ts}</th>
      <th field="GroupType" dataType="String" scope="col">{ts}Group Type{/ts}</th>
      <th field="Visibility" dataType="String" scope="col">{ts}Visibility{/ts}</th>
<th field = "ChildGroups" dataType = "String" scope ="col">{ts}Child Groups{/ts}</th>
<th datatype="html"></th>
     </tr>
	</thead>

	<tbody>  
   {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}{if NOT $row.is_active} disabled{/if}">
        <td>{$row.title}</td>	
        <td>{$row.id}</td>
        <td>
            {$row.description|mb_truncate:80:"...":true}
        </td>
        <td>{$row.group_type}</td>	
        <td>{$row.visibility}</td>	
<td>{$row.children}</td>
        <td>{$row.action}</td>

     </tr>
   {/foreach}
	</tbody>
   </table>
   {/strip}
{include file="CRM/common/pager.tpl" location="bottom"}
{/if}{* browse action *}

{if $action eq 1 or $action eq 2} 
   {include file="CRM/Group/Form/Edit.tpl"}
{/if}
{if $action eq 8}
   {include file="CRM/Group/Form/Delete.tpl"}
{/if}

{if $action ne 1 and $action ne 2 and $action ne 8 and $groupPermission eq 1}
    <div class="action-link">
        <a href="{crmURL p='civicrm/group/add' q='reset=1'}" id="newGroup">&raquo; {ts}New Group{/ts}</a>
    </div>
{/if} {* action ne add or edit *}
</div>
{* No groups to list. Check isSearch flag to see if we're in a search or not. Display 'add group' prompt if user has 'edit groups' permission. *}
{elseif $isSearch eq 1}
    <div class="status messages">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
            {capture assign=browseURL}{crmURL p='civicrm/group' q="reset=1"}{/capture}
            <dd>
                {ts 1=$browseURL}No available Groups match your search criteria. Suggestions:
                <div class="spacer"></div>
                <ul>
                <li>Check your spelling.</li>
                <li>Try a different spelling or use fewer letters.</li>
                <li>Make sure you have enough privileges in the access control system.</li>
                </ul>
                Or you can <a href="%1">browse all available Groups</a>.{/ts}
            </dd>
        </dl>
    </div>
{else}
    <div class="status messages">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
            {capture assign=crmURL}{crmURL p='civicrm/group/add' q="reset=1"}{/capture}
            <dd>{ts}No Groups have been created for this site.{/ts}
                {if $groupPermission eq 1}
                    {ts 1=$crmURL}You can <a href="%1">add one</a> now.{/ts}
                {/if}
            </dd>
        </dl>
    </div>
{/if}
