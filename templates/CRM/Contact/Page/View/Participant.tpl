<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Event/Form/Participant.tpl"}
{elseif $action eq 4}
    {include file="CRM/Event/Form/ParticipantView.tpl"}
{else}
{capture assign=newEventURL}{crmURL p="civicrm/contact/view/participant" q="reset=1&action=add&cid=`$contactId`&context=participant"}{/capture}
<div id="help">
<p>{ts 1=$displayName}This page lists all events participated by %1 since inception.{/ts} 
{ts 1=$newEventURL}Click <a href="%1">New Participation</a> to record a new participation for this contact.{/ts}
</p>
</div>

{if $rows}
    {include file="CRM/Event/Form/Selector.tpl"}
    {if $action eq 16 and $permission EQ 'edit'}
    <div class="action-link">
    <a href="{$newEventURL}">&raquo; {ts}New Participation{/ts}</a>
    </div>
    {/if}
{else}
   <div class="messages status">
       <dl>
         <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>
             {if $permission EQ 'edit'}
               {ts 1=$newEventURL}There are no participations recorded for this contact. You can <a href="%1">enter one now</a>.{/ts}
             {else}
               {ts}There are no participations recorded for this contact.{/ts}
             {/if}
           </dd>
       </dl>
   </div>
{/if}

{/if}
</div>
