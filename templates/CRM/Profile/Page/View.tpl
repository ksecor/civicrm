{foreach from=$profileGroups item=group}
    <h2>{$group.title}</h2>
    <div id="profilewrap{$groupID}">
    	 {$group.content}
    </div>
{/foreach}
<div class="action-link">
    <a href="{$listingURL}">&raquo; {ts}Back to Listings{/ts}</a>&nbsp;&nbsp;&nbsp;&nbsp;
    {if $mapURL}
    <a href="{$mapURL}">&raquo; {ts}Map Primary Address{/ts}</a>
    {/if}

</div>
