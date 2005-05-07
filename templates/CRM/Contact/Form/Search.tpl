{* Main template for contact search *}

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
{/if}

<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

{* This section handles form elements for search criteria *}
{include file="CRM/Contact/Form/SearchCriteria.tpl"}

{if $rowsEmpty}
    {include file="CRM/Contact/Form/EmptySearchResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Contact/Form/SearchResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p>
       {include file="CRM/Contact/Form/Selector.tpl"}
       </p>

    </fieldset>
    {* END Actions/Results section *}

{/if}
</form>
