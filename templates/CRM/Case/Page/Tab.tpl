{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="reset=1&action=add&cid=`$contactId`&atype=`$openCaseId`&context=case"}{/capture}

{if $action eq 1 or $action eq 2 or $action eq 8 or $action eq 32768 } {* add, update, delete, restore*}            
    {include file="CRM/Case/Form/Case.tpl"}
{elseif $action eq 4 }
    {include file="CRM/Case/Form/CaseView.tpl"}

{else}
<div class="view-content">
<div id="help">
     {ts 1=$displayName}This page lists all case records for %1.{/ts}
     {if $permission EQ 'edit'}{ts 1=$newCaseURL}Click <a href='%1'>New Case</a> to add a case record for this contact.{/ts}{/if}
</div>

{if $cases}
    {if $action eq 16 and $permission EQ 'edit'}
        <div class="action-link">
        <a accesskey="N" href="{$newCaseURL}" class="button"><span>&raquo; {ts}New Case{/ts}</span></a>
        </div>
        <br /><br />
    {/if}
    <div class="form-item" id=case_page>
    {strip}
        <table class="selector">  
         <tr class="columnheader">
            <th>{ts}Status{/ts}</th>
            <th>{ts}Case Type{/ts}</th>
            <th>{ts}My Role{/ts}</th>
            <th>{ts}Most Recent Activity{/ts}</th>
            <th>{ts}Next Scheduled Activity{/ts}</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$cases item=case}
        <tr class="{cycle values="odd-row,even-row"}{if $case.case_status eq 'Resolved' } disabled{/if}">
            <td>{$case.case_status}</td>
            <td>{$case.case_type}</td>
            <td>{if $case.case_role}{$case.case_role}{else}---{/if}</td>
            <td>{$case.case_recent_activity_type}<br />{$case.case_recent_activity_date |crmDate}</td>
            <td>{$case.case_scheduled_activity_type}<br />{$case.case_scheduled_activity_date |crmDate}</td>
            <td>{$case.action}</td>
        </tr>
        {/foreach}
        </table>
    {/strip}


    </div>

{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}There are no case records for this contact.{/ts}
            {if $permission EQ 'edit'}{ts 1=$newCaseURL}You can <a href='%1'>open one now</a>.{/ts}{/if}
       </dd>
       </dl>
  </div>
{/if}
</div>
{/if}
