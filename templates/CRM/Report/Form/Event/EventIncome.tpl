{* this div is being used to apply special css *}
<div id="searchForm">   
    {include file="CRM/Report/Form/Fields.tpl"}
    {*Statistics at the Top of the page*}
    {include file="CRM/Report/Form/Statistics.tpl" top=true}    
    
    {if $events}
        {include file="CRM/common/pager.tpl" noForm=1}
        {foreach from=$events item=eventID}
            <table style="width:100%">
                <tr>
                    <td>    
                	<table class="report-layout" >
                	    {foreach from=$summary.$eventID item=values key=keys}
                	        {if $keys == 'Title'}
                        	    <tr class="reports-header">
                                        <td class="reports-header"><b>{$keys}</b></td>
                                        <td class="reports-header" colspan="3"><b>{$values}</b></td>
                                    </tr>
                                {else}  
                                    <tr>
                                        <td class="report-contents">{$keys}</td>
                                        <td class="report-contents" colspan="3">{$values}</td>
                                    </tr>
                                {/if}
                            {/foreach}
                        </table>
                        {foreach from=$rows item=row key=keys}
                            <table class="report-layout">
                        	{if $row}
                        	    <tr class="reports-header">
                        	        <td class="reports-header" width="34%"><b>{ts 1=$keys} %1 Breakdown{/ts}</b></td>
                                	<td class="reports-header" width="22%"><b>{ts}Total{/ts}</b></td>
                                        <td class="reports-header" width="22%"><b>{ts}% of Total{/ts}</b></td>
                                        <td class="reports-header" width="22%"><b>{ts}Revenue{/ts}</b></td>
                                    </tr>
                                    {foreach from=$row.$eventID item=row key=role}
                                        <tr>
                                            <td class="report-contents">{$role}</td>
                                            <td class="report-contents">{$row.0}</td>
                                            <td class="report-contents">{$row.1}</td>
                                            <td class="report-contents">{$row.2|crmMoney}</td>	        
                                        </tr>
                                    {/foreach}
                                {/if}
                            </table>
                        {/foreach} 
                    </td>
                </tr>
            </table>       
        {/foreach}
        
        {*Statistics at the bottom of the page*}
        {include file="CRM/Report/Form/Statistics.tpl" bottom=true}    
    {/if}
    
    {include file="CRM/Report/Form/ErrorMessage.tpl"} 
    {* special div where id=searchForm ends *}
</div>
