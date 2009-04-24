{if $rows}
<br/>
   {if $statistics}
   <table class="form-layout"><tr><td>
   {foreach from=$statistics item=stats key=statName}
      &nbsp;&nbsp;{$stats.title}:&nbsp;{$stats.value},
   {/foreach}
   </td></tr></table><br/>
   {/if}

   <table class="form-layout">
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header key=field}
         <th>{$header.title}</th>
      {/foreach}
      </tr>

      {foreach from=$rows item=row}
      <tr class="{cycle values="odd-row,even-row"}">
         {foreach from=$columnHeaders item=header key=field}
            <td>{if $header.type eq 12}
	            {if $header.group_by eq 'MONTH' or $header.group_by eq 'QUARTER'}
		    	{$row.$field|crmDate:$config->dateformatPartial}
		    {elseif $header.group_by eq 'YEAR'}	
		    	{$row.$field|crmDate:$config->dateformatYear}
	     	    {else}				
		        {$row.$field|truncate:10:''|crmDate}
		    {/if}	
		{elseif $header.type eq 1024}
	    	    {$row.$field|crmMoney}
	        {else}
	    	    {$row.$field}
		{/if}
	    </td>
         {/foreach}
      </tr>
      {/foreach}

      {foreach from=$grandStat item=row}
      <tr>
         {foreach from=$columnHeaders item=header key=field}
            <td><strong>
		{if $header.type eq 12}
	    	    {$row.$field|truncate:10:''|crmDate}
		{elseif $header.type eq 1024}
	    	    {$row.$field|crmMoney}
	        {else}
	    	    {$row.$field}
		{/if}
		</strong></td>
         {/foreach}
      </tr>
      {/foreach}
   </table>

   {if $statistics}
   <br/><table class="form-layout">
      {foreach from=$statistics item=row}
          <tr><td>{$row.title}:&nbsp;{$row.value}</strong></td></tr>
      {/foreach}
   </table>
   {/if}
{/if}