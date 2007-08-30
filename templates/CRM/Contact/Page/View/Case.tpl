<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update, delete*}            
    {include file="CRM/Case/Form/Case.tpl"}
{elseif $action eq 4 }
    {include file="CRM/Case/Form/CaseView.tpl"}

{else}
<div id="help">
 
     {capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="reset=1&action=add&cid=`$contactId`&context=case"}{/capture}
     {ts 1=$newCaseURL}Click <a href="%1">Register Case</a> for this contact.{/ts}

</div>

{if $cases}
    <div class="form-item" id=case_page>
    {strip}
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">  
       <thead> 
        <tr class="columnheader">
            <th field="Status" dataType="String">{ts}Case Status{/ts}</th>
            <th field="Type" dataType="String">{ts}Case Type{/ts}</th>
            <th field="Subject" dataType="String">{ts}Subject{/ts}</th>
            <th datatype="html"></th>
            <th field="Start Date" dataType="String">{ts}Start Date{/ts}</th>
            <th datatype="html">&nbsp;</th>
        </tr>
       </thead>
       <tbody> 
        {foreach from=$cases item=case}
        <tr class="{cycle values="odd-row,even-row"}">

            <td>{$case.status_id}</td>
            <td>{$case.casetag1_id.0} <br />{$case.casetag1_id.1}<br />{$case.casetag1_id.2}</td>  
            <td>{$case.subject}</td>
            <td><a href="{crmURL p='civicrm/contact/view/case' q="action=view&selectedChild=case&id=`$case.id`&cid=$contactId"}">(View)</a></td>
            <td>{$case.start_date|crmDate}</td>
            <td >{$case.action}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
    {/strip}


    </div>

{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}No cases have been recorded from this contact.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{/if}
</div>
