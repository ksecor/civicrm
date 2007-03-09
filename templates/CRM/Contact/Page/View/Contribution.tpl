<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Contribute/Form/Contribution.tpl"}
{elseif $action eq 4}
    {include file="CRM/Contribute/Form/ContributionView.tpl"}
{else}
{if $permission EQ 'edit'}
    {capture assign=newContribURL}{crmURL p="civicrm/contact/view/contribution" q="reset=1&action=add&cid=`$contactId`&context=contribution"}{/capture}
{/if}
<div class="description">
    {ts 1=$displayName}Contributions received from %1 since inception.{/ts} 
    {if $permission EQ 'edit'}{ts 1=$newContribURL}Click <a href="%1">New Contribution</a> to record a new offline contribution from this contact.{/ts}{/if}
</div>

{if $rows}
    {include file="CRM/Contribute/Page/ContributionTotals.tpl" mode="view"}
    <p> </p>
    {include file="CRM/Contribute/Form/Selector.tpl"}
    
    {if $action eq 16 and $permission EQ 'edit'}
    <div class="action-link">
    <a href="{$newContribURL}">&raquo; {ts}New Contribution{/ts}</a>
    </div>
    {/if}

{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {if $permission EQ 'edit'}
                {ts 1=$newContribURL}There are no contributions recorded from this contact. You can <a href="%1">enter one now</a>.{/ts}
            {else}
                {ts}There are no contributions recorded from this contact.{/ts}
            {/if}
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
