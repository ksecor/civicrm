<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Event/Form/Participant.tpl"}
{elseif $action eq 4}
    {include file="CRM/Event/Form/ParticipantView.tpl"}
{else}
    {if $permission EQ 'edit'}{capture assign=newEventURL}{crmURL p="civicrm/contact/view/participant" q="reset=1&action=add&cid=`$contactId`&context=participant"}{/capture}
    {/if}

    <div id="help">
        <p>{ts 1=$displayName}This page lists all event registrations for %1 since inception.{/ts} 
        {if $permission EQ 'edit'}{ts 1=$newEventURL}Click <a accesskey="N" href='%1'>New Event Registration</a> to register this contact for an event.{/ts}{/if}
        </p>
        {if $accessContribution and $newCredit}
            {capture assign=newCreditURL}{crmURL p="civicrm/contact/view/participant" q="reset=1&action=add&cid=`$contactId`&context=participant&mode=live"}{/capture}
            {ts 1=$newCreditURL}Click <a href='%1'>Submit Credit Card Event Registration</a> to process a new New Registration on behalf of the participant using their credit or debit card.{/ts}
        {/if}
    </div>
    {if $action eq 16 and $permission EQ 'edit'}
       <div class="action-link">
           <a accesskey="N" href="{$newEventURL}" class="button"><span>&raquo; {ts}New Event Registration{/ts}</span></a>
            {if $accessContribution and $newCredit}
                <a accesskey="N" href="{$newCreditURL}" class="button"><span>&raquo; {ts}Submit Credit / Debit Card Event Registration{/ts}</a></span>
            {/if}
            <br/ ><br/ >
       </div>
   {/if}

    {if $rows}
        {include file="CRM/Event/Form/Selector.tpl"}
    {else}
       <div class="messages status">
           <dl>
             <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
               <dd>
                 {if $permission EQ 'edit'}
                   {ts 1=$newEventURL}There are no event registrations recorded for this contact. You can <a href='%1'>enter one now</a>.{/ts}
                 {else}
                   {ts}There are no event registrations recorded for this contact.{/ts}
                 {/if}
               </dd>
           </dl>
       </div>
    {/if}

{/if}
</div>
