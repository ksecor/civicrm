{* CiviCase DashBoard (launch page) *}
{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="action=add&context=case&reset=1&atype=`$openCaseId`"}{/capture}

<div class="float-right">
  <table class="form-layout-compressed">
    <tr>
      <td>
        <a href="{$newCaseURL}" class="button"><span>&raquo; {ts}New Case for New Client{/ts}</span></a>
      </td>
    </tr>
   {if $myCases}
    <tr>
      <td class="right">
        <a href="{crmURL p="civicrm/case" q="reset=1&all=1"}"><span>&raquo; {ts}Show ALL Cases{/ts}</span></a>
      </td>
    </tr>
   {else}	
    <tr>
      <td class="right">
        <a href="{crmURL p="civicrm/case" q="reset=1&all=0"}"><span>&raquo; {ts}Show My Cases{/ts}</span></a>
      </td>
    </tr>
   {/if}
  </table>
</div>

<h3>{ts}Summary of Case Involvement{/ts}</h3>
<table class="report">
  <tr class="columnheader-dark">
    <th>&nbsp;</th>
    {foreach from=$casesSummary.headers item=header}
    <th scope="col" class="right" style="padding-right: 10px;">{$header}</th>
    {/foreach}
  </tr>

  {foreach from=$casesSummary.rows item=row}
  <tr>
    <th><strong>{$row.case_type}</strong></td>
    {foreach from=$row.columns item=cell}
    <td class="label">
    {if $cell}
    <a href="{$cell.url}">{$cell.case_count}</a>
    {else}
    0
    {/if}
    </td>
    {/foreach}
  </tr>
{/foreach}
</table>
{capture assign=findCasesURL}<a href="{crmURL p="civicrm/case/search" q="reset=1"}">{ts}Find Cases{/ts}</a>{/capture}

<div class="spacer"></div>
    <h3>{ts}Cases With Upcoming Activities{/ts}</h3>
    {if $upcomingCases}
    <div class="form-item">
        {include file="CRM/Case/Page/DashboardSelector.tpl" context="dashboard" list="upcoming" rows=$upcomingCases}
    </div>
    {else}
        <div class="messages status">
	    {ts 1=$findCasesURL}There are no cases with activities scheduled in the next two weeks. Use %1 to expand your search.{/ts}
        </div>
    {/if}
<div class="spacer"></div>
    <h3>{ts}Cases With Recently Performed Activities{/ts}</h3>
    {if $recentCases}
    <div class="form-item">
        {include file="CRM/Case/Page/DashboardSelector.tpl" context="dashboard" list="recent" rows=$recentCases}
    </div>
    {else}
        <div class="messages status">
	    {ts 1=$findCasesURL}There are no cases with activities scheduled in the past two weeks. Use %1 to expand your search.{/ts}
        </div>
    {/if}