{* Actions: 1=add, 2=edit, browse=16, delete=8 *}
{if $action eq 16}
<div id="help">
<p>{ts}Use Groups to organize contacts (e.g. these contacts are members of our 'Steering Committee'). You can also create 'smart' groups whose membership is based on contact characteristics (e.g. this group consists of all people in our database who live in a specific locality).{/ts}</p>
<p>{ts}You can add contacts to a group from any set of search results (or when viewing an individual contact). You can also allow contacts to sign themselves up for certain groups by setting the group visibility to 'Public User Pages' (use the <strong>Settings</strong> link), and including the <strong>Groups</strong> element in your CiviCRM Profile.{/ts}</p>
</div>
{/if}
 
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
{else} {* No groups to list. Display 'add group' prompt if user has 'edit groups' permission. *}
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
