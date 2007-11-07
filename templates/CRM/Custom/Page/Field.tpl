{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Custom/Form/Field.tpl"}
{elseif $action eq 8}
    {include file="CRM/Custom/Form/DeleteField.tpl"}
{elseif $action eq 1024 }
    {include file="CRM/Custom/Form/Preview.tpl"}
{else}
    {if $customField}
    
    <div id="field_page">
     <p></p>
        <div class="form-item">
        {strip}
         <table enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
         <thead> 
        <tr class="columnheader">
            <th field="Field Label" dataType="String">{ts}Field Label{/ts}</th>
            <th field="Data Type"   dataType="String">{ts}Data Type{/ts}</th>
            <th field="Field Type"  dataType="String">{ts}Field Type{/ts}</th>
            <th field="Order"       dataType="Number" sort="asc">{ts}Order{/ts}</th>
            <th field="Req"         dataType="String">{ts}Req?{/ts}</th>
            <th field="Status"      dataType="String">{ts}Status?{/ts}</th>
            <th datatype="html">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$customField item=row}
        <tr class="{cycle values="odd-row,even-row"} {if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
            <td>{$row.data_type}</td>
            <td>{$row.html_type}</td>
            <td>{$row.weight}</td>
            <td>{if $row.is_required eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        {/strip}
        
        <div class="action-link">
            <a href="{crmURL q="reset=1&action=add&gid=$gid"}" id="newCustomField">&raquo; {ts}New Custom Field{/ts}</a>
        </div>

        </div>
     </div>

    {else}
        {if $action eq 16}
        <div class="messages status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/custom/group/field q="action=add&reset=1&gid=$gid"}{/capture}
        <dd>{ts 1=$groupTitle 2=$crmURL}There are no custom fields for custom group "%1", <a href="%2">add one</a>.{/ts}</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
