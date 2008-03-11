<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Event/Form/Participant.tpl"}
{elseif $action eq 4}
    {include file="CRM/Event/Form/ParticipantView.tpl"}
{else}
    {if $permission EQ 'edit'}{capture assign=newEventURL}{crmURL p="civicrm/contact/view/participant" q="reset=1&action=add&cid=`$contactId`&context=participant"}{/capture}{/if}

    <div id="help">
        <p>{ts 1=$displayName}This page lists all event registrations for %1 since inception.{/ts} 
        {if $permission EQ 'edit'}{ts 1=$newEventURL}Click <a accesskey="N" href='%1'>New Event Registration</a> to register this contact for an event.{/ts}{/if}
        </p>
    </div>

    {if $rows}
        {if $action eq 16 and $permission EQ 'edit'}
            <div class="action-link">
            <a href="{$newEventURL}">&raquo; {ts}New Event Registration{/ts}</a>
            </div>
        {/if}
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
