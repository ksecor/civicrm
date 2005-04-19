{if $op eq 'add' or $op eq 'edit'}
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
       {foreach from=$IMProviders item=IMProv}
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$IMProv.name}</td>	
            <td><a href="{crmURL p='admin/contact/improv' q="impid=`$IMProv.id`&op=edit"}">Edit</a></td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $op eq 'browse' }
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='admin/contact/improv' q="op=add"}">New IM Provider</a>
       </div>
       {/if}
    </div>
 </p>
</div>
