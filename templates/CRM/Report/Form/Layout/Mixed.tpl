{if $events}
{include file="CRM/common/pager.tpl" noForm=1}
{foreach from=$events item=eventID}
    <table class="form-layout" style="width:100%;">
        {foreach from=$summary.$eventID item=values key=keys}
	    {if $keys == 'Title'}
		<tr class="columnheader">
		    <th>{$keys}</th>
		    <th colspan="3">{$values}</th>
		</tr>
	    {else}  
	        <tr>
	            <td>{$keys}</td>
	            <td colspan="3">{$values}</td>
	        </tr>
	    {/if}
	{/foreach}

	{foreach from=$rows item=row key=keys}
	    {if $row}
	        <tr class="columnheader">
	            <th>{ts 1=$keys} %1 Breakdown{/ts}</th>
	            <th class="right">{ts}Total{/ts}</th>
	            <th class="right">{ts}% of Total{/ts}</th>
	            <th class="right">{ts}Revenue{/ts}</th>
	        </tr>
	        {foreach from=$row.$eventID item=row key=role}
		    <tr>
		        <td>{$role}</td>
		        <td class="right">{$row.0}</td>
		        <td class="right">{$row.1}</td>
		        <td class="right">{$row.2|crmMoney}</td>	        
		    <tr>
		{/foreach}
	    {/if}
	{/foreach}        
    </table>
    <table>
        <tr></tr>
    </table>
{/foreach}
{if $statistics}
    <br/>
    <table class="form-layout" style="width:100%;">
        {foreach from=$statistics.counts item=row}
            <tr>
                <td>{$row.title}:&nbsp;<strong>{$row.value}</strong></td>
            </tr>
        {/foreach}
    </table>
{/if}
{/if}
