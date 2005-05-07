{* No matches for submitted search request or viewing an empty group. *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
    <dd>
        {if $context EQ 'smog'}
            {$group.title} currently has no members. You can <a href="{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}">add members here.</a>
        {else}
            No matches found. You can:
            <ul>
            <li>check your spelling
            <li>try a different spelling or use fewer letters</li>
            <li>if you are searching within a Group or Category, try 'any group' or 'any category'</li>
            <li>add a <a href="{crmURL p='civicrm/contact/addI' q='c_type=Individual&reset=1'}">New Individual</a>,
            <a href="{crmURL p='civicrm/contact/addO' q='c_type=Organization&reset=1'}">Organization</a> or
            <a href="{crmURL p='civicrm/contact/addH' q='c_type=Household&reset=1'}">Household</a></li>
            </ul>
        {/if}
    </dd>
  </dl>
</div>
