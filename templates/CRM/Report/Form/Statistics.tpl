{if $top}
    {if $printOnly}
        <h1>{$reportTitle}</h1>
    {/if}
    <br/>
    {if $statistics and $outputMode}
        <table class="report-layout">
            {foreach from=$statistics.groups item=row}
                <tr>
                   <td width="10%">{$row.title}</td>
                   <td><strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
            {foreach from=$statistics.filters item=row}
                <tr>
                    <td width="10%">{$row.title}</td>
                    <td><strong>{$row.value}</strong></td>
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
                <td width="10%">{$row.title}</td>
                <td><strong>{$row.value}</strong></td>
            </tr>
        {/foreach}
    </table>
{/if}