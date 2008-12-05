{if $softCreditRows}

{strip}
<p></p>
<table class="form-layout-compressed">
    <tr>
        <th class="right">{ts}Total amount credited{/ts} - {$softCreditTotals.total|crmMoney}</th>
    </tr>
</table> 
<p></p>

<table class="selector">
    <tr class="columnheader">
        <th scope="col">{ts}Contributor{/ts}</th> 
        <th scope="col">{ts}Amount{/ts}</th>
	<th scope="col">{ts}Type{/ts}</th>
        <th scope="col">{ts}Received{/ts}</th>
        <th scope="col">{ts}Status{/ts}</th>
        <th scope="col">{ts}Personal Contribution Page?{/ts}</th>
     </tr>
     {foreach from=$softCreditRows item=row}
     <tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
	<td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.contributor_id`"}" id="view_contact">{$row.contributor_display_name}</a></td>
	<td>{$row.amount|crmMoney}</td>
        <td>{$row.contribution_type}</td>
        <td>{$row.receive_date|truncate:10:''|crmDate}</td>
        <td>{$row.contribution_status}</td>
	<td>{if $row.pcp_id}<a href="{crmURL p="civicrm/contribute/pcp/info" q="reset=1&id=`$row.pcp_id`"}">{$row.pcp_title}</a>{else}N/A{/if}</td>
     </tr>
     {/foreach}
</table>
{/strip}

{/if}

