{if $action eq 1 or $action eq 2 or $action eq 4}
   {include file="CRM/Admin/Form/RelationshipType.tpl"}	
{/if}
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
         <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} {ts}disabled{/ts}{/if}">
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

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add&reset=1"}">{ts}New Relationship Type{/ts}</a>
       </div>
       {/if}
    </div>
 </p>
</div>
