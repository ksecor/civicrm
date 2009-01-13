{* Display monthly and yearly contributions using Google charts (Bar and Pie) *} 
{if $hasContributions}
<table class="chart">
<tr>
 {if $monthlyData}
     <td><img src="{$monthFilePath}" usemap="#monthMap"/>
	<map id="monthMap" name="monthMap">
	{foreach from=$monthCoords item=values key=field}
	   {if $chartType eq 'pie'}   
	       {foreach from=$values item=coordinates}
	       <area shape="{$shape}" coords="{$coordinates}" href="{$monthUrls.$field}" title="{ts 1=$field}Contribution(s) for the month of %1{/ts}"/>
	       {/foreach}
	   {else}	
	        <area shape="{$shape}" coords="{$values}" href="{$monthUrls.$field}" title="{ts 1=$field}Contribution(s) for the month of %1{/ts}"/>
	   {/if}
	{/foreach}
	</map>
     </td>
 {else}
     <td>{ts}There were no contributions during the selected year.{/ts} </td>
 {/if}
     <td><img src="{$yearFilePath}" usemap="#yearMap"/>
	<map id="yearMap" name="yearMap">
	{foreach from=$yearCoords item=values key=field}
	   {if $chartType eq 'pie'}   
	       {foreach from=$values item=coordinates}
	       <area shape="{$shape}" coords="{$coordinates}" href="{$yearUrls.$field}" title="{ts 1=$field}Contribution(s) for the year of %1{/ts}"/>
	       {/foreach}
	   {else}
	       <area shape="{$shape}" coords="{$values}" href="{$yearUrls.$field}" title="{ts 1=$field}Contribution(s) for the year of %1{/ts}"/>	
	   {/if}
	{/foreach}
	</map>
     </td>
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

