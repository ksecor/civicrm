{* Template for Case Activity Selector to be shown on Show Case Page *}
        <table cellpadding="0" cellspacing="0" border="0">   
        <tr class="columnheader">
            <th field="ActivityType" dataType="String">{ts}Activity Type{/ts}</th>
            <th>{ts}To{/ts}</th>
            <th>{ts}From{/ts}</th>
            <th>{ts}Regarding{/ts}</th>
            <th>{ts}Case{/ts}</th>
            <th>{ts}Type{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th></th>
	        <th></th>
        </tr>
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
        </table>