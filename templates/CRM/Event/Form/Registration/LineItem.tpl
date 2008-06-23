    {foreach from=$lineItem item=value key=priceset}
    {if $value neq 'skip'}
    {if $priceset eq 0}
    <strong>{ts}Primary Participant{/ts}</strong>
    {else}
    <strong>{ts}Additional Participant {$priceset}{/ts}</strong>
    {/if}				 
    <table>
            <tr class="columnheader">
                <th>{ts}Item{/ts}</th>
                <th class="right">{ts}Qty{/ts}</th>
                <th class="right">{ts}Unit Price{/ts}</th>
                <th class="right">{ts}Total Price{/ts}</th>
            </tr>
                {foreach from=$value item=line}
            <tr>
                <td>{$line.description}</td>
                <td class="right">{$line.qty}</td>
                <td class="right">{$line.unit_price|crmMoney}</td>
                <td class="right">{$line.line_total|crmMoney}</td>
            </tr>
            {/foreach}
    </table>
    {/if}
    {/foreach}
        <strong>{ts}Event Total{/ts}: {$totalAmount|crmMoney}</strong>
