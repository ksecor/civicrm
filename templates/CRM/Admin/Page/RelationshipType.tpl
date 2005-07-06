<div id="help">
    {ts}<p>Relationship types describe relationships between people, households and organizations. CiviCRM includes several 'reserved' types for frequently used relationships (such as Parent/Child or Employer/Employee).</p>
    <p>You can define as many additional relationships types as needed to cover the types of relationships you want to track.</p>{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 4}
   {include file="CRM/Admin/Form/RelationshipType.tpl"}	
{/if}

{if $rows}
<div id="ltype">
<p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
	        <th>{ts}Relationship A to B{/ts}</th>
	        <th>{ts}Relationship B to A{/ts}</th>
	        <th>{ts}Contact Type A{/ts}</th>
	        <th>{ts}Contact Type B{/ts}</th>
	        <th>{ts}Reserved?{/ts}</th>
	        <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td> {$row.name_a_b} </td>	
            <td> {$row.name_b_a} </td>	
            <td> {$row.contact_type_a} </td>	
            <td> {$row.contact_type_b} </td>	
            <td> {if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if} </td>	
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if !($action eq 1 and $action eq 2)}
        <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}">&raquo; {ts}New Relationship Type{/ts}</a>
        </div>
        {/if}
    </div>
</p>
</div>
{else}
    <div class="status message">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/reltype' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Tags entered for this Contact. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
