<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 4} {* add, update,View or delete *}            
    {include file="CRM/Case/Form/Case.tpl"}

{else}
<div id="help">
 
     {capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="reset=1&action=add&cid=`$contactId`&context=case"}{/capture}
     {ts 1=$newCaseURL}Click <a href="%1">Register Case</a> for this contact.{/ts}

</div>

{if $cases}
<div id="case">
    <div class="form-item" id=case_id>

    {strip}
       
        <table  dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">   
       <thread> 
        <tr class="columnheader">
            <th field="Status" dataType="String">{ts}Case Status{/ts}</th>
            <th field="Type" dataType="String">{ts}Case Type{/ts}</th>
            <th field="Subject" dataType="String">{ts}Subject{/ts}</th>
            <th field="Start Date" dataType="String">{ts}Start Date{/ts}</th>
            <th datatype="html"></th>

	        <th scope="col" title="Action Links"></th>
        </tr>
       </thread>
       <tbody> 
        {foreach from=$cases item=case}
        <tr class="{cycle values="odd-row,even-row"}">

            <td>{$case.status_id}</td>
            <td>{$case.casetag1_id}</td>  
            <td><a href="{crmURL p='civicrm/contact/view/case' q="action=view&selectedChild=case&id=`$case.id`&cid=$contactId"}">{$case.subject|mb_truncate:33:"...":true}</a></td>

            <td>{$case.start_date|crmDate}</td>

            <td class="nowrap">{$case.action}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
    {/strip}


    </div>
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
