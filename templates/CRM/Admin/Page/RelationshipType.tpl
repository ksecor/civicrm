{if $action eq 1 or $action eq 2 or $action eq 4}
   {include file="CRM/Admin/Form/RelationshipType.tpl"}	
{/if}
<div id="ltype">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Relationship A to B</th>
	<th>Relationship B to A</th>
	<th>Contact Type A</th>
	<th>Contact Type B</th>
	<th>Reserved?</th>
	<th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	    <td> {$row.name_a_b} </td>	
	    <td> {$row.name_b_a} </td>	
	    <td> {$row.contact_type_a} </td>	
	    <td> {$row.contact_type_b} </td>	
	    <td> {if $row.is_reserved eq 1} Yes {else} No {/if} </td>	
	    <td>{$row.action}</td>
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='civicrm/admin/reltype' q="action=add&reset=1"}">New Relationship Type</a>
       </div>
       {/if}
    </div>
 </p>
</div>
