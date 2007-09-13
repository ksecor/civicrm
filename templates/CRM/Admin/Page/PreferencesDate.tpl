<div id="help">
    {ts}Changing the parameters here globally changes the date parameters for fields in that type across CiviCRM.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/PreferencesDate.tpl"}
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
            <th field="Start" dataType="String">{ts}Start{/ts}</th>
            <th field="End" dataType="String">{ts}End{/ts}</th>
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

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newLocationType">&raquo; {ts}New Location Type{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/locationType' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Location Types entered for this Contact. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
