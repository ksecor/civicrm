{if $rows}
    {if $printOnly}
        <h1>{$reportTitle}</h1>
    {/if}
    <br/>
    {if $statistics}
        <table class="report-layout">
            {foreach from=$statistics.groups item=row}
                <tr class="reports-header">
                    <td class="reports-header" width="5%">{$row.title}</td>
                    <td class="reports-header"><strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
            {foreach from=$statistics.filters item=row}
                <tr>
                    <td width="5%">{$row.title}</td>
                    <td><strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
        </table>
        <br/>
    {/if}
    {include file="CRM/common/pager.tpl" noForm=1}
   
    {foreach from=$rows item=row}
    <br />
    <table style = "width : 100%;">
	<tr>
	    <td>
		<table class="report-layout">
		    <tr class="reports-header">
			{foreach from=$columnHeaders item=header key=field}
			    {if !$skip}
				{if $header.colspan}
				    <td class="reports-header" colspan={$header.colspan}>{$header.title}</td>
				    {assign var=skip value=true}
				    {assign var=skipCount value=`$header.colspan`}
				    {assign var=skipMade  value=1}
				{else}
				    <td class="reports-header"><b>{$header.title}</b></td>
				    {assign var=skip value=false}
				{/if}
			    {else} {* for skip case *}
				{assign var=skipMade value=`$skipMade+1`}
				{if $skipMade >= $skipCount}{assign var=skip value=false}{/if}
			    {/if}
			{/foreach}
		    </tr>               
		    <tr>
		        {foreach from=$columnHeaders item=header key=field}
		            {assign var=fieldLink value=$field|cat:"_link"}
			        {assign var=fieldHover value=$field|cat:"_hover"}
			    <td  class="report-contents">
				{if $row.$fieldLink}<a title="{$row.$fieldHover}" href="{$row.$fieldLink}">{/if}
                        
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
				
				{if $row.contactID} {/if}
				
				{if $row.$fieldLink}</a>{/if}
			    </td>
			{/foreach}
		    </tr>
		</table>

    		{if $columnHeadersComponent}
		    {*include file="CRM/Report/Form/Layout/Component.tpl"  contactID=$row.contactID*}
		    {assign var=contribMode value=$row.contactID}
		    {foreach from=$columnHeadersComponent item=pheader key=component}
                        {if $componentRows.$contribMode.$component}
		            <u><strong>{$component|upper}</strong></u>
                        {/if}
			<table class="report-layout">
			    {*add space before headers*}
			    {if $componentRows.$contribMode.$component}
				<tr class="reports-header">
				    {foreach from=$pheader item=header}
					<td class="reports-header"><b>{$header.title}</td>
				    {/foreach}
				</tr>
			    {/if}
                             
			    {foreach from=$componentRows.$contribMode.$component item=row}
				<tr>
				    {foreach from=$columnHeadersComponent.$component item=header key=field}
					{assign var=fieldLink value=$field|cat:"_link"}
                    {assign var=fieldHover value=$field|cat:"_hover"}
					<td class="report-contents">
					    {if $row.$fieldLink}
						<a title="{$row.$fieldHover} "href="{$row.$fieldLink}">
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
                        
					    {if $row.$fieldLink}</a>{/if}
					</td>
				    {/foreach}
				</tr>
			    {/foreach}
			</table>	
		    {/foreach}
		{/if}
	    </td>
	</tr>
    </table> 
    {/foreach}
    
    <br />
    {if $grandStat}
    <table class="report-layout">
        {* foreach from=$grandStat item=row*}
        <tr>
            {foreach from=$columnHeaders item=header key=field}
                <td>
                    <strong>
                        {if $header.type eq 1024}
                            {$grandStat.$field|crmMoney}
                        {else}
                            {$grandStat.$field}
                        {/if}
                    </strong>
                </td>
            {/foreach}
        </tr>
        {*/foreach*}
    </table>
    {/if}

    {if $statistics}
        <br/>
        <table class="report-layout">
            {foreach from=$statistics.counts item=row}
                <tr>
                    <td class="report-contents">{$row.title}</td>
                    <td class="report-contents"><strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
        </table>
    {/if}
{/if}        