{* No matches for submitted search request or viewing an empty group. *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {if $context EQ 'smog'}
            {capture assign=crmURL}{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}{/capture}{ts 1=$group.title 2=$crmURL}%1 currently has no members. You can <a href="%2">add members here.</a>{/ts}
        {else}
            {if $qill}{ts}No matches found for:{/ts}
            <ul>
            {foreach from=$qill item=criteria}
                <li>{$criteria}</li>
            {/foreach}
            </ul>
            <br />
            {else}
            {ts}No matches found{/ts}
            {/if}
            {ts}Suggestions:{/ts}
            <ul>
            <li>{ts}check your spelling{/ts}</li>
            <li>{ts}try a different spelling or use fewer letters{/ts}</li>
            <li>{ts}if you are searching within a Group or for Tagged contacts, try 'any group' or 'any tag'{/ts}</li>
            {capture assign=crmURLI}{crmURL p='civicrm/contact/addI' q='c_type=Individual&reset=1'}{/capture}
            {capture assign=crmURLO}{crmURL p='civicrm/contact/addO' q='c_type=Organization&reset=1'}{/capture}
            {capture assign=crmURLH}{crmURL p='civicrm/contact/addH' q='c_type=Household&reset=1'}{/capture}
            <li>{ts 1=$crmURLI 2=$crmURLO 3=$crmURLH}add a <a href="%1">New Individual</a>, <a href="%2">Organization</a> or <a href="%3">Household</a>{/ts}</li>
            <li>{ts}make sure you have enough privileges in the access control system{/ts}</li>
            </ul>
        {/if}
    </dd>
  </dl>
</div>
