{if $rows}
    {if $printOnly}
        <h1>{$reportTitle}</h1>
    {/if}
    <br/>
    {if $statistics}
        <table class="form-layout">
            {foreach from=$statistics.groups item=row}
                <tr>
                    <td>{$row.title}:&nbsp;<strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
            {foreach from=$statistics.filters item=row}
                <tr>
                    <td>{$row.title}:&nbsp;<strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
        </table>
        <br/>
    {/if}
    {include file="CRM/common/pager.tpl" noForm=1}
    <table class="form-layout">
        <tr class="columnheader">
            {foreach from=$columnHeaders item=header key=field}
                {assign var=class value=""}
                {if $header.type eq 1024 OR $header.type eq 1}
		    {assign var=class value="class='right'"}
		{/if}
                {if !$skip}
                   {if $header.colspan}
                      <th {$class}colspan={$header.colspan}>{$header.title}</th>
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
            <tr class="{cycle values="odd-row,even-row"}">
                {foreach from=$columnHeaders item=header key=field}
                    {assign var=fieldLink value=$field|cat:"_link"}
                    <td {if $header.type eq 1024 OR $header.type eq 1}class="right"{/if}>
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
                        
                        {if $row.$fieldLink}</a>{/if}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
        
        {if $grandStat}
            {* foreach from=$grandStat item=row*}
            <tr>
                {foreach from=$columnHeaders item=header key=field}
                    <td class="right">
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
            {* /foreach*}
        {/if}
    </table>

    {if $statistics}
        <br/>
        <table class="form-layout">
            {foreach from=$statistics.counts item=row}
                <tr>
                    <td>{$row.title}:&nbsp;<strong>{$row.value}</strong></td>
                </tr>
            {/foreach}
        </table>
    {/if}
{else}
   {if $outputMode eq 'html'}
   <div class="messages status">
      <dl>
         <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
         <dd>{ts}Sorry. No reults found.{/ts}</dd>
      </dl>
   </div>
   {/if}
{/if}        