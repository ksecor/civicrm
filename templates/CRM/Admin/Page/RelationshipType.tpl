{capture assign=customURL}{crmURL p='civicrm/admin/custom/group' q="reset=1"}{/capture}
{capture assign=docLink}{docURL page="Relationship Types"}{/capture}
<div id="help">
    <p>{ts}Relationship types describe relationships between people, households and organizations. Relationship types labels describe the relationship from the perspective of each of the two entities (e.g. Parent <-> Child, Employer <-> Employee). For some types of relationships, the labels may be the same in both directions (e.g. Spouse <-> Spouse).{/ts} {$docLink}</p>
    <p>{ts 1=$customURL}You can define as many additional relationships types as needed to cover the types of relationships you want to track. Once a relationship type is created, you may also define custom fields to extend relationship information for that type from <a href='%1'>Administer CiviCRM &raquo; Custom Data</a>.{/ts}</p>
</div>

{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8}
   {include file="CRM/Admin/Form/RelationshipType.tpl"}	
{/if}

{if $rows}

<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
	        <th>{ts}Relationship A to B{/ts}</th>
	        <th>{ts}Relationship B to A{/ts}</th>
	        <th>{ts}Contact Type A{/ts}</th>
	        <th>{ts}Contact Type B{/ts}</th>
	        <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td> {$row.label_a_b} </td>	
            <td> {$row.label_b_a} </td>	
            <td> {if $row.contact_type_a_display} {$row.contact_type_a_display} {else}  {ts}All Contacts{/ts} {/if} </td>	
            <td> {if $row.contact_type_b_display} {$row.contact_type_b_display} {else}  {ts}All Contacts{/ts} {/if} </td>	
            <td>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if !($action eq 1 and $action eq 2)}
        <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newRelationshipType" class="button"><span>&raquo; {ts}New Relationship Type{/ts}</span></a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/reltype' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no relationship types present. You can <a href='%1'>add one</a>.{/ts}</dd>
    </dl>
    </div>    
{/if}
