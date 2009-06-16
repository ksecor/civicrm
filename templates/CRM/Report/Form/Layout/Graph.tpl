{* Display weekly,Quarterly,monthly and yearly contributions using pChart (Bar and Pie) *}
{if $form.charts.value.0 neq '' && $displayChart && $rows}
    <table class="chart">
        <tr>
            <td>
                <img src="{$graphFilePath}"/>
            </td>
        </tr>
    </table>
{/if} 