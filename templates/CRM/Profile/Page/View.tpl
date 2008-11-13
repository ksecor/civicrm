{foreach from=$profileGroups item=group}
    <h2>{$group.title}</h2>
    {$group.content}
{/foreach}
<div class="action-link">
    <a href="{$listingURL}">&raquo; {ts}Back to Listings{/ts}</a>&nbsp;&nbsp;&nbsp;&nbsp;
    {if $mapURL}
    <a href="{$mapURL}">&raquo; {ts}Map Primary Address{/ts}</a>
    {/if}

</div>
