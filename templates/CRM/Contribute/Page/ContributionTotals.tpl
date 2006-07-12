{*Table displays contribution totals for a contact or search result-set *}
<table class="form-layout-compressed">
    <tr><th></th>
    {if $summary.total.amount}
        <th>{ts}Total Amount{/ts} - {$summary.total.amount|crmMoney}</th>
        <th> &nbsp; {ts}# Contributions{/ts} - {$summary.total.count}</th>
        <th> &nbsp; {ts}Avg Amount{/ts} - {$summary.total.avg|crmMoney}</th>
    {/if}
    {if $summary.cancel.amount}
        <th class="disabled"> &nbsp; {ts}Total Cancelled Amount{/ts} - {$summary.cancel.amount|crmMoney}</th>
    {/if}
    </tr>
</table>
