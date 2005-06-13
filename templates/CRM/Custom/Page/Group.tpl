{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Custom/Form/Group.tpl"}

{else}
    <div id="help">{ts}Custom Data Groups are used to collect and store additional data not included in the standard CiviCRM forms. You can create one or many groups - each one containing a related set of custom data fields.{/ts}</div>

    {if $rows}
    <div id="notes">
    <p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Group Title{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>{ts}Used For{/ts}</th>
            <th>{ts}Weight{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{if $row.extends eq 'Contact'}{ts}All Contact Types{/ts}{else}{$row.extends}s{/if}</td>
            <td>{$row.weight}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/custom/group' q="action=add&reset=1"}">&raquo;  {ts}New Custom Data Group{/ts}</a>
        </div>
        </p>
        {/if}

        {/strip}
        </div>
    </p>
    </div>
    {else}
       {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="message status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"> &nbsp;
         {ts}No custom data groups have been created yet. You can {/ts}<a href="{crmURL p='civicrm/admin/custom/group' q='action=add&reset=1'}">{ts}add one{/ts}</a>.
       </div>
       {/if}
    {/if}
{/if}
