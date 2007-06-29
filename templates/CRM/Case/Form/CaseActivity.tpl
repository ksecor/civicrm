{* Template for Case Activity Selector to be shown on Show Case Page *}
        <table  dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">   
       <thread> 
        <tr class="columnheader">
            <th field="ActivityType" dataType="String">{ts}Activity Type{/ts}</th>
            <th field="To" dataType="String">{ts}To{/ts}</th>
            <th field="From" dataType="String">{ts}From{/ts}</th>
            <th field="Regarding" dataType="String">{ts}Regarding{/ts}</th>
            <th field="Subject" dataType="String">{ts}Case{/ts}</th>
            <th field="Type" dataType="String">{ts}Type{/ts}</th>
            <th field="Start Date" dataType="String">{ts}Start Date{/ts}</th>
           
            <th datatype="html"></th>

	        <th scope="col" title="Action Links"></th>
        </tr>
       </thread>
       <tbody> 
       
       {foreach from=$activities item=activity}
        <tr class="{cycle values="odd-row,even-row"}">
        
        <td>{$activity.activity_type}</td>
        <td>{$activity.to_contact}</td>
        <td>{$activity.sourceName}</td>  
        <td>{$activity.targetName}</td> 
        <td>{$form.subject.html}</td>
        <td>{$activity.case_activity_type}</td> 
        <td>{$activity.start_date|crmDate}</td>
         <td class="nowrap">{$activity.action}</td>
        </tr>
       
       {/foreach}
        </tbody>
        </table>