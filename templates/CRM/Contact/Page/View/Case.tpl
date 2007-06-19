<div class="view-content">
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Case/Form/Case.tpl"}
{elseif $action eq 4}
    {include file="CRM/Contribute/Form/ContributionView.tpl"}
{else}
<div id="help">
    {if $permission EQ 'edit'}
     {capture assign=newContribURL}{crmURL p="civicrm/contact/view/case" q="reset=1&action=add&cid=`$contactId`&context=case"}{/capture}
     {ts 1=$newContribURL}Click <a href="%1">Register Case</a> for this contact.{/ts}
    {/if}
</div>

{if $rows}
    
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {ts}No cases have been recorded from this contact.{/ts}
       </dd>
       </dl>
  </div>
{/if}

{/if}
</div>
