{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Custom/Form/Group.tpl"}
{elseif $action eq 1024}
    {include file="CRM/Custom/Form/Preview.tpl"}
{elseif $action eq 8}
    {include file="CRM/Custom/Form/DeleteGroup.tpl"}
{else}
    <div id="help">
    {ts}Custom data is stored in custom fields. Custom fields are organized into logically related custom data groups (e.g. Volunteer Info). Use custom fields to collect and store custom data which is not included in the standard CiviCRM forms. You can create one or many groups of custom fields.{/ts} {docURL page="Custom Data Fields & Custom Data Groups Admin"}
    </div>

    {if $rows}
    <div id="custom_group">
    <p></p>
        {strip}
        <table class="selector">
        <tr class="columnheader">
            <th>{ts}Group Title{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>{ts}Used For{/ts}</th>
            <th>{ts}Type{/ts}</th>
            <th>{ts}Order{/ts}</th>
            <th>{ts}Style{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{if $row.is_active       eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{if $row.extends eq 'Contact'}{ts}All Contact Types{/ts}{else}{$row.extends_display}{/if}</td>
            <td>{$row.extends_entity_column_value}</td>
            <td class="nowrap">{$row.weight}</td>
            <td>{$row.style_display}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/custom/group' q="action=add&reset=1"}" id="newCustomDataGroup" class="button"><span>&raquo;  {ts}New Group of Custom Fields{/ts}</span></a>
        </div>
        {/if}

        {/strip}
    </div>
    {else}
       {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="messages status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/> &nbsp;
         {capture assign=crmURL}{crmURL p='civicrm/admin/custom/group' q='action=add&reset=1'}{/capture}
         {ts 1=$crmURL}No custom data groups have been created yet. You can <a href='%1'>add one</a>.{/ts}
       </div>
       {/if}
    {/if}
{/if}
