{* Displays search criteria assigned to $qill variable, for all search forms - basic, advanced, search builder, and component searches. *}
{foreach from=$qill name=sets key=setKey item=orClauses}
    {if $smarty.foreach.sets.total > 2}
        {* We have multiple criteria sets, so display AND'd items in each set on the same line. *}
        {if count($orClauses) gt 0}
        <ul>
        <li>
        {foreach from=$orClauses name=criteria item=item}
            {$item}
            {if !$smarty.foreach.criteria.last}
                &nbsp; ... AND ...
            {/if}
        {/foreach}
        </li>
        </ul>

        {* If there's a criteria set with key=0, this set is AND'd with other sets (if any). Otherwise, multiple sets are OR'd together. *}
        {if !$smarty.foreach.sets.last}
            <ul class="menu"><li class="no-display"> 
            {if $setKey == 0}AND<br />
            {else}OR<br />
            {/if}
            </li></ul>
        {/if}
        {/if}

    {else}
        <ul>
        {foreach from=$orClauses name=criteria item=item}
            <li>{$item}
            {if !$smarty.foreach.criteria.last}
                &nbsp; ... AND ...
            {/if}
            </li>
        {/foreach}
        </ul>
    {/if}
{/foreach}
