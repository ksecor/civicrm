{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8 }
    {include file="CRM/UF/Form/Field.tpl"}
{elseif $action eq 1024 }
    {include file="CRM/UF/Form/Preview.tpl"}
{else}
    {if $ufField}
    <div id="field_page">
     <p></p>
        <div class="form-item">
        {strip}
    <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
	<thead>
        <tr class="columnheader">
            <th field="Name" dataType="String">{ts}CiviCRM Field Name{/ts}</th>
            <th field="Visibility" dataType="String">{ts}Visibility{/ts}</th>
            <th field="Searchable" dataType="String">{ts}Searchable?{/ts}</th>
            <th field="Selector" dataType="String">{ts}In Selector?{/ts}</th>
            <th field="Order" dataType="Number" sort="asc">{ts}Order{/ts}</th>
            <th field="Active" dataType="String">{ts}Active{/ts}</th>	
            <th field="Required" dataType="String">{ts}Required{/ts}</th>	
            <th field="View" dataType="String">{ts}View Only{/ts}</th>	
            <th datatype="html"></th>
        </tr>
    </thead>
	
	<tbody>
        {foreach from=$ufField item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}
            {if NOT $row.is_active}disabled{/if}">
            <td>{$row.label}<br/>({$row.field_type})</td>
            <td>{$row.visibility_display}</td>
            <td>{if $row.is_searchable   eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.in_selector     eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.weight}</td>
            <td>{if $row.is_active       eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_required     eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_view         eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
	</tbody>
        </table>
        {/strip}
        
{if not ($action eq 2 or $action eq 1)}
            <div class="action-link">
            <a href="{crmURL p="civicrm/admin/uf/group/field" q="reset=1&action=add&gid=$gid"}">&raquo; {ts}New CiviCRM Profile Field{/ts}</a>
            </div>
{/if}
        </div>
     </div>
    {else}
        {if $action eq 16}
        {capture assign=crmURL}{crmURL p="civicrm/admin/uf/group/field" q="reset=1&action=add&gid=$gid"}{/capture}
        <div class="messages status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$groupTitle 2=$crmURL}There are no CiviCRM Profile Fields for "%1", you can <a href="%2">add one now</a>.{/ts}</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
