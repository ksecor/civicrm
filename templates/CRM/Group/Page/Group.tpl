{* Actions: 1=add, 2=edit, browse=16, delete=8 *}
{if $action eq 16}
<div id="help">
<p>{ts}Use Groups to organize contacts (e.g. these contacts are members of our 'Steering Committee'). You can also create 'smart' groups whose membership is based on contact characteristics (e.g. this group consists of all people in our database who live in a specific locality).{/ts}</p>
<p>{ts}You can add contacts to a group from any set of search results (or when viewing an individual contact). You can also allow contacts to sign themselves up for certain groups by setting the group visibility to 'Public User Pages' (use the <strong>Settings</strong> link), and including the <strong>Groups</strong> element in your CiviCRM Profile.{/ts}</p>
</div>
{/if}


{assign var="showBlock" value = "'group'"}
{assign var = "hideBlock" value = "'group_show'"}


<div id = "group_show" class="hide-row">
<a href = "#" onclick="hide('group_show'); show('group'); return false;"><img src = "{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt = "{ts}open section{/ts}" /></a>
</div>  
<div id="group" >
<span id = "group_hide"><a href = "#" onclick="hide('group'); show('group_show'); return false;"><img src = "{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
{if $rows}
<p></p>
{if $action eq 16 or $action eq 32 or $action eq 64} {* browse *}  
   {strip}


   <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead>  
     <tr class="columnheader">
<th field = "Ancestry" dataType="Integer" scope="col">{ts}Ancestry{/ts}</th>
<th field="Name" dataType="String" scope="col">{ts}Name{/ts}</th>
      <th field="ID" dataType="Integer" scope="col">{ts}ID{/ts}</th>
      <th field="Description" dataType="String" scope="col">{ts}Description{/ts}</th>
      <th field="Visibility" dataType="String" scope="col">{ts}Visibility{/ts}</th>
      <th datatype="html"></th>
     </tr>
	</thead>

	<tbody>
{assign var = counter value = 1}
{$counter}
{assign var = current_rows[] value = '[]'}
{foreach from=$rows item=row}
{if $row.level EQ 0}
{assign var = current_rows[$counter] value = $row}
{$current_rows.$counter.title}
{assign var = counter value = counter+1}
{/if}
{/foreach}
{include file = "CRM/Group/Page/GroupRows.tpl"}
</div>
	</tbody>
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

{* Include Javascript to hide and display the appropriate blocks as directed by the php code *}
{include file="CRM/common/showHide.tpl"}
