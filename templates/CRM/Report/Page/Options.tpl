<div id="help">
    {ts 1=$GName}The existing option choices for %1 group are listed below. You can add, edit or delete them from this screen.{/ts}
</div>
{if $action ne 1 and $action ne 2}
    <div class="action-link">
	<a href="{$newReport}"  id="new"|cat:$GName class="button"><span>&raquo; {ts 1=$GName}Register New %1{/ts}</span></a>
    </div>
    <div class="spacer"></div>
{/if}
{if $rows}
    <div id={$gName}>
	{strip}
	{* handle enable/disable actions*}
 	{include file="CRM/common/enableDisable.tpl"}
 	{include file="CRM/common/jsortable.tpl"}
       <table id="options" class="display">
       <thead>
		<tr>      
		    <th>{ts}Label{/ts}</th>
		    <th>{ts}URL{/ts}</th>   
		    <th id="nosort">{ts}Description{/ts}</th>
		    <th id="order" class="sortable">{ts}Order{/ts}</th>
		    {if $showIsDefault}
		        <th>{ts}Default{/ts}</th>
		    {/if}
		    <th>{ts}Reserved{/ts}</th>
		    <th>{ts}Enabled?{/ts}</th>
		    <th>{ts}Component{/ts}</th>
		    <th></th>
		</tr>
        </thead>
		{foreach from=$rows item=row}
		    <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}{$row.class}{if NOT $row.is_active} disabled{/if}">
 		        <td>{$row.label}</td>	
		        <td>{$row.value}</td>
		        <td>{$row.description}</td>	
		        <td class="nowrap">{$row.order}</td>
		        {if $showIsDefault}
		            <td>{$row.default_value}</td>
		        {/if}
		        <td>{if $row.is_reserved eq 1}{ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
    			<td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	    		<td>{$row.component_name}</td>	
		        <td>{$row.action}</td>
                <td class="order hiddenElement">{$row.weight}</td>
		    </tr>
		{/foreach}
	    </table>
	{/strip}

        {if $action ne 1 and $action ne 2}
            <div class="action-link">
		<a href="{$newReport}"  id="new"|cat:$GName class="button"><span>&raquo; {ts 1=$GName}Register New %1{/ts}</span></a>
            </div>
        {/if}
    </div>
{else}
    <div class="messages status">
	<dl>
	    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>      
	    <dd>{ts 1=$newReport}There are no option values entered. You can <a href="%1">add one</a>.{/ts}</dd>
	</dl>
    </div>    
{/if}