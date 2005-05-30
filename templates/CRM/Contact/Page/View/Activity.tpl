{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Activity/Form/Activity.tpl"}
{else}
    {if $activity}
    <div id="notes">
    <p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Activity Type{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th>{ts}Activity Date{/ts}</th>
        </tr>
        {foreach from=$activity item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            <td>{$row.activity_type}</td>
            <td>{$row.activity_summary}</td>
            <td>{$row.activity_date|crmDate}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/contact/view/activity' q="action=add"}">&raquo;  {ts}New Activity{/ts}</a>
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
