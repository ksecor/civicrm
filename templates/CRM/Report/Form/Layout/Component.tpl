<table class="form-layout">
    <tr>
       	{foreach from=$columnHeadersComponent item=pheader key=component}
	    <tr class="columnheader-dont">
		<th>{$component|upper}</th>
		{foreach from=$pheader item=header}
                    <th>{$header.title}</th>
	        {/foreach}
	    </tr>

	    {foreach from=$componentRows.$component item=row}
            	<tr class="{cycle values="odd-row,even-row"}">
		    <td></td>
		    {foreach from=$columnHeadersComponent.$component item=header key=field}
			{assign var=fieldLink value=$field|cat:"_link"}			
			<td>
			    {if $row.$fieldLink}
				<a href="{$row.$fieldLink}">
			    {/if}
                        
			    {if $row.$field eq 'Sub Total'}
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
        {/foreach}  
    </tr>
</table>
