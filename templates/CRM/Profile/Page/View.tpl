{* If you want a custom profile view, you can access field labels and values in $profileFields_N array - where N is profile ID. *}
{* EXAMPLES *}{* $profileFields_1.last_name.label *}{* $profileFields_1.last_name.value *}

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
