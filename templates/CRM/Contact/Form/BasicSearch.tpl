<form {$form.attributes}>
{$form.hidden}

{* Begin Browse Criteria section *}
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
     <span class="element-right">{$form.buttons.html}</span>
     <div class="description font-italic">
        <span class="horizontal-position">
        Enter full or partial last name or organization name to further limit the contacts included below.
        </span>
     </div>
     <p>
     <span class="element-right"><a href="{crmURL p='civicrm/contact/advanced_search' q='reset=1'}">&gt;&gt; Advanced Search...</a></span>
     </p>
 </div>
</fieldset>
{* END Browse Criteria section *}

<p>
Fetching {$session->get('qill')}
<p>


{if $rowsEmpty}
    {* No matches for submitted search request.*}
    <div class="messages status">
        <img src="crm/i/Inform.gif" alt="status"> &nbsp;
        No matches were found for your browse criteria.
        <ul>
        <li>check your spelling
        <li>try a different spelling or use fewer letters</li>
        <li>if you are searching within a Group or Category, try 'any group' or 'any category'</li>
        <li>add a <a href="crm/contact/addI?c_type=Individual&reset=1">New Individual</a>,
        <a href="crm/contact/addO?c_type=Organization&reset=1">Organization</a> or
        <a href="crm/contact/addH?c_type=Household&reset=1">Household</a></li>
        </ul>
    </div>
{/if}

{if $rows}
    {* Some matches for search request. Begin Actions/Results section *}
    <fieldset>
     <div class="form-item">
       <span class="horizontal-position">
         {$form.action_id.label}{$form.action_id.html} &nbsp; &nbsp; {$form.go.html}
       </span>
       <span class="element-right">Select: 
<a onclick="changeCheckboxVals('chk','select'  , 'Search' ); return false;" name="select_all"  href="#">All</a> |
<a onclick="changeCheckboxVals('chk','deselect', 'Search' ); return false;" name="select_none" href="#">None</a></span>
     </div>  

     <p>
       {include file="CRM/Contact/Selector.tpl"}
     </p>

    </fieldset>
    {* END Actions/Results section *}

{/if}
</form>
