<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or delete *}
    {include file="CRM/Grant/Form/Grant.tpl"}
{else}
<div id="help">
    {if $permission EQ 'edit'}
     {capture assign=newGrantURL}{crmURL p="civicrm/contact/view/grant" q="reset=1&action=add&cid=`$contactId`&context=grant"}{/capture}
     {ts 1=$newGrantURL}<a href="%1">Add new grant</a> for this contact.{/ts}
    {/if}
</div>

{if $rows}


    
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}No grants have been recorded from this contact.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{/if}
</div>
