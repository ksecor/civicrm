{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/IMProvider.tpl"}	
{/if}

<div id="improvider">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Name</th>
	<th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$row.name}</td>	
            <td><a href="{crmURL p='admin/contact/IMProvider' q="id=`$row.id`&action=update"}">Edit</a></td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
       <br/>
       <div class="action-link">
    	 <a href="{crmURL p='admin/contact/IMProvider' q="action=add&reset=1"}">New IM Provider</a>
       </div>
       {/if}
    </div>
 </p>
</div>
