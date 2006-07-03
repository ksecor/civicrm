{assign var=i value=0}
{foreach from=$rows item=ro}
<fieldset>
<div><label class="font-size12pt">{$display_name[$i]}</label></div>

{* Open Activities table and Activity History are toggled on this page for now because we don't have a solution for including 2 'selectors' on one page. *}
{if $history[$i] NEQ 1}
    {* Showing Open Activities *}
    {if $totalCountOpenActivity[$i]}
        <fieldset><legend><a href="{crmURL p='civicrm/activityView' q="show=1&action=browse&history=1&cid=`$contactId[$i]`"}"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Scheduled Activities{/ts}</legend>
    {else}
        <div class="data-group">
        <dl><dt>{ts}Scheduled Activities{/ts}</dt>
            <dd>{ts}No activities currently scheduled.{/ts}</dd>
        </dl>
        </div>
    {/if}
{else}
    {* Showing History *}
    <div id="openActivities[show]" class="data-group">
        {if $totalCountOpenActivity[$i]}
            <a href="{crmURL p='civicrm/activityView' q="show=1&action=browse&history=0&cid=`$contactId[$i]`"}"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Open Activities{/ts}</label> ({$totalCountOpenActivity[$i]})
        {else}
            <dl><dt>{ts}Open Activities{/ts}</dt>
            {if $permission EQ 'view'}
                {capture assign=mtgURL}{crmURL p='civicrm/activityView' q="activity_id=1&action=add&reset=1&cid=`$contactId[$i]`"}{/capture}
                {capture assign=callURL}{crmURL p='civicrm/activityView' q="activity_id=2&action=add&reset=1&cid=`$contactId[$i]`"}{/capture}
                <dd>{ts 1=$mtgURL 2=$callURL}No open activities.{/ts}</dd>
            {else}
                {ts}There are no open activities.{/ts}
            {/if}
            </dl>
        {/if}
    </div>
    {if $totalCountActivity[$i] gt 0}
        <fieldset><legend><a href="{crmURL p='civicrm/activityView' q="show=1&action=browse&history=0&cid=$contactID"}"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Activity History{/ts}</legend>
    {else}
        <div class="data-group">
            <dl><dt>{ts}Activity History{/ts}</dt><dd>{ts}No activity history.{/ts}</dd></dl>
        </div>
    {/if}
{/if}

{if $ro}
 <form title="activity_pager" action="{crmURL}" method="post">

    {include file="CRM/pager.tpl" location="top"}

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
     {foreach from=$ro item=row}  
        {if $history[$i] eq 1}
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
               {$row.subject|mb_truncate:33:"...":true}
             </td>
             <td>
             {if $contactId[$i]  NEQ $row.sourceID} 
               {$row.sourceName}
             {else}
                {$row.sourceName}
             {/if}			
             </td>
             <td>
                {if $contactId[$i] NEQ $row.targetID and $contactId[$i]  EQ $row.sourceID }
                 {$row.targetName}
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

    {include file="CRM/pager.tpl" location="bottom"}
  </form>
    </fieldset>
{/if}

{if $history[$i] NEQ 1}
    {* Showing Open Activities - give link for History toggle *}
    <div id="activityHx[show]" class="data-group">
        {if $totalCountActivity[$i]}
            <a href="{crmURL p='civicrm/activityView' q="show=1&action=browse&history=1&cid=`$contactId[$i]`"}"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Activity History{/ts}</label> ({$totalCountActivity[$i]})
        {else}
            <dl><dt>{ts}Activity History{/ts}</dt><dd>{ts}No activity history.{/ts}</dd></dl>
        {/if}
    </div>
{/if}

{assign var=i value=$i+1}
</fieldset>
{/foreach}



