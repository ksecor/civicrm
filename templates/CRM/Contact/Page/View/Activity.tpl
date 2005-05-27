{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Activity/Form/Activity.tpl"}
{else}
    {if $rows}
    <div id="notes">
    <p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Form Title{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>{ts}Used For{/ts}</th>
            <th>{ts}Weight{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.extends}</td>
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
       <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
         {ts}No activities created yet. You can {/ts}<a href="{crmURL p='civicrm/contact/view/activity' q='action=add'}">{ts}add one{/ts}</a>.
       </div>
       {/if}
    {/if}
{/if}
