{*Table displays contribution totals for a contact or search result-set *}
<table class="form-layout-compressed">
    <tr>
        <th>{ts}Total Contributed{/ts} - {if $total_amount}{$total_amount|crmMoney:USD}{else}n/a{/if}</th>
        <th> &nbsp; {ts}# Contributions{/ts} - {$pager->_totalItems}</th>
        <th> &nbsp; {ts}Avg Amount{/ts} - {$total_amount/$pager->_totalItems|crmMoney:USD}</th>
    {if $cancel_amount}
        <th> &nbsp; {ts}Cancelled{/ts} - {$cancel_amount|crmMoney:USD}</th>
    {/if}
    </tr>
</table>
