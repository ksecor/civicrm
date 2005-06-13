{* $context indicates where we are searching, values = "search,advanced,smog,amtg" *}
{* smog = 'show members of group'; amtg = 'add members to group' *}
{if $context EQ 'smog'}
    <div id="help">
        {capture assign=crmURL}{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}{/capture}
        {ts 1=$group.title 2=$crmURL}The current members of the <strong>%1</strong> group are listed below. Use the Find box below to search for specific members. Use the <a href="%2">Add Members...</a> screen if you want to add new members to this group.{/ts}
    </div>
    <div class="form-item">
        <a href="{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}">&raquo; {ts 1=$group.title}Add Members to %1{/ts}</a>
    </div>
{elseif $context EQ 'amtg'}
    <div id="help">
        {ts 1=$group.title}Use the Search form to find contacts to add to %1. Mark the contacts you want to add and click 'Add Contacts...' buttons.{/ts}
    </div>
{else}
    <div id="help">
        {ts}Use the Search Criteria form to find contacts by name, type of contact, group membership, tags, etc. You can then view or edit contact details, print a contact list, assign tags, export contact data to a spreadsheet, etc.{/ts}
    </div>
{/if}
