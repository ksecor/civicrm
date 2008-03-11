<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Contribute/Form/Contribution.tpl"}
{elseif $action eq 4}
    {include file="CRM/Contribute/Form/ContributionView.tpl"}
{else}
<div id="help">
    {ts 1=$displayName}Contributions received from %1 since inception.{/ts} 
    {if $permission EQ 'edit'}
     {capture assign=newContribURL}{crmURL p="civicrm/contact/view/contribution" q="reset=1&action=add&cid=`$contactId`&context=contribution"}{/capture}
     {ts 1=$newContribURL}Click <a href='%1'>Record Offline Contribution</a> to record a new offline contribution received from this contact.{/ts}
     {if $newCredit}
       {capture assign=newCreditURL}{crmURL p="civicrm/contribute/offline" q="reset=1&cid=`$contactId`"}{/capture}
       {ts 1=$newCreditURL}Click <a href='%1'>Submit Credit Card Contribution</a> to process a new contribution on behalf of the contributor using their credit or debit card.{/ts}
     {/if}
    {/if}
</div>

{if $action eq 16 and $permission EQ 'edit'}
    <div class="action-link">
        <a accesskey="N" href="{$newContribURL}">&raquo; {ts}Record Offline Contribution{/ts}</a>
        {if $newCredit}
            &nbsp; &nbsp; <a href="{$newCreditURL}">&raquo; {ts}Submit Credit Card Contribution{/ts}</a>
        {/if}
    </div>
{/if}


{if $rows}
    {include file="CRM/Contribute/Page/ContributionTotals.tpl" mode="view"}
    <p> </p>
    {include file="CRM/Contribute/Form/Selector.tpl"}
    
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}No contributions have been recorded from this contact.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{if $honor}	
    <div class="description">
        <p>{ts 1=$displayName}Contributions made in honor of %1.{/ts}</p>
    </div>
    {include file="CRM/Contribute/Page/ContributionHonor.tpl"}	
{/if} 

{/if}
</div>
