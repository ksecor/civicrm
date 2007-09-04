{* Open Activities table and Activity History are toggled on this page for now because we don't have a solution for including 2 'selectors' on one page. *}

<div>
  <fieldset>
  <legend>{ts}Open Activities{/ts}</legend>




    {if $totalCountOpenActivity and $caseview NEQ 1}

        
    
    {elseif !$totalCountOpenActivity and $caseview NEQ 1}
        <div>
        <dl><dt>{ts}Open Activities{/ts}</dt>
        {if $permission EQ 'edit'}
            {capture assign=mtgURL}{crmURL p='civicrm/contact/view/activity' q="activity_id=1&action=add&reset=1&cid=$contactId"}{/capture}
            {capture assign=callURL}{crmURL p='civicrm/contact/view/activity' q="activity_id=2&action=add&reset=1&cid=$contactId"}{/capture}
            <dd>{ts 1=$mtgURL 2=$callURL}No open activities. You can schedule a <a href="%1">meeting</a> or a <a href="%2">call</a>.{/ts}</dd>
        {else}
            <dd>{ts}There are no open activities for this contact.{/ts}</dd>
        {/if}
        </dl>
        </div>
    {/if}

{if $rows}
  <form title="activity_pager" action="{crmURL}" method="post">
  {include file="CRM/common/pager.tpl" location="top"}

  {strip}
    <table border="1">
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header}
        <th scope="col">
        {if $header.sort}
          {assign var='key' value=$header.sort}
          {$sort->_response.$key.link}
        {else}
          {$header.name}
        {/if}
        </th>
      {/foreach}
      </tr>

      {counter start=0 skip=1 print=false}
      {foreach from=$rows item=row}
      <tr class="{cycle values="odd-row,even-row"}">

        <td>{$row.activity_type}</td>

        <td><a href="{crmURL p='civicrm/contact/view/case' 
                             q="action=view&selectedChild=case&id=`$row.case_id`&cid=`$row.sourceID`"}">
                             {$row.case}</a>
        </td>

        <td><a href="{$viewURL}">{$row.subject}</td></a>
      
        <td>
        {if !$row.source_contact_id}
	  <em>n/a</em>
	{elseif $contactId NEQ $row.source_contact_id}
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.source_contact_id`"}">{$row.source_contact_name}</a>
        {else}
          {$row.source_contact_name}
        {/if}			
        </td>

        <td>
        {if !$row.target_contact_id}
          <em>n/a</em>
        {elseif $contactId NEQ $row.target_contact_id}
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.target_contact_id`"}">{$row.target_contact_name}</a>
        {else}
          {$row.target_contact_name}
        {/if}			
        </td>

        <td>
        {if !$row.assignee_contact_id}
	  <em>n/a</em>
        {elseif $contactId NEQ $row.assignee_contact_id}
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.assignee_contact_id`"}">{$row.assignee_contact_name}</a>
        {else}
          {$row.assignee_contact_name}
        {/if}			
        </td>

        <td>{$row.activity_date_time|crmDate}</td>

        <td>{$row.action}</td>    
      </tr>
      {/foreach}

    </table>
  {/strip}

  {include file="CRM/common/pager.tpl" location="bottom"}
  </form>


</fieldset>
</div>

{else}

  <dl>{ts}No Activites Recorded for this case.{/ts} 
  <a href="{crmURL p='civicrm/contact/view/activity/' 
                   q="activity_id=5&action=add&reset=1&context=case&caseid=`$caseId`&cid=`$contactId`"}">
                   {ts}Record a new Activity.{/ts}</a>
  </dl>

{/if}