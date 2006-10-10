{*Table displays contribution totals for a contact or search result-set *}
<table class="form-layout-compressed">
    <tr><th></th>
    {if $contributionSummary.total.amount}
        <th>{ts}Total Amount{/ts} - {$contributionSummary.total.amount|crmMoney}</th>
        <th> &nbsp; {ts}# Contributions{/ts} - {$contributionSummary.total.count}</th>
        <th> &nbsp; {ts}Avg Amount{/ts} - {$contributionSummary.total.avg|crmMoney}</th>
    {/if}
    {if $contributionSummary.cancel.amount}
        <th class="disabled"> &nbsp; {ts}Total Cancelled Amount{/ts} - {$contributionSummary.cancel.amount|crmMoney}</th>
    {/if}
    </tr>
</table>
