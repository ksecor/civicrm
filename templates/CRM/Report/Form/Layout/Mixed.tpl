{if $events}
    {if $printOnly}
        <h1>{$reportTitle}</h1>
    {/if}
    <br/>
    {if $statistics}
    <table class="form-layout" style="width:100%">
        {foreach from=$statistics.filters item=row}
            <tr>
                <td>{$row.title}:&nbsp;<strong>{$row.value}</strong></td>
            </tr>
        {/foreach}
    </table>
    <br/>
{/if}

{include file="CRM/common/pager.tpl" noForm=1}
{foreach from=$events item=eventID}
    <table style="width:100%">
	<tr>
	    <td>    
		<table class="form-layout" style="width:100%">
		    {foreach from=$summary.$eventID item=values key=keys}
		        {if $keys == 'Title'}
			    <tr class="columnheader">
				<th width="34%">{$keys}</th>
				<th colspan="3">{$values}</th>
			    </tr>
			{else}  
			    <tr>
			        <td>{$keys}</td>
			        <td colspan="3">{$values}</td>
			    </tr>
			{/if}
		    {/foreach}
		</table>
		{foreach from=$rows item=row key=keys}
		    <table class="form-layout" style="width:100%">
			{if $row}
			    <tr class="columnheader">
			        <th width="34%" >{ts 1=$keys} %1 Breakdown{/ts}</th>
		    	        <th width="22%" class="right">{ts}Total{/ts}</th>
			        <th width="22%" class="right">{ts}% of Total{/ts}</th>
			        <th width="22%" class="right">{ts}Revenue{/ts}</th>
			    </tr>
			    {foreach from=$row.$eventID item=row key=role}
			        <tr>
		    	            <td >{$role}</td>
			            <td class="right">{$row.0}</td>
			            <td class="right">{$row.1}</td>
			            <td class="right">{$row.2|crmMoney}</td>	        
			        </tr>
			    {/foreach}
			{/if}
		    </table>
		{/foreach} 
	    </td>
        </tr>
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
