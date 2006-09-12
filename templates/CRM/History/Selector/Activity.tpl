{* Open Activities table and Activity History are toggled on this page for now because we don't have a solution for including 2 'selectors' on one page. *}
{if $history NEQ 1}
    {* Showing Open Activities *}
    {if $totalCountOpenActivity}
        <fieldset><legend><a href="{crmURL p='civicrm/contact/view/activity' q="show=1&action=browse&history=1&cid=$contactId"}"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Open Activities{/ts}</legend>
    {else}
        <div class="data-group">
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
{else}
    {* Showing History *}
    <div id="openActivities_show" class="data-group">
        {if $totalCountOpenActivity}
            <a href="{crmURL p='civicrm/contact/view/activity' q="show=1&action=browse&history=0&cid=$contactId"}"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Open Activities{/ts}</label> ({$totalCountOpenActivity})
        {else}
            <dl><dt>{ts}Open Activities{/ts}</dt>
            {if $permission EQ 'edit'}
                {capture assign=mtgURL}{crmURL p='civicrm/contact/view/activity' q="activity_id=1&action=add&reset=1&cid=$contactId"}{/capture}
                {capture assign=callURL}{crmURL p='civicrm/contact/view/activity' q="activity_id=2&action=add&reset=1&cid=$contactId"}{/capture}
                <dd>{ts 1=$mtgURL 2=$callURL}No open activities. You can schedule a <a href="%1">meeting</a> or a <a href="%2">call</a>.{/ts}</dd>
            {else}
                <dd>{ts}There are no open activities for this contact.{/ts}</dd>
            {/if}
            </dl>
        {/if}
    </div>
    {if $totalCountActivity}
        <fieldset><legend><a href="{crmURL p='civicrm/contact/view/activity' q="show=1&action=browse&history=0&cid=$contactId"}"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Activity History{/ts}</legend>
    {else}
        <div class="data-group">
            <dl><dt>{ts}Activity History{/ts}</dt><dd>{ts}No activity history for this contact.{/ts}</dd></dl>
        </div>
    {/if}
{/if}

{if $rows}
    <form title="activity_pager" action="{crmURL}" method="post">

    {include file="CRM/common/pager.tpl" location="top"}

    {strip}
    <table>
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header}
        <th>
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
        {if $history eq 1}  	
           <tr class="{cycle values="odd-row,even-row"}">
             <td>{$row.activity_type}</td>
             <td>{$row.activity_summary|mb_truncate:33:"...":true}</td>
             <td>{$row.activity_date|crmDate}</td>
             <td>{$row.action}</td>
           </tr>
        {else}
           <tr class="{cycle values="odd-row,even-row"}">
             <td>{$row.activity_type}</td>
             <td>
               <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=`$row.activity_type_id`&action=view&id=`$row.id`&cid=$contactId&history=0"}">{$row.subject|mb_truncate:33:"...":true}</a>
             </td>
             <td>
             {if $contactId  NEQ $row.sourceID} 
                <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.sourceID`"}">{$row.sourceName}</a>
             {else}
                {$row.sourceName}
             {/if}			
             </td>
             <td>
                {if $$contactId NEQ $row.targetID and $contactId  EQ $row.sourceID }
                    <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.targetID`"}">{$row.targetName}</a>
                {else}
                    {$row.targetName} 
                {/if}	
             </td>
             <td>{$row.date|crmDate}</td>
             <td>{$row.status_display}</td>
             <td>{$row.action}</td>
           </tr>
        {/if}

      {/foreach}
    </table>
    {/strip}

    {include file="CRM/common/pager.tpl" location="bottom"}
    </form>
    </fieldset>
{/if}

{if $history NEQ 1}
    {* Showing Open Activities - give link for History toggle *}
    <div id="activityHx_show" class="data-group">
        {if $totalCountActivity}
            <a href="{crmURL p='civicrm/contact/view/activity' q="show=1&action=browse&history=1&cid=$contactId"}"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Activity History{/ts}</label> ({$totalCountActivity})
        {else}
            <dl><dt>{ts}Activity History{/ts}</dt><dd>{ts}No activity history for this contact.{/ts}</dd></dl>
        {/if}
    </div>
{/if}
