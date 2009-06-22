{* Display weekly,Quarterly,monthly and yearly contributions using pChart (Bar and Pie) *}
{if $chartEnabled && $chartSupported && $rows}
    <table class="chart">
        <tr>
            <td>
                <img src="{$graphFilePath}"/>
            </td>
        </tr>
    </table>
{/if} 