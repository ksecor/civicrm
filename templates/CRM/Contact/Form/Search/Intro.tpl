{* $context indicates where we are searching, values = "search,advanced,smog,amtg" *}
{* smog = 'show members of group'; amtg = 'add members to group' *}
{if $context EQ 'smog'}
    <div id="help">
        The current members of {$group.title} are displayed below. Use the Find box below to
        search for specific members. Click 'Add Members...' to find more contacts
        and add them to this group.
    </div>
    <div class="form-item">
        <a href="{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}">Add Members to {$group.title}</a>
    </div>
{elseif $context EQ 'amtg'}
    <div id="help">
        Use the Search form to find contacts to add to {$group.title}. Mark the
        contacts you want to add and click 'Add Contacts...' buttons.
    </div>
{else}
    <div id="help">
        Use the Search Criteria form to find contacts by name, type of contact, group membership, tags, etc.
        You can then view or edit contact details, print a contact list, assign tags, export contact data to a
        spreadsheet, etc.
    </div>
{/if}
