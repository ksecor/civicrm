{if $top}
    {if $printOnly}
        <h1>{$reportTitle}</h1>
    {/if}
    <br/>
    {if $statistics and $outputMode}
        <table class="report-layout">
            {foreach from=$statistics.groups item=row}
                <tr>
                   <td class="report-label" width="10%">{$row.title}</td>
                   <td>{$row.value}</td>
                </tr>
            {/foreach}
            {foreach from=$statistics.filters item=row}
                <tr>
                    <td class="report-label" width="10%">{$row.title}</td>
                    <td>{$row.value}</td>
                </tr>
            {/foreach}
        </table>
        <br/>
    {/if}
{/if}

{if $bottom and $rows and $statistics}
    <br/>
    <table class="report-layout">
        {foreach from=$statistics.counts item=row}
            <tr>
                <td class="report-label" width="15%">{$row.title}</td>
                <td>
                   {if $row.type eq 1024}
                       {$row.value|crmMoney}
                   {else}
                       {$row.value}
                   {/if}

                </td>
            </tr>
        {/foreach}
    </table>
{/if}