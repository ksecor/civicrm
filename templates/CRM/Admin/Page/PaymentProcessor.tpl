<div id="help">
    {ts}Payment Processor configurations for all payment processors that can be used in this install of CiviCRM.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/PaymentProcessor.tpl"}
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead> 
        <tr class="columnheader">
            <th field="Name" dataType="String" >{ts}Name{/ts}</th>
            <th field="Processor Type" dataType="String">{ts}Processor Type{/ts}</th>
            <th field="Description" dataType="String">{ts}Description{/ts}</th>
            <th field="Enabled" dataType="String">{ts}Enabled?{/ts}</th>
	        <th field="Default" dataType="String">{ts}Default?{/ts}</th>
            <th datatype="html"></th>
        </tr>
        </thead>  
 
	<tbody>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
	        <td>{$row.processor_class_name}</td>	
            <td>{$row.description}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
		<td>{if $row.is_default eq 1} [X] {else}  {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
	<tbody>
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newPaymentProcessor">&raquo; {ts}New Payment Processor{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{elseif $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/paymentProcessor' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Payment Processors entered. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
