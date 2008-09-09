{* Display monthly and yearly contributions using Google charts (Bar and Pie) *} 
{if $hasContributions}
<table class="chart">
<tr>
    {if $chartType == 'bvg'}
	{if $totalMonths > 7}
            {assign var=msize value="440x250"}
	{else}
	    {assign var=msize value="300x250"}
	{/if}
	{if $totalYears > 7}
            {assign var=ysize value="440x250"}
	{else}
	    {assign var=ysize value="300x250"}
	{/if}
    {else}
      {assign var=msize value="300x150"}
      {assign var=ysize value="300x150"} 	
    {/if}
    {assign var=color value="99C754|54C7C5|999999"}
    {assign var=bgcolor value="bg,s,fafafa"}
    {if $monthlyData}
        <td><img src="http://chart.apis.google.com/chart?cht={$chartType}&chs={$msize}&chd=t:{$chartData}&chl={$chartLabel}&chtt={$chartLegend}&chco={$color}&chxl=1:|0|{$config->defaultCurrency}-{$monthMaxAmount}&chxt=x,y&chds=0,{$monthMaxAmount}&chf={$bgcolor}&chm={$mMarker}" /></td>
    {else}
        <td>{ts}There were no contributions during the selected year.{/ts} </td>
    {/if}
    <td><img src="http://chart.apis.google.com/chart?cht={$chartType}&chs={$ysize}&chd=t:{$chartData1}&chl={$chartLabel1}&chtt={$chartLegend1}&chco={$color}&chxl=1:|0|{$config->defaultCurrency}-{$yearMaxAmount}&chxt=x,y&chds=0,{$yearMaxAmount}&chf={$bgcolor}&chm={$yMarker}" /> </td>
</tr>
</table>

<table  class="form-layout-compressed" >
      <td class="label">{$form.select_year.label}</td><td>{$form.select_year.html}</td> 
      <td class="label">{$form.chart_type.label}</td><td>{$form.chart_type.html}</td> 
      <td class="html-adjust">
        {$form.buttons.html}<br />
        <span class="add-remove-link"><a href="{crmURL p="civicrm/contribute" q="reset=1"}">{ts}Table View{/ts}...</a></span>
      </td> 
</table> 
{else}
 <div class="messages status"> 
      <dl> 
        <dd>{ts}There are no live contribution records to display.{/ts}</dd> 
      </dl> 
 </div> 
{/if}

