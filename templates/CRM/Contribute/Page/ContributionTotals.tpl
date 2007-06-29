{*Table displays contribution totals for a contact or search result-set *}
<table class="form-layout-compressed">
    {if $annual.count}
        <tr>
        <th></th>
        <th class="right">{ts}Current Year-to-Date{/ts} - {$annual.amount|crmMoney}</th>
        <th class="right"> &nbsp; {ts}# Contributions{/ts} - {$annual.count}</th>
        <th class="right"> &nbsp; {ts}Avg Amount{/ts} - {$annual.avg|crmMoney}</th>
        {if $contributionSummary.cancel.amount && $mode neq "view"}
            <th></th>
        {/if}
        </tr>
    {/if}
    <tr><th></th>
    {if $contributionSummary.total.amount}
        <th class="right">{ts}Total Amount{/ts} - {$contributionSummary.total.amount|crmMoney}</th>
        <th class="right"> &nbsp; {ts}# Contributions{/ts} - {$contributionSummary.total.count}</th>
        <th class="right"> &nbsp; {ts}Avg Amount{/ts} - {$contributionSummary.total.avg|crmMoney}</th>
    {/if}
    {if $contributionSummary.cancel.amount && $mode neq "view"}
        <th class="disabled right"> &nbsp; {ts}Total Cancelled Amount{/ts} - {$contributionSummary.cancel.amount|crmMoney}</th>
    {/if}
    </tr>
</table>
