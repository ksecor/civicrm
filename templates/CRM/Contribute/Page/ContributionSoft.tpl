{if $softCreditRows}
{strip}
<table class="form-layout-compressed">
    <tr>
        <th class="right">{ts}Total Soft Credits{/ts} - {$softCreditTotals.total|crmMoney}</th>
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
        <th scope="col">{ts}Personal Campaign Page?{/ts}</th>
        <th></th>
     </tr>
     {foreach from=$softCreditRows item=row}
     <tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
        <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.contributor_id`"}" id="view_contact" title="{ts}View contributor contact record{/ts}">{$row.contributor_display_name}</a></td>
        <td>{$row.amount|crmMoney}</td>
        <td>{$row.contribution_type}</td>
        <td>{$row.receive_date|truncate:10:''|crmDate}</td>
        <td>{$row.contribution_status}</td>
        <td>{if $row.pcp_id}<a href="{crmURL p="civicrm/contribute/pcp/info" q="reset=1&id=`$row.pcp_id`"}" title="{ts}View Personal Campaign Page{/ts}">{$row.pcp_title}</a>{else}{ts}( n/a ){/ts}{/if}</td>
        <td><a href="{crmURL p="civicrm/contact/view/contribution" q="reset=1&id=`$row.contribution_id`&cid=`$row.contributor_id`&action=view&context=contribution&selectedChild=contribute"}" title="{ts}View related contribution{/ts}">{ts}View{/ts}</a></td>
     </tr>
     {/foreach}
</table>
{/strip}

{/if}

