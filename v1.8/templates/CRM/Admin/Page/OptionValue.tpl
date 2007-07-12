{* Admin page for browsing Option Group value*}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/OptionValue.tpl"}
{else}
<div id="help">
    {ts}The existing option choices for this option group are listed below. You can add, edit or delete them from this screen.{/ts}
</div>
{/if}

{if $rows}

<div id="browseValues">
    <div class="form-item">
        {strip}
         <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr class="columnheader">
            <th field="Title" dataType="String">{ts}Title{/ts}</th>
            <th field="Value" dataType="String">{ts}Value{/ts}</th>
            <th field="Description" dataType="String">{ts}Description{/ts}</th>
            <th field="Weight" dataType="Number" sort="asc">{ts}Weight{/ts}</th>
            <th field="Default"  dataType="String">{ts}Default{/ts}</th>
            <th field="Reserved" dataType="String">{ts}Reserved?{/ts}</th>
            <th field="Enabled" dataType="String">{ts}Enabled?{/ts}</th>
            <th datatype="html"></th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
	        <td>{$row.value}</td>	
	        <td>{$row.description}</td>
            <td>{$row.weight}</td>
            <td>{$row.default_value}</td>
	        <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1&gid=$gid"}" id="newOptionValue">&raquo; {ts}New Option Value{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{elseif $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/optionValue' q="action=add&reset=1&gid=$gid"}{/capture}
        <dd>{ts 1=$crmURL}There are no option choices entered for this option group. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
