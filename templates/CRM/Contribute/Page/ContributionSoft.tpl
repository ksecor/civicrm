{if $softCreditRows}
    {strip}
    <table class="selector">
        <tr class="columnheader">
	<th scope="col">{ts}Contributor{/ts}</th> 
        <th scope="col">{ts}Amount{/ts}</th>
	<th scope="col">{ts}Type{/ts}</th>
        <th scope="col">{ts}Received{/ts}</th>
        <th scope="col">{ts}Status{/ts}</th>
        <th>&nbsp;</th>   
        </tr>
       	{foreach from=$softCreditRows item=row}
	    <tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
	       <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.contributor_id`"}" id="view_contact">{$row.contributor_display_name}</a></td>
	       <td>{$row.amount|crmMoney}</td>
           <td>{$row.contribution_type}</td>
           <td>{$row.receive_date|truncate:10:''|crmDate}</td>
           <td>{$row.contribution_status}</td>
	       <td>{$row.action}</td>
	    </tr>
        {/foreach}
    </table>
    {/strip}
{/if}

