{if $rows}
{if $printOnly}
<h1>{$reportTitle}</h1>
{/if}
<br/>
{include file="CRM/common/pager.tpl" noForm=1}
   <table class="form-layout">
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header key=field}
         <th>{$header.title}</th>
      {/foreach}
      </tr>

      {foreach from=$rows item=row}
      <tr class="{cycle values="odd-row,even-row"}">
         {foreach from=$columnHeaders item=header key=field}
            {assign var=fieldLink value=$field|cat:"_link"}
            <td>{if $row.$fieldLink}<a href="{$row.$fieldLink}">{/if}
                {if $row.$field eq '&nbsp;&nbsp;&nbsp;Sub Total'}
	    	    {$row.$field}
                {elseif $header.type eq 12}
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
                {if $row.$fieldLink}{/if}
	    </td>
         {/foreach}
      </tr>
      {/foreach}

      {if $grandStat}
      {* foreach from=$grandStat item=row*}
      <tr>
         {foreach from=$columnHeaders item=header key=field}
            <td><strong>
		{if $header.type eq 1024}
	    	    {$grandStat.$field|crmMoney}
	        {else}
	    	    {$grandStat.$field}
		{/if}
		</strong></td>
         {/foreach}
      </tr>
      {* /foreach*}
      {/if}
   </table>

   {if $statistics}
   <br/><table class="form-layout">
      {foreach from=$statistics item=row}
          <tr><td>{$row.title}:&nbsp;{$row.value}</strong></td></tr>
      {/foreach}
   </table>
   {/if}
{/if}