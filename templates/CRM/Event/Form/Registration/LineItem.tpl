    {foreach from=$lineItem item=value key=pricest}	 
    <table>
            <tr class="columnheader">
                <th>{ts}Item{/ts}</th>
                <th class="right">{ts}Qty{/ts}</th>
                <th class="right">{ts}Unit Price{/ts}</th>
                <th class="right">{ts}Total Price{/ts}</th>
            </tr>
                {foreach from=$value item=line}
            <tr>
                <td>{$line.label}</td>
                <td class="right">{$line.qty}</td>
                <td class="right">{$line.unit_price|crmMoney}</td>
                <td class="right">{$line.line_total|crmMoney}</td>
            </tr>
            {/foreach}
    </table>
    {/foreach}
        <strong>{ts}Event Total{/ts}: {$total_amount|crmMoney}</strong>
