{* Custom searches. Default template for NO MATCHES on submitted search request. *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {if $qill}{ts}No matches found for:{/ts}
            {include file="CRM/common/displaySearchCriteria.tpl"}
            <br />
        {else}
            {ts}No matches found.{/ts}
            <br />
        {/if}
        {ts}Suggestions:{/ts}
        <ul>
        <li>{ts}check your spelling{/ts}</li>
        <li>{ts}try a different spelling or use fewer letters{/ts}</li>
        <li>{ts}make sure you have enough privileges in the access control system{/ts}</li>
        </ul>
    </dd>
  </dl>
</div>
