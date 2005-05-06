{* $context indicates where we are searching, values = "search,advanced,smog,amtg" *}
{* Subtitles for 'show members of group' (smog) and 'add members to group' (amtg) contexts *}
{if $context EQ 'smog'}
    <div id="help">
        The current members of {$group.title} are displayed below. Use the Find box below to
        search for specific members. Click 'Add Members...' to find more contacts
        and add them to this group.
    </div>
    <div class="form-item">
        <a href="{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}">Add Members to {$group.title}</a>
    </div>
{/if}
{if $context EQ 'amtg'}
    <div id="help">
        Use the Search form to find contacts to add to {$group.title}. Mark the
        contacts you want to add and click 'Add Contacts...' buttons.
    </div>
{/if}
<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

{* Begin Browse Criteria section *}
<fieldset>
 {if $context EQ 'smog'}<legend>Find Contacts within this Group</legend>{/if}
 {if $context EQ 'amtg'}<legend>Find Contacts to Add to this Group</legend>{/if}
 <div class="form-item">
     <span class="horizontal-position">{$form.contact_type.label}{$form.contact_type.html}</span>
     <span class="horizontal-position">{$form.group.label}{$form.group.html}</span>
     <span class="element-right">{$form.category.label}{$form.category.html}</span>
 </div>
 <div class="form-item">
     <span class="horizontal-position">
     {$form.sort_name.label}{$form.sort_name.html}
     </span>
     <span class="element-right">{$form._qf_Search_refresh_search.html}</span>
     <div class="description font-italic">
        <span class="horizontal-position">
        Enter full or partial last name or organization name to further limit the contacts included below.
        </span>
     </div>
     <p>
     <span class="element-right"><a href="{crmURL p='civicrm/contact/search/advanced' q='reset=1'}">&gt;&gt; Advanced Search...</a></span>
     </p>
 </div>
</fieldset>
{* END Browse Criteria section *}

{if $rowsEmpty}
    {* No matches for submitted search request or viewing an empty group. *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
        <dd>
            {if $context EQ 'smog'}
                {$group.title} currently has no members. You can <a href="{crmURL q="context=amtg&amtgID=`$group.id`&reset=1"}">add members here.</a>
            {else}
                No matching contacts found. You can:
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
{/if}

{if $rows}
    {* Some matches for search request. Begin Actions/Results section *}
    <fieldset>
    
     <div id="search-status">
      Found {$pager->_totalItems} contacts.
     </div>

     <div class="form-item">
       <span>
         {* Hide export and print buttons in 'Add Members to Group' context. *}
         {if $context NEQ 'amtg'}
            {$form._qf_Search_next_print.html} &nbsp; {$form._qf_Search_refresh_export.html} &nbsp; &nbsp; &nbsp;
            {$form.task.html}
         {/if}
  	     {$form._qf_Search_next_action.html}
         <br />
	     {$form.radio_ts.ts_sel.html} &nbsp; {$form.radio_ts.ts_all.html} {$pager->_totalItems} records
       </span>
       <span class="element-right">Select: 
<a onclick="changeCheckboxVals('mark_x_','select'  , Search ); return false;" name="select_all"  href="#">All</a> |
<a onclick="changeCheckboxVals('mark_x_','deselect', Search ); return false;" name="select_none" href="#">None</a></span>
     </div>  

     <p>
       {include file="CRM/Contact/Form/Selector.tpl"}
     </p>

    </fieldset>
    {* END Actions/Results section *}

{/if}
</form>
