<div id="help">
    {ts}Changing the parameters here globally changes the date parameters for fields in that type across CiviCRM.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/PreferencesDate.tpl"}
{/if}

<div id="preferencesDate">
<p></p>
    <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead> 
        <tr class="columnheader">
            <th field="Class" dataType="String" >{ts}Date Class{/ts}</th>
            <th field="Start" dataType="String">{ts}Start Offset{/ts}</th>
            <th field="End" dataType="String">{ts}End Offset{/ts}</th>
            <th field="Format" dataType="String">{ts}Format{/ts}</th>
            <th field="Minute" dataType="String">{ts}Minute Increment{/ts}</th>
            <th datatype="html"></th>
        </tr>
        </thead>  
 
	<tbody>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	        <td>{$row.name}</td>	
	        <td>{$row.start}</td>	
	        <td>{$row.end}</td>	
	        <td>{$row.format}</td>	
	        <td>{$row.minute_increment}</td>	
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
	<tbody>
        </table>
        {/strip}
</div>
