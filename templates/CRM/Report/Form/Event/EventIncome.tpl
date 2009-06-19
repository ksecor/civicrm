{* this div is being used to apply special css *}
<div id="searchForm">   
    {include file="CRM/Report/Form/Fields.tpl"}
    {*Statistics at the Top of the page*}
    {include file="CRM/Report/Form/Statistics.tpl" top=true}    
    
    {if $events}
        <div class="report-pager">
            {include file="CRM/common/pager.tpl" noForm=1}
        </div>
        {foreach from=$events item=eventID}
            <table style="width:100%">
                <tr>
                    <td>    
                	<table class="report-layout" >
                	    {foreach from=$summary.$eventID item=values key=keys}
                	        {if $keys == 'Title'}
                        	    <tr>
                                        <th>{$keys}</th>
                                        <th colspan="3">{$values}</th>
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
                        	    <tr>
                        	        <th width="34%">{ts 1=$keys}%1 Breakdown{/ts}</th>
                                	<th class="reports-header-right">{ts}Total{/ts}</th>
                                        <th class="reports-header-right">{ts}% of Total{/ts}</th>
                                        <th class="reports-header-right">{ts}Revenue{/ts}</th>
                                    </tr>
                                    {foreach from=$row.$eventID item=row key=role}
                                        <tr>
                                            <td class="report-contents" width="34%">{$role}</td>
                                            <td class="report-contents-right">{$row.0}</td>
                                            <td class="report-contents-right">{$row.1}</td>
                                            <td class="report-contents-right">{$row.2|crmMoney}</td>	        
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
