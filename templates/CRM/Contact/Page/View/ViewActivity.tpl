{assign var=i value=0}
{foreach from=$rows item=ro}
<fieldset>
<div><label class="font-size12pt">{$display_name[$i]}</label></div>

{* Open Activities table and Activity History are toggled on this page for now because we don't have a solution for including 2 'selectors' on one page. *}

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
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
             <td>{$row.activity_type}</td>
             <td>
               <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=`$row.activity_type_id`&action=view&id=`$row.id`&cid=`$contactId[$i]`&history=0"}">{$row.subject|mb_truncate:33:"...":true}</a>
             </td>
             <td>
               <a href="{crmURL p='civicrm/contact/view/basic' q="reset=1&cid=`$row.sourceID`"}">{$row.sourceName}</a>
             </td>
             <td>
               <a href="{crmURL p='civicrm/contact/view/basic' q="reset=1&cid=`$row.targetID`"}">{$row.targetName}</a>
             </td>
             <td>{$row.date|crmDate}</td>
             <td>{$row.status_display}</td>
             <td>{$row.action}</td>
           </tr>           
    {/foreach}
    </table>

    {/strip}

    {include file="CRM/pager.tpl" location="bottom"}
  </form>
    </fieldset>
{/if}

{assign var=i value=$i+1}
</fieldset>
{/foreach}



