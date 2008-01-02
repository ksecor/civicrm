<div class="view-content">

{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="reset=1&action=add&cid=`$contactId`&context=case"}{/capture}

{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update, delete*}            
    {include file="CRM/Case/Form/Case.tpl"}
{elseif $action eq 4 }
    {include file="CRM/Case/Form/CaseView.tpl"}

{else}
<div id="help">
     {ts 1=$displayName 2=$newCaseURL}This page lists all case records for %1. Click <a href="%2">Add New Case</a> to register new case record for this contact.{/ts}
</div>

{if $cases}
    <div class="form-item" id=case_page>
    {strip}
        <table >  
       <thead> 
        <tr class="columnheader">
            <th>{ts}Case Status{/ts}</th>
            <th>{ts}Case Type{/ts}</th>
            <th>{ts}Subject{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>&nbsp;</th>
        </tr>
       </thead>
       <tbody> 
        {foreach from=$cases item=case}
        <tr class="{cycle values="odd-row,even-row"}">
            <td>{$case.status_id}</td>
            <td>{$case.case_type_id}</td>  
            <td><a href="{crmURL p='civicrm/contact/view/case' q="action=view&selectedChild=case&id=`$case.id`&cid=$contactId"}">{$case.subject}</a></td>
            <td>{$case.start_date|crmDate}</td>
            <td>{$case.action}</td>
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
            {ts 1=$newCaseURL}There are no case records for this contact. You can <a href="%1">enter one now</a>.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{/if}
</div>
