{*Table displays contribution totals for a contact or search result-set *}
<table class="form-layout-compressed">
    <tr>
        <th>{ts}Total Amount{/ts} - {if $total_amount}{$total_amount|crmMoney}{else}n/a{/if}</th>
        <th> &nbsp; {ts}# Contributions{/ts} - {$pager->_totalItems}</th>
        <th> &nbsp; {ts}Avg Amount{/ts} - {$total_amount/$pager->_totalItems|crmMoney}</th>
    {if $cancel_amount}
        <th> &nbsp; {ts}Cancelled{/ts} - {$cancel_amount|crmMoney}</th>
    {/if}
    </tr>
</table>
