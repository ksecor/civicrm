<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Pledge/Form/Pledge.tpl"}
{elseif $action eq 4}
    {include file="CRM/Pledge/Form/PledgeView.tpl"}
{else}
<div id="help">
    {ts 1=$displayName}Pledges received from %1 since inception.{/ts} 
    {if $permission EQ 'edit'}
     {capture assign=newContribURL}{crmURL p="civicrm/contact/view/pledge" q="reset=1&action=add&cid=`$contactId`&context=pledge"}{/capture}
     {ts 1=$newContribURL}Click <a href='%1'>New Pledge</a> to record a new pledge received from this contact.{/ts}
    {/if}
</div>

{if $action eq 16 and $permission EQ 'edit'}
    <div class="action-link">
       <a accesskey="N" href="{$newContribURL}" class="button"><span>&raquo; {ts}New Pledge{/ts}</a></span>
       <br/><br/>
    </div>
{/if}


{if $rows}
    <p> </p>
    {include file="CRM/Pledge/Form/Selector.tpl"}
    
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}No pledges have been recorded from this contact.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{if $honor}	
    <div class="solid-border-top">
        <br /><label>{ts 1=$displayName}Contributions made in honor of %1{/ts}</label>
    </div>
    {include file="CRM/Contribute/Page/ContributionHonor.tpl"}	
{/if} 

{/if}
</div>
