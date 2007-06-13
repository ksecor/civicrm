{*Table displays contribution totals for a contact or search result-set *}
<table class="form-layout-compressed">
    {if $annual.count}
        <tr>
        <th></th>
        <th>{ts}Current Year-to-Date{/ts} - {$annual.amount|crmMoney}</th>
        <th> &nbsp; {ts}# Contributions{/ts} - {$annual.count}</th>
        <th> &nbsp; {ts}Avg Amount{/ts} - {$annual.avg|crmMoney}</th>
        {if $contributionSummary.cancel.amount && $mode neq "view"}
            <th></th>
        {/if}
        </tr>
    {/if}
    <tr><th></th>
    {if $contributionSummary.total.amount}
        <th>{ts}Total Amount{/ts} - {$contributionSummary.total.amount|crmMoney}</th>
        <th> &nbsp; {ts}# Contributions{/ts} - {$contributionSummary.total.count}</th>
        <th> &nbsp; {ts}Avg Amount{/ts} - {$contributionSummary.total.avg|crmMoney}</th>
    {/if}
    {if $contributionSummary.cancel.amount && $mode neq "view"}
        <th class="disabled"> &nbsp; {ts}Total Cancelled Amount{/ts} - {$contributionSummary.cancel.amount|crmMoney}</th>
    {/if}
    </tr>
</table>
