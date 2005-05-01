{if $action eq 1 or $action eq 2}
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
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	    <td> {$row.name}</td>	
            <td><a href="{crmURL p='admin/contact/mobileProvider' q="id=`$row.id`&action=update"}">Edit</a></td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='admin/contact/mobileProvider' q="action=add&reset=1"}">New Mobile Provider</a>
       </div>
       {/if}
    </div>
 </p>
</div>
