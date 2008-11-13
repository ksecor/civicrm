<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8 }{* add, update or delete *}
    {include file="CRM/Grant/Form/Grant.tpl"}
{elseif $action eq 4 }
    {include file="CRM/Grant/Form/GrantView.tpl"}
{else}
     {if $permission EQ 'edit'}
        {capture assign=newGrantURL}{crmURL p="civicrm/contact/view/grant" q="reset=1&action=add&cid=`$contactId`&context=grant"}{/capture}
    {/if}

    <div id="help">
        <p>{ts 1=$displayName}This page lists all grant registrations for %1 since inception.{/ts} 
        {if $permission EQ 'edit'}
            {ts 1=$newGrantURL}Click <a accesskey="N" href='%1'>New Grant</a> to register this contact for a Grant.{/ts}
        {/if}
        </p>
    </div>

    {if $rows}
        {if $action eq 16 and $permission EQ 'edit'}
            <div class="action-link">
            <a href="{$newGrantURL}" class="button"><span>&raquo; {ts}New Grant{/ts}</span></a><br/><br/>
            </div>
        {/if}
        {include file="CRM/Grant/Form/Selector.tpl"}
    {else}
        <div class="messages status">
           <dl>
             <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
               <dd>
                 {if $permission EQ 'edit'}
                    {ts 1=$newGrantURL}There are no grant recorded for this contact. You can <a href='%1'>enter one now</a>.{/ts}
                 {else}
                    {ts}There are no grant recorded for this contact.{/ts}
                 {/if}
               </dd>
           </dl>
       </div>
    {/if}

{/if}
</div>
