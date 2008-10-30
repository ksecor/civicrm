{* CiviCase DashBoard (launch page) *}
{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="action=add&atype=13&reset=1"}{/capture}

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
    {foreach from=$caseSummary.headers item=header}
    <th scope="col" class="right" style="padding-right: 10px;">{$header}</th>
    {/foreach}
  </tr>

  {foreach from=$caseSummary.rows item=row}
  <tr>
    <th><strong>{$row.case_type}</strong></td>
    {foreach from=$row.columns item=cell}
    <td class="label"><a href="{$cell.purl}">{$cell.case_count}</a></td>
    {/foreach}
  </tr>
{/foreach}
</table>

<div class="spacer"></div>

{if $pager->_totalItems}
    <h3>{ts}Recent Cases{/ts}</h3>
    <div class="form-item">
        {include file="CRM/Case/Form/Selector.tpl" context="dashboard"}
    </div>
{/if}
