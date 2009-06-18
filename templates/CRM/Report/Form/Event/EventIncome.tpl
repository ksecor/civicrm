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
                        	           <th width="34%">{$keys} Breakdown</th>
                                	   <th width="22%">Total</th>
                                       <th width="22%">% of Total</th>
                                       <th width="22%">Revenue</th>
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
