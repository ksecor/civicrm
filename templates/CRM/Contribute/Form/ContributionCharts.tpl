{* this template is used for showing google chart( Bar and Pi ) for monthly and year contribution  *} 
{if $noContribution}
<table class="form-layout-compressed">
<tr>
{if $chartType == 'bvg'}
  {assign var=size value="435x250"}
{else}
  {assign var=size value="300x150"}
{/if}
{if $monthlyData}
    <td> <img src="http://chart.apis.google.com/chart?cht={$chartType}&chs={$size}&chd=t:{$chatData}&chl={$chatLabel}&chtt={$chartLegend}&chco=99C754|54C7C5|999999&chxl=1:|0|{$config->defaultCurrency}-{$monthMaxAmount}&chxt=x,y&chds=0,{$monthMaxAmount}&chm={$mMarker}" /> </td>
{else }
    <td>{ts}Current Year don't have Month Contribution{/ts} </td>
{/if}

    <td> <img src="http://chart.apis.google.com/chart?cht={$chartType}&chs={$size}&chd=t:{$chatData1}&chl={$chatLabel1}&chtt={$chartLegend1}&chco=99C754|54C7C5|999999&chxl=1:|0|{$config->defaultCurrency}-{$yearMaxAmount}&chxt=x,y&chds=0,{$yearMaxAmount}&chm={$yMarker}" /> </td> </tr>

</table>

<table  class="form-layout-compressed" >
      <td class="label">{$form.select_map.label}</td><td>{$form.select_map.html}</td> 
      <td class="label">{$form.select_year.label}</td><td>{$form.select_year.html}</td> 
      <td class="html-adjust">{$form.buttons.html}</td> 
</table> 
{else}
 <div class="messages status"> 
      <dl> 
        <dd>{ts}Contribution Not Available{/ts}</dd> 
      </dl> 
 </div> 
{/if}
