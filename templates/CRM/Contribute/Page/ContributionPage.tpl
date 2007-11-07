{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
{capture assign=newPageURL}{crmURL q='action=add&reset=1'}{/capture}
<div id="help">
    <p>{ts 1="http://wiki.civicrm.org/confluence//x/1Cs" 2=$docURLTitle}CiviContribute allows you to create and maintain any number of Online Contribution Pages. You can create different pages for different programs or campaigns - and customize text, amounts, types of information collected from contributors, etc. (<a href="%1" target="_blank" title="%2">read more...</a>){/ts}</p>
  
    {include file="CRM/Contribute/Form/SearchContribution.tpl"}  
    {if $rows}
    {ts}For existing pages{/ts}:
    <ul class="indented">
    <li>{ts}Click <strong>Configure</strong> to view and modify settings, amounts, and text for existing pages.{/ts}</li>
    <li>{ts}Click <strong>Test-drive</strong> to try out the page in <strong>test mode</strong>. This allows you to go through the full contribution process using a dummy credit card on a test server.{/ts}</li>
    <li>{ts}If your page is enabled, click <strong>Live Page</strong> to view to the page in <strong>live mode</strong>.{/ts}</li>
    </ul>
    <p>{ts 1=$newPageURL}Click <a href="%1">New Contribution Page</a> to create and configure a new online contribution page using the step-by-step wizard.{/ts}</p>
    {/if}
</div>

{if $rows}
    <div class="form-item" id="configure_contribution_page">
        {strip}

        {if NOT ($action eq 1 or $action eq 2) }
        
        <div class="action-link">
        <a href="{$newPageURL}" id="newContributionPage">&raquo;  {ts}New Contribution Page{/ts}</a>
        </div>
        
        {/if}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl} 
        <table  headClass="fixedHeader" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
         <thead> 
          <tr class="columnheader">
            <th field="Title" dataType="String" >{ts}Title{/ts}</th>
            <th field="ID" dataType="String" >{ts}ID{/ts}</th>
            <th field="Status" dataType="String" >{ts}Status?{/ts}</th>
            <th datatype="html">&nbsp;</th>
          </tr>
         </thead>
        <tbody>
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
        </tbody>
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
        {ts 1=$newPageURL}No contribution pages have been created yet. Click <a href="%1">here</a> to create a new contribution page using the step-by-step wizard.{/ts}
    </div>
    {/if}
{/if}
