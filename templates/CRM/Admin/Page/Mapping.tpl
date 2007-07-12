<div id="help">
    {ts}Saved mappings allow you to easily run the same import or export job multiple times. Mappings are created and updated as part of an Import or Export task.
    This screen allows you to rename or delete existing mappings.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Admin/Form/Mapping.tpl"}	
{/if}

{if $rows}
<div id="mapping">
<p></p>
    <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead> 
        <tr class="columnheader">
	        <th field="Name" dataType="String" >{ts}Name{/ts}</th>
	        <th field="Description" dataType="String">{ts}Description{/ts}</th>
            <th field="MemberType" dataType="String">{ts}Mapping Type{/ts}</th>
	        <th datatype="html"></th>
        </tr>
        </thead>   
	
	<tbody>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
            <td>{$row.name}</td>	
            <td>{$row.description}</td>
            <td>{$row.mapping_type_display}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
    </tbody>
        </table>
        {/strip}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}There are currently no saved import or export mappings. You create saved mappings as part of an Import or Export task.{/ts}</dd>
        </dl>
    </div>    
{/if}
