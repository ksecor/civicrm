{* No matches for submitted search request. *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {if $qill}{ts}No matches found for:{/ts}
            {include file="CRM/common/displaySearchCriteria.tpl"}
        {else}
            {ts}No matching cases found.{/ts}
        {/if}
        <br />
        {ts}Suggestions:{/ts}
        <ul>
        <li>{ts}if you are searching by client name, check your spelling{/ts}</li>
        <li>{ts}try a different spelling or use fewer letters{/ts}</li>
        <li>{ts}if you are searching within a date range, try a wider range of values{/ts}</li>
        <li>{ts}make sure you have enough privileges in the access control system{/ts}</li>
        </ul>
    </dd>
  </dl>
</div>
