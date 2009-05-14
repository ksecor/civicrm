{* No matches for submitted search request. *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {if $qill}{ts}No matches found for:{/ts}
            {include file="CRM/common/displaySearchCriteria.tpl"}
        {else}
            {ts}No matching pledge results found.{/ts}
        {/if}
    </dd>
    <dt>&nbsp;</dt>
    <dd>
        {ts}Suggestions:{/ts}
        <ul>
        <li>{ts}If you are searching by pledger name, check your spelling or use fewer letters.{/ts}</li>
        <li>{ts}If you are searching within a date or amouht range, try a wider range of values.{/ts}</li>
        <li>{ts}Make sure you have enough privileges in the access control system.{/ts}</li>
        </ul>
    </dd>
  </dl>
</div>
