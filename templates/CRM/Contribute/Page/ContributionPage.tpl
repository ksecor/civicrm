{capture assign=newPageURL}{crmURL p='civicrm/admin/contribute' q='action=add&reset=1'}{/capture}
<div id="help">
    <p>{ts}CiviContribute allows you to create and maintain any number of Online Contribution Pages. You can create different pages for different programs or campaigns - and customize text, amounts, types of information collected from contributors, etc.{/ts}</p>
    
    {if $rows}
    {ts}For existing pages{/ts}:
    <ul class="indented">
    <li>{ts}Click the <strong>title</strong> to go to the live page (enabled pages only).{/ts}</li>
    <li>{ts}Click <strong>Configure</strong> to view and modify settings, amounts, and text for existing pages.{/ts}</li>
    <li>{ts}Click <strong>Test-drive</strong> to try out the page in <strong>test mode</strong>. This allows you to go through the full contribution process using a dummy credit card on a test server.{/ts}</li>
    </ul>
    <p>{ts 1=$newPageURL}Click <a href="%1">New Contribution Page</a> to create and configure a new online contribution page using the step-by-step wizard.{/ts}</p>
    {/if}
</div>

{if $rows}
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Title{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>
                {if $row.is_active eq 1}<a href="{crmURL p="civicrm/contribute/transact" q="reset=1&id=`$row.id`"}">{$row.title}</a>
                {else}<strong>{$row.title}</strong>{/if}
            </td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        
        <div class="action-link">
        <a href="{$newPageURL}" id="newContributionPage">&raquo;  {ts}New Contribution Page{/ts}</a>
        </div>
        
        {/if}

        {/strip}
    </div>
{else}
    <div class="messages status">
        <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /> &nbsp;
        {ts 1=$newPageURL}No contribution pages have been created yet. Click <a href="%1">here</a> to create a new contribution page using the step-by-step wizard.{/ts}
    </div>
{/if}
