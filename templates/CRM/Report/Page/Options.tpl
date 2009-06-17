<div id="help">
    {ts}The existing option choices for {$GName} group are listed below. You can add, edit or delete them from this screen.{/ts}
</div>

{if $rows}
<div id={$gName}>
        {strip}
        <table class="selector">
        <tr class="columnheader">
      
            <th>{ts}Label{/ts}</th>
            <th>{ts}URL{/ts}</th>   
            <th>{ts}Description{/ts}</th>
            <th>{ts}Order{/ts}</th>
	        {if $showIsDefault}<th>{ts}Default{/ts}</th>{/if}
            <th>{ts}Reserved{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
            <th></th>
            </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"}{$row.class}{if NOT $row.is_active} disabled{/if}">          
	        <td>{$row.label}</td>	
	        <td>{$row.value}</td>
	        <td>{$row.description}</td>	
	        <td class="nowrap">{$row.weight}</td>
            {if $showIsDefault}<td>{$row.default_value}</td>{/if}
	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
            <div class="action-link">
		
                <a href="{$newReport}"  id="new"|cat:$GName class="button"><span>&raquo; {ts}New {$GName}{/ts}</span></a>
            </div>
        {/if}
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
      
        <dd>{ts 1=$crmURL}There are no option values entered. You can <a href="{$newReport}">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
