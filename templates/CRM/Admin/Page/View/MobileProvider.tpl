{if $op eq 'add' or $op eq 'edit'}
   {include file="CRM/Admin/Form/MobileProvider.tpl"}	
{/if}

<div id="mobprovider">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Name</th>
	<th></th>
       </tr>
       {foreach from=$MobileProviders item=MobileProv}
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$MobileProv.name}</td>	
            <td><a href="{crmURL p='admin/contact/mobprov' q="impid=`$MobileProv.id`&op=edit"}">Edit</a></td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $op eq 'browse' }
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='admin/contact/mobprov' q="op=add"}">New Mobile Provider</a>
       </div>
       {/if}
    </div>
 </p>
</div>
