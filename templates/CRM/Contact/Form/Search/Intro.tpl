{* $context indicates where we are searching, values = "search,advanced,smog,amtg" *}
{* smog = 'show members of group'; amtg = 'add members to group' *}
{if $context EQ 'smog'}
    {if $permissionedForGroup}
    {capture assign=addMembersURL}{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}{/capture}
    <div class="action-link">
        <a href="{$addMembersURL}">&raquo; {ts 1=$group.title}Add Members to %1{/ts}</a>
    </div>
    {/if}
    
    {* Provide link to modify smart group search criteria if we are viewing a smart group (ssID = saved search ID) *}
    {if $ssID}
        {if $ssMappingID}
            {capture assign=editSmartGroupURL}{crmURL p="civicrm/contact/search/builder" q="reset=1&force=1&ssID=`$ssID`"}{/capture}
        {else}
            {capture assign=editSmartGroupURL}{crmURL p="civicrm/contact/search/advanced" q="reset=1&force=1&ssID=`$ssID`"}{/capture}
        {/if} 
        <div class="action-link">
            <a href="{$editSmartGroupURL}">&raquo; {ts 1=$group.title}Edit Smart Group Search Criteria for %1{/ts}</a>
        </div>
    {/if}
{/if}
