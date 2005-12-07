{capture assign=newContribURL}{crmURL p="civicrm/contribute/contribution" q="reset=1&action=add&cid=`$contactId`"}{/capture}
<div id="help">
<p>{ts 1=$newContribURL}This page lists all contributions received from {$display_name} since inception.
Click <a href="%1">New Contribution</a> to record a new offline contribution for this contact.{/ts}.
</div>

{if $rows}
    {include file="CRM/Contribute/Page/ContributionTotals.tpl"}
    <p>
    {include file="CRM/Contribute/Form/Selector.tpl"}
    </p>
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
       <dd>
            {if $permission EQ 'edit'}
                {ts 1=$newContribURL}There are no Contributions recorded for this contact. You can <a href="%1">enter one now</a>.{/ts}
            {else}
                {ts}There are no Contributions recorded for this contact.{/ts}
            {/if}
       </dd>
       </dl>
  </div>
{/if}

<div class="action-link">
<a href="{$newContribURL}">&raquo; {ts}New Contribution{/ts}</a>
<div>