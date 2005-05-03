{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/Category.tpl"}	
{/if}
<div id="cat">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Category Name (Tag)</th>
	<th>Description</th>
	<th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}">
            <td> {$row.name}
            </td>	
            <td>
                {$row.description}
            </td>
            <td>{$row.action}</td>
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add&reset=1"}">New Category</a>
       </div>
       {/if}
    </div>
 </p>
</div>
