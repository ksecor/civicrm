{* CiviCase DashBoard (launch page) *}
{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="action=add&context=case&reset=1&atype=`$openCaseId`"}{/capture}

<div class="float-right">
  <table class="form-layout-compressed">
    <tr>
      <td>
        <a href="{crmURL p="civicrm/case" q="reset=1"}" class="button"><span>&raquo; {ts}Show ALL Cases{/ts}</span></a><br />
      </td>
    </tr><tr>
      <td>
        <a href="{$newCaseURL}" class="button"><span>&raquo; {ts}New Case for New Client{/ts}</span></a>
      </td>
    </tr>
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

<div class="spacer"></div>
    <h3>{ts}Cases With Upcoming Activities{/ts}</h3>
    {if $upcomingCases}
    <div class="form-item">
        {include file="CRM/Case/Page/DashboardSelector.tpl" context="dashboard" list="upcoming" rows=$upcomingCases}
    </div>
    {else}
        <div class="messages status">
	    {ts}There are no cases with activities scheduled in the next two weeks. Use Find Cases to expand your search.{/ts}
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
	    {ts}There are no cases with activities scheduled in the past two weeks. Use Find Cases to expand your search.{/ts}
        </div>
    {/if}