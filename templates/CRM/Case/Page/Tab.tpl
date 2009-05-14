{if $notConfigured} {* Case types not present. Component is not configured for use. *}
    {include file="CRM/Case/Page/ConfigureError.tpl"}
{else}

    {capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="reset=1&action=add&cid=`$contactId`&context=case"}{/capture}
    {capture assign=myCaseURL}{crmURL p="civicrm/case" q="reset=1&cid=`$contactId`&all=0"}{/capture}

    {if $action eq 1 or $action eq 2 or $action eq 8 or $action eq 32768 } {* add, update, delete, restore*}            
        {include file="CRM/Case/Form/Case.tpl"}
    {elseif $action eq 4 }
        {include file="CRM/Case/Form/CaseView.tpl"}

    {else}
    <div class="view-content">
    <div id="help">
         {ts 1=$displayName}This page lists all case records for %1.{/ts}
         {if $permission EQ 'edit'}{ts 1=$newCaseURL}Click <a href='%1'>New Case</a> to add a case record for this contact.{/ts}{/if}
    </div>

    {if $action eq 16} 
      {if $permission EQ 'edit'}
        <div class="action-link">
        <a accesskey="N" href="{$newCaseURL}" class="button"><span>&raquo; {ts}New Case{/ts}</span></a>
        </div>
      {/if}
        <div class="action-link">
        <a accesskey="M" href="{$myCaseURL}" class="button"><span>&raquo; {ts}My Cases{/ts}</span></a>
        </div>
        <br /><br />
    {/if}

    {if $rows}
        {include file="CRM/Case/Form/Selector.tpl"}
    {else}
       <div class="messages status">
           <dl>
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>
                {ts}There are no case records for this contact.{/ts}
                {if $permission EQ 'edit'}{ts 1=$newCaseURL}You can <a href='%1'>open one now</a>.{/ts}{/if}
           </dd>
           </dl>
      </div>
    {/if}
    </div>
    {/if}
{/if}