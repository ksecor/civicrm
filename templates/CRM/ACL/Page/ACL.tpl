<div id="help">
    {ts}ACL's are the base permissioning unit. Check the ACL wiki page for more details...{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/ACL/Form/ACL.tpl"}
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}Allow?{/ts}</th>
            <th>{ts}Operation{/ts}</th>
            <th>{ts}Entity Table{/ts}</th>
            <th>{ts}Entity ID{/ts}</th>
            <th>{ts}Object Table{/ts}</th>
            <th>{ts}Object ID{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{if $row.deny eq 1} {ts}Deny{/ts} {else} {ts}Allow{/ts} {/if}</td> 
	        <td>{$row.operation}</td>	
	        <td>{$row.entity_table}</td>	
	        <td>{$row.entity_id}</td>	
	        <td>{$row.object_table}</td>	
	        <td>{$row.object_id}</td>	
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newACL">&raquo; {ts}New ACL{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{elseif $action ne 1 and $action ne 2 and $action ne 8}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/acl' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no ACL's entered. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
