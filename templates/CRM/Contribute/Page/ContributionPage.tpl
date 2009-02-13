{capture assign=newPageURL}{crmURL q='action=add&reset=1'}{/capture}
<div id="help">
    {ts}CiviContribute allows you to create and maintain any number of Online Contribution Pages. You can create different pages for different programs or campaigns - and customize text, amounts, types of information collected from contributors, etc.{/ts} {help id="id-intro"}
</div>

{include file="CRM/Contribute/Form/SearchContribution.tpl"}  
{if NOT ($action eq 1 or $action eq 2) }
    <table class="form-layout-compressed">
    <tr>
        <td><a href="{$newPageURL}" class="button"><span>&raquo; {ts}New Contribution Page{/ts}</span></a></td>
        <td style="vertical-align: top"><a href="{crmURL p="civicrm/admin/pcp" q="reset=1"}">&raquo; {ts}Manage Personal Campaign Pages{/ts}</a></td>
    </tr>
    </table>
{/if}

{if $rows}
    <div id="configure_contribution_page">
        {strip}

        
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl} 
        <table class="selector">
          <tr class="columnheader">
            <th>{ts}Title{/ts}</th>
            <th>{ts}ID{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>&nbsp;</th>
          </tr>
          {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>
               <strong>{$row.title}</strong>
            </td>
            <td>{$row.id}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {/strip}
    </div>
{else}
    {if $isSearch eq 1}
    <div class="status messages">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
            {capture assign=browseURL}{crmURL p='civicrm/contribute/manage' q="reset=1"}{/capture}
            <dd>
                {ts}No available Contribution Pages match your search criteria. Suggestions:{/ts}
                <div class="spacer"></div>
                <ul>
                <li>{ts}Check your spelling.{/ts}</li>
                <li>{ts}Try a different spelling or use fewer letters.{/ts}</li>
                <li>{ts}Make sure you have enough privileges in the access control system.{/ts}</li>
                </ul>
                {ts 1=$browseURL}Or you can <a href='%1'>browse all available Contribution Pages</a>.{/ts}
            </dd>
        </dl>
    </div>
    {else}
    <div class="messages status">
        <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /> &nbsp;
        {ts 1=$newPageURL}No contribution pages have been created yet. Click <a accesskey="N" href='%1'>here</a> to create a new contribution page using the step-by-step wizard.{/ts}
    </div>
    {/if}
{/if}
