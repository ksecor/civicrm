<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

{* Begin Browse Criteria section *}
{* $context indicates where we are searching, values = "search, advanced, group" *}
<fieldset>
 <div class="form-item">
     <span class="horizontal-position">{$form.contact_type.label}{$form.contact_type.html}</span>
     <span class="horizontal-position">{$form.group.label}{$form.group.html}</span>
     <span class="element-right">{$form.category.label}{$form.category.html}</span>
 </div>
 <div class="form-item">
     <span class="horizontal-position">
     {$form.sort_name.label}{$form.sort_name.html}
     </span>
     <span class="element-right">{$form._qf_Search_refresh.html}</span>
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
    {* No matches for submitted search request.*}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
        <dd>
            No matching contacts found. You can:
            <ul>
            <li>check your spelling
            <li>try a different spelling or use fewer letters</li>
            <li>if you are searching within a Group or Category, try 'any group' or 'any category'</li>
            <li>add a <a href="{crmURL p='civicrm/contact/addI' q='c_type=Individual&reset=1'}">New Individual</a>,
            <a href="{crmURL p='civicrm/contact/addO' q='c_type=Organization&reset=1'}">Organization</a> or
            <a href="{crmURL p='civicrm/contact/addH' q='c_type=Household&reset=1'}">Household</a></li>
            </ul>
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
         {$form.task.html} &nbsp; &nbsp; {$form._qf_Search_next.html} &nbsp; &nbsp; {$form._qf_Search_refresh_export.html}
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
