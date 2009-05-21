<table class="form-layout">
    {foreach from=$summary item=values key=keys}
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
	        <th>{ts}Total{/ts}</th>
	        <th>{ts}% of Total{/ts}</th>
	        <th>{ts}Revenue{/ts}</th>
	    </tr>
	    {foreach from=$row item=row key=role}
		<tr>
		    <td>{$role}</td>
		    <td>{$row.0}</td>
		    <td>{$row.1}</td>
		    <td>{$row.2|crmMoney}</td>	        
		<tr>
	    {/foreach}
	{/if}
    {/foreach}        
</table>