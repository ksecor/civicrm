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
        {strip}
         <table class="selector">
         <tr class="columnheader">
            <th>{ts}Field Label{/ts}</th>
            <th>{ts}Data Type{/ts}</th>
            <th>{ts}Field Type{/ts}</th>
            <th>{ts}Order{/ts}</th>
            <th>{ts}Req?{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$customField item=row}
        <tr class="{cycle values="odd-row,even-row"} {if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
            <td>{$row.data_type}</td>
            <td>{$row.html_type}</td>
            <td class="nowrap">{$row.weight}</td>
            <td>{if $row.is_required eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td class="btn-slide" id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        
        <div class="action-link">
            <a href="{crmURL q="reset=1&action=add&gid=$gid"}" id="newCustomField" class="button"><span>&raquo; {ts}New Custom Field{/ts}</span></a>
        </div>
     </div>

    {else}
        {if $action eq 16}
        <div class="messages status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/custom/group/field q="action=add&reset=1&gid=$gid"}{/capture}
        <dd>{ts 1=$groupTitle 2=$crmURL}There are no custom fields for custom group '%1', <a href='%2'>add one</a>.{/ts}</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
