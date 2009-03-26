{*Table displays contribution totals for a contact or search result-set *}
    {if $annual.count}
      <table class="form-layout-compressed">
        <tr>
        <th class="right">{ts}Current Year-to-Date{/ts} - {$annual.amount|crmMoney}</th>
        <th class="right"> &nbsp; {ts}# Contributions{/ts} - {$annual.count}</th>
        <th class="right"> &nbsp; {ts}Avg Amount{/ts} - {$annual.avg|crmMoney}</th>
        </tr>
      </table> 
    {/if}

    {if $contributionSummary }
     <table class="form-layout-compressed">
      <tr>
      {if $contributionSummary.total.amount}
        <th style="border-left: 1px solid #5A8FDB;">{ts}Total Amount{/ts} - {$contributionSummary.total.amount|crmMoney}</th>
        <th class="right" width="39px"> &nbsp; </th>
        <th class="right"> &nbsp; {ts}# Contributions{/ts} - {$contributionSummary.total.count}</th>
        <th class="right" style="border-right: 1px solid #5A8FDB;"> &nbsp; {ts}Avg Amount{/ts} - {$contributionSummary.total.avg|crmMoney}</th>
      {/if}
      {if $contributionSummary.cancel.amount}
        <th class="disabled right"> &nbsp; {ts}Total Cancelled Amount{/ts} - {$contributionSummary.cancel.amount|crmMoney}</th>
      {/if}
      </tr>  
     </table>
    {/if} 