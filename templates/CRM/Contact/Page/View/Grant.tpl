<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8 or $action eq 4} {* add, update or delete *}
    {include file="CRM/Grant/Form/Grant.tpl"}
{else}
<div id="help">
    {if $permission EQ 'edit'}
     {capture assign=newGrantURL}{crmURL p="civicrm/contact/view/grant" q="reset=1&action=add&cid=`$contactId`&context=grant"}{/capture}
     {ts 1=$newGrantURL}<a href="%1">Add new grant</a> for this contact.{/ts}
    {/if}
</div>

{if $grants}

<div id="grant">
    <div class="form-item" id=grant_id>

    {strip}
       
        <table  dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">   
       <thread> 
        <tr class="columnheader">
            <th field="Status" dataType="String">{ts}Grant Status{/ts}</th>
            <th field="Type" dataType="String">{ts}Grant Type{/ts}</th>
            <th field="Subject" dataType="String">{ts}Amount requested{/ts}</th>
            <th field="Start Date" dataType="String">{ts}Application received date{/ts}</th>
            <th datatype="html"></th>

	        <th scope="col" title="Action Links"></th>
        </tr>
       </thread>
       <tbody> 
        {foreach from=$grants item=grant}
        <tr class="{cycle values="odd-row,even-row"}">

            <td>{$grant.status_id}</td>
            <td>{$grant.grant_type_id}</td>  
            <td>{$grant.amount_requested}</td> 
            <td>{$grant.application_received_date|crmDate}</td>

            <td class="nowrap">{$grant.action}</td>
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
            {ts}No grants have been recorded from this contact.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{/if}
</div>
