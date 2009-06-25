{if (!$chartEnabled || !$chartSupported )&& $rows}
    <div class="report-pager">
        {include file="CRM/common/pager.tpl" noForm=1}
    </div>
    <br/>
    <table class="report-layout">
        <tr>
            {foreach from=$columnHeaders item=header key=field}
                {assign var=class value=""}
                {if $header.type eq 1024 OR $header.type eq 1}
        		    {assign var=class value="class='reports-header-right'"}
                {else}
                    {assign var=class value="class='reports-header'"}
                {/if}
                {if !$skip}
                   {if $header.colspan}
                       <th colspan={$header.colspan}>{$header.title}</th>
                      {assign var=skip value=true}
                      {assign var=skipCount value=`$header.colspan`}
                      {assign var=skipMade  value=1}
                   {else}
                       <th {$class}>{$header.title}</th> 
                   {assign var=skip value=false}
                   {/if}
                {else} {* for skip case *}
                   {assign var=skipMade value=`$skipMade+1`}
                   {if $skipMade >= $skipCount}{assign var=skip value=false}{/if}
                {/if}
            {/foreach}
        </tr>
        
        {foreach from=$rows item=row}
            <tr>
                {foreach from=$columnHeaders item=header key=field}
                    {assign var=fieldLink value=$field|cat:"_link"}
                    {assign var=fieldHover value=$field|cat:"_hover"}
                    <td {if $header.type eq 1024 OR $header.type eq 1} class="report-contents-right"{elseif $row.$field eq 'Subtotal'} class="report-label"{/if}>
                        {if $row.$fieldLink}
                            <a title="{$row.$fieldHover}" href="{$row.$fieldLink}">
                        {/if}
                        
                        {if $row.$field eq 'Subtotal'}
                            {$row.$field}
                        {elseif $header.type & 4}
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
        
        {if $grandStat}
            {* foreach from=$grandStat item=row*}
            <tr>
                {foreach from=$columnHeaders item=header key=field}
                    <td class="report-label">
                        {if $header.type eq 1024}
                            {$grandStat.$field|crmMoney}
                        {else}
                            {$grandStat.$field}
                        {/if}
                    </td>
                {/foreach}
            </tr>
            {* /foreach*}
        {/if}
    </table>
{/if}        