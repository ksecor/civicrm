{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Custom/Form/Group.tpl"}
{elseif $action eq 1024}
    {include file="CRM/Custom/Form/Preview.tpl"}
{elseif $action eq 8}
    {include file="CRM/Custom/Form/DeleteGroup.tpl"}
{else}
    <div id="help">{ts}Custom Data Groups are used to collect and store additional data not included in the standard CiviCRM forms. You can create one or many groups - each one containing a related set of custom data fields.{/ts}</div>

    {if $rows}
    {include file="CRM/common/dojo.tpl"}    
    <div id="custom_group">
    <p></p>
        <div class="form-item">
        {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr class="columnheader">
            <th field="Group Title" dataType="String">{ts}Group Title{/ts}</th>
            <th field="Status"      dataType="String">{ts}Status?{/ts}</th>
            <th field="Used For"    dataType="String">{ts}Used For{/ts}</th>
            <th field="Type"        dataType="String">{ts}Type{/ts}</th>
            <th field="Weight" dataType="Number" sort="asc">{ts}Weight{/ts}</th>
            <th field="Style"       dataType="String">{ts}Style{/ts}</th>
            <th datatype="html"></th>
        </tr>
        </thead>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{if $row.extends eq 'Contact'}{ts}All Contact Types{/ts}{else}{$row.extends_display}{/if}</td>
            <td>{$row.extends_entity_column_value}</td>
            <td>{$row.weight}</td>
            <td>{$row.style_display}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/custom/group' q="action=add&reset=1"}" id="newCustomDataGroup">&raquo;  {ts}New Custom Data Group{/ts}</a>
        </div>
        {/if}

        {/strip}
        </div>
    </div>
    {else}
       {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="messages status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/> &nbsp;
         {capture assign=crmURL}{crmURL p='civicrm/admin/custom/group' q='action=add&reset=1'}{/capture}
         {ts 1=$crmURL}No custom data groups have been created yet. You can <a href="%1">add one</a>.{/ts}
       </div>
       {/if}
    {/if}
{/if}
