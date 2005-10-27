{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/Contribute/Form/ContributePage.tpl"}

{else}
    <div id="help">{ts}Contributions Pages are used for creating customized pages for collecting contributions.{/ts}</div>

    {if $rows}
    <div id="notes">
    <p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>{ts}Description{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.name}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.description}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/contribute' q="action=add&reset=1"}">&raquo;  {ts}New Contribution Page{/ts}</a>
        </div>
        </p>
        {/if}

        {/strip}
        </div>
    </p>
    </div>
    {else}
       {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="messages status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"> &nbsp;
         {capture assign=crmURL}{crmURL p='civicrm/contribute' q='action=add&reset=1'}{/capture}
         {ts 1=$crmURL}No contribution pages have been created yet. You can <a href="%1">add one</a>.{/ts}
       </div>
       {/if}
    {/if}
{/if}
