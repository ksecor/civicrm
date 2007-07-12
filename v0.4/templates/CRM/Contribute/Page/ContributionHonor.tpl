{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Contribute/Form/Contribution.tpl"}
{elseif $action eq 4}
    {include file="CRM/Contribute/Form/ContributionView.tpl"}
{/if}
{if $honorRows}
 <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>    
       	  <tr class="columnheader">
	    <th datatype="html" field="Contributor">{ts}Contributor{/ts}</th> 
            <th field="Total Amount">{ts}Amount{/ts}</th>
	    <th field="Contribution Type">{ts}Type{/ts}</th>
            <th field="Source">{ts}Source{/ts}</th>
            <th field="Received">{ts}Received{/ts}</th>
            <th field="Contribution Status">{ts}Status{/ts}</th>
            <th datatype="html">&nbsp;</th>   
          </tr>
        </thead>
        <tbody>
	{foreach from=$honorRows item=row}
	   <tr id='rowid{$row.honorId}' class="{cycle values="odd-row,even-row"}">
	   <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.honorId`"}" id="view_contact">{$row.display_name}</a></td>
	   <td>{$row.amount|crmMoney}</td>
           <td>{$row.type}</td>
           <td>{$row.source}</td>
           <td>{$row.receive_date|truncate:10:''|crmDate}</td>
           <td>{$row.contribution_status}</td>
	   <td>{$row.action}</td>
	  </tr>
        {/foreach}
        </tbody>
	</table>
	{/strip}
       </div>
{/if}

