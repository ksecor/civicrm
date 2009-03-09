    {assign var="total_price" value=0}
    <dt>{ts}Event Fees{/ts}</dt>
    <dd>
        <table class="report">
           <tr class="columnheader">
              <th>{ts}Item{/ts}</th>
              <th>{ts}Qty{/ts}</th>
              <th>{ts}Unit Price{/ts}</th>
              <th>{ts}Total{/ts}</th>
           </tr>
           {foreach from=$line_items item=line}
              {assign var="total_price" value=`$total_price+$line.line_total`}
                 <tr>
                     <td>{$line.label}</td>
                     <td>{$line.qty}</td>
                     <td class="right">{$line.unit_price|crmMoney}</td>
                     <td class="right">{$line.line_total|crmMoney}</td>
                 </tr>
           {/foreach}
                 <tr>
                     <td colspan="3"><strong>{ts}Line Item Total{/ts}</strong>:</td>
                     <td class="right"><strong>{$total_price|crmMoney}</strong></td>
                 </tr>
                 {* If we have a contribution and the amount is different from line item total - then discount has been applied. *}
                 {if $rows.0.total_amount AND ($rows.0.total_amount NEQ $total_price)}
                     {assign var="discount" value=`$total_price-$rows.0.total_amount`}
                     <tr>
                         <td colspan="3">{ts}Discounted Applied{/ts}:</td>
                         <td class="right"> - {$discount|crmMoney}</td>
                     </tr>
                     <tr>
                         <td colspan="3"><strong>{ts}Discounted Total{/ts}</strong>:</td>
                         <td class="right"><strong>{$rows.0.total_amount|crmMoney}</strong></td>
                     </tr>
                 {/if}
        </table>
    </dd>
