{foreach from=$profileGroups item=group}
    <h2>{$group.title}</h2>
    {$group.content}
{/foreach}
<div class="action-link">
    <a href="{crmURL p='civicrm/profile'}">&raquo; {ts}Back to Listings{/ts}</a>
</div>
