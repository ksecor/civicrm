{* Displays Activities. *}

<div>
  <fieldset>
  <legend>{ts}Activities{/ts}</legend>

{if $rows}
  <form title="activity_pager" action="{crmURL}" method="post">
  {include file="CRM/common/pager.tpl" location="top"}

  {strip}
    <table class="selector">
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
      <tr class="{cycle values="odd-row,even-row"} {$row.class}">

        <td>{$row.activity_type}</td>
       
    	<td>{$row.subject}</td>
	
        <td>
        {if !$row.source_contact_id}
          <em>n/a</em>
        {elseif $contactId NEQ $row.source_contact_id}
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.source_contact_id`"}" title="{ts}View contact{/ts}">{$row.source_contact_name}</a>
        {else}
          {$row.source_contact_name}	
        {/if}			
        </td>

        <td>
        {if $row.mailingId}
          <a href="{$row.mailingId}" title="{ts}View Mailing Report{/ts}">{$row.recipients}</a>
        {elseif $row.recipients}
          {$row.recipients}
        {elseif !$row.target_contact_name}
          <em>n/a</em>
        {elseif $row.target_contact_name}
            {assign var="showComma" value=0}
            {foreach from=$row.target_contact_name item=targetName key=targetID}
                {if $contactId NEQ $targetID}
                    {if $showComma},&nbsp;{/if}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$targetID`"}" title="{ts}View contact{/ts}">"{$targetName}"</a>
                    {assign var="showComma" value=1}
                {/if}
            {/foreach}
        {else}
          {$row.target_contact_name}
        {/if}
        </td>

        <td>
        {if !$row.assignee_contact_name}
            <em>n/a</em>
        {elseif $row.assignee_contact_name}
            {assign var="showComma" value=0}
            {foreach from=$row.assignee_contact_name item=assigneeName key=assigneeID}
                {if $contactId NEQ $assigneeID}
                    {if $showComma},&nbsp;{/if}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$assigneeID`"}" title="{ts}View contact{/ts}">"{$assigneeName}"</a>
                    {assign var="showComma" value=1}
                {/if}
            {/foreach}
        {else}
            {$row.assignee_contact_name}
        {/if}	
        </td>

        <td>{$row.activity_date_time|crmDate}</td>

        <td>{$row.status}</td>

        <td>{$row.action|replace:'xx':$row.id}</td>
      </tr>
      {/foreach}

    </table>
  {/strip}

  {include file="CRM/common/pager.tpl" location="bottom"}
  </form>

{else}

  <div class="messages status">
    {if isset($caseview) and $caseview}
      {ts}There are no Activities attached to this case record.{/ts}{if $permission EQ 'edit'} {ts}You can go to the Activities tab to create or attach activity records.{/ts}{/if}
    {elseif $context eq 'home'}
      {ts}There are no Activities to display.{/ts}
    {else}
      {ts}There are no Activites to display.{/ts}{if $permission EQ 'edit'} {ts}You can use the links above to schedule or record an activity.{/ts}{/if}
    {/if}
  </div>

{/if}

</fieldset>
</div>

