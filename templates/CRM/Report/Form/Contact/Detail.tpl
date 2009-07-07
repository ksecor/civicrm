{* this div is being used to apply special css *}
{include file="CRM/Report/Form/Fields.tpl"}
{include file="CRM/Report/Form/Statistics.tpl" top=true}

    {if $rows}
        <div class="report-pager">
            {include file="CRM/common/pager.tpl" noForm=1}
        </div>
        {foreach from=$rows item=row}
            <br />
            <table class="report-layout">
                <tr>
                    <td>
                	<table class="report-layout">
                            <tr>
                                {foreach from=$columnHeaders item=header key=field}
                                    {if !$skip}
                                        {if $header.colspan}
                                            <th colspan={$header.colspan}>{$header.title}</th>
                                            {assign var=skip value=true}
                                            {assign var=skipCount value=`$header.colspan`}
                                            {assign var=skipMade  value=1}
                                        {else}
                                            <th>{$header.title}</th>
                                            {assign var=skip value=false}
                                        {/if}
                                    {else} {* for skip case *}
                                        {assign var=skipMade value=`$skipMade+1`}
                                        {if $skipMade >= $skipCount}{assign var=skip value=false}{/if}
                                    {/if}
                                {/foreach}
                            </tr>               
                            <tr class="group-row">
                                {foreach from=$columnHeaders item=header key=field}
                                    {assign var=fieldLink value=$field|cat:"_link"}
                                    {assign var=fieldHover value=$field|cat:"_hover"}
                                    <td  class="report-contents">
                                        {if $row.$fieldLink}<a title="{$row.$fieldHover}" href="{$row.$fieldLink}">{/if}
                        
                                        {if $row.$field eq 'Subtotal'}
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
                        		<tr>
                        		    {foreach from=$pheader item=header}
                        			<th>{$header.title}</th>
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
            </table>
        {/if}
   
        {*Statistics at the bottom of the page*}
        {include file="CRM/Report/Form/Statistics.tpl" bottom=true}
    {/if} 
{include file="CRM/Report/Form/ErrorMessage.tpl"}
