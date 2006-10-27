{capture assign=aclURL}{crmURL p='civicrm/acl' q='reset=1'}{/capture}
{capture assign=rolesURL}{crmURL p='civicrm/admin/options' q='group=acl_role&reset=1'}{/capture}

<div id="help">
    <p>{ts}ACL's allow you control access to CiviCRM contacts. An ACL consists of an <strong>Operation</strong> ('View' or 'View and Edit'), a <strong>Group of contacts</strong> that the operation can be performed on, and an <strong>ACL Role</strong> that has permission to do this operation.{/ts}</p>
    <p>{ts 1=$aclURL 2=$rolesURL 3="http://wiki.civicrm.org/confluence//x/fCM"}An ACL Role represents a collection of individual ACL's. You can assign roles to groups of CiviCRM contacts who are users of your site below. You can add or modify ACL's <a href="%1">here</a>. You can create additional ACL Roles <a href="%2">here</a>.
        Refer to the <a href="%3">ACL Administrator Guide</a> for more info.{/ts}</p>
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/ACL/Form/EntityRole.tpl"}
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}ACL Role{/ts}</th>
            <th>{ts}Assigned To{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.acl_role}</td>	
	        <td>{$row.entity}</td>	
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
            <a href="{crmURL q="action=add&reset=1"}" id="newACL">&raquo; {ts}New Role Assignment{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{elseif $action ne 1 and $action ne 2 and $action ne 8}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Role Assignments. You can <a href="%1">add one</a> now.{/ts}</dd>
        </dl>
    </div>    
{/if}
