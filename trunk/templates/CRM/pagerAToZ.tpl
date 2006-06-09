{* Displays alphabetic filter bar for search results. If one more records in resultset starts w/ that letter, item is a link. *}

<div id="alpha-filter">
    <ul>
    {foreach from=$aToZ item=letter}
        <li {if $letter.class}class="{$letter.class}"{/if}>{$letter.item}</li>
    {/foreach}
    </ul>
</div>
