{* $context indicates where we are searching, values = "search,advanced,smog,amtg" *}
{* smog = 'show members of group'; amtg = 'add members to group' *}
{if $context EQ 'smog'}
    <div id="help">
        {capture assign=addMembersURL}{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}{/capture}
        <p>{ts 1=$group.title}The members of the <strong>%1</strong> group are listed below. Use the 'Find Members...' criteria below to search for specific members. Use the 'Group Status...' checkboxes to view members with 'Pending' status and/or members who have been 'Removed' from this group.{/ts}</p>
        {if $permissionedForGroup}
        <p>{ts 1=$addMembersURL}Use the <a href="%1">Add Members...</a> screen if you want to add new members to this group.{/ts}
        {if $ssID}
            {if $ssMappingID}
                {capture assign=editSmartGroupURL}{crmURL p="civicrm/contact/search/builder" q="reset=1&force=1&ssID=`$ssID`"}{/capture}
            {else}
		{capture assign=editSmartGroupURL}{crmURL p="civicrm/contact/search/advanced" q="reset=1&force=1&ssID=`$ssID`"}{/capture}
            {/if} 
            {ts 1=$editSmartGroupURL}Click <a href="%1">Edit Smart Group Search Criteria...</a> to change the search query used for this 'smart' group.{/ts}
        {/if}
        </p>
        {/if}
    </div>
    {if $permissionedForGroup}
    <div class="form-item">
        <a href="{$addMembersURL}">&raquo; {ts 1=$group.title}Add Members to %1{/ts}</a>
    </div>
    {/if}
    
    {* Provide link to modify smart group search criteria if we are viewing a smart group (ssID = saved search ID) *}
    {if $ssID}
        <div class="form-item">
            <a href="{$editSmartGroupURL}">&raquo; {ts 1=$group.title}Edit Smart Group Search Criteria for %1{/ts}</a>
        </div>
    {/if}

{elseif $context EQ 'amtg'}
    <div id="help">
        {ts 1=$group.title}Use the Search form to find contacts to add to %1. Mark the contacts you want to add and click 'Add Contacts...'.{/ts}
    </div>

{else}
    <div id="help">
        {ts}Use the Search Criteria form to find contacts by name, type of contact, group membership, tags, etc. You can then view or edit contact details, print a contact list, assign tags, export contact data to a spreadsheet, etc.{/ts}
        {if $action EQ 512}
            <p>{ts}When multiple boxes are checked for Contact Types, Groups, Tags and Location Types, the selections are combined as <strong>OR</strong> criteria (e.g. checking &quot;Group A&quot; and &quot;Group B&quot; will find contacts who are either in &quot;Group A&quot; OR &quot;Group B&quot;).
               All other search fields are combined as <strong>AND</strong> criteria (e.g. selecting Tag is &quot;Major Donor&quot; AND Country is &quot;Mexico&quot; returns only those contacts who meet both criteria).{/ts}</p>
        {/if}
        {if $ssID}
            <p>{ts}If you've changed search criteria for this 'smart group' and want to save your changes, select <strong>Update Smart Group</strong> from the '- more actions -' drop-down menu.{/ts}</p>
        {/if}
    </div>

{/if}
