{* CiviCase DashBoard (launch page) *}
{if $notConfigured} {* Case types not present. Component is not configured for use. *}
    {include file="CRM/Case/Page/ConfigureError.tpl"}
{else}

{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="action=add&context=case&reset=1"}{/capture}

<div class="float-right">
  <table class="form-layout-compressed">
   {if $newClient}	
    <tr>
      <td>
        <a href="{$newCaseURL}" class="button"><span>&raquo; {ts}New Case for New Client{/ts}</span></a>
      </td>
    </tr>
   {/if}
   {if $myCases}
    <tr>
      <td class="right">
        <a href="{crmURL p="civicrm/case" q="reset=1&all=1"}"><span>&raquo; {ts}Show ALL Cases with Upcoming Activities{/ts}</span></a>
      </td>
    </tr>
   {else}	
    <tr>
      <td class="right">
        <a href="{crmURL p="civicrm/case" q="reset=1&all=0"}"><span>&raquo; {ts}Show My Cases with Upcoming Activities{/ts}</span></a>
      </td>
    </tr>
   {/if}
   <tr>
     <td class="right">
       <a href="{crmURL p="civicrm/case/search" q="reset=1&case_owner=1&force=1"}"><span>&raquo; {ts}Show My Cases{/ts}</span></a>
     </td>
   </tr>
  </table>
</div>

<h3>{ts}Summary of Case Involvement{/ts}</h3>
<table class="report">
  <tr class="columnheader">
    <th>&nbsp;</th>
    {foreach from=$casesSummary.headers item=header}
    <th scope="col" class="right" style="padding-right: 10px;"><a href="{$header.url}">{$header.status}</a></th>
    {/foreach}
  </tr>
  {foreach from=$casesSummary.rows item=row key=caseType}
   <tr>
   <th><strong>{$caseType}</strong></th>
   {foreach from=$casesSummary.headers item=header}
    {assign var="caseStatus" value=$header.status}
    <td class="label">
    {if $row.$caseStatus}
    <a href="{$row.$caseStatus.url}">{$row.$caseStatus.count}</a>
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
    <h3>{if $myCases}{ts}My Cases With Upcoming Activities{/ts}{else}{ts}All Cases With Upcoming Activities{/ts}{/if}</h3>
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
    <h3>{if $myCases}{ts}My Cases With Recently Performed Activities{/ts}{else}{ts}All Cases With Recently Performed Activities{/ts}{/if}</h3>
    {if $recentCases}
    <div class="form-item">
        {include file="CRM/Case/Page/DashboardSelector.tpl" context="dashboard" list="recent" rows=$recentCases}
    </div>
    {else}
        <div class="messages status">
	    {ts 1=$findCasesURL}There are no cases with activities scheduled in the past two weeks. Use %1 to expand your search.{/ts}
        </div>
    {/if}

    {*include activity view js file*}
    {include file="CRM/common/activityView.tpl"}
    <div id="view-activity">
        <div id="activity-content"></div>
    </div>
{/if}