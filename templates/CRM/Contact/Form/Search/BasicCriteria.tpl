{* Search criteria form elements *}

<fieldset>
    <legend>
        {if $context EQ 'smog'}Find Members of this Group
        {elseif $context EQ 'amtg'}Find Contacts to Add to this Group
        {else}Search Criteria{/if}
    </legend>
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
        {ts}Full or partial name (last name, or first name, or organization name).{/ts}
        </span>
     </div>
     <p>
{if $context EQ 'smog'}
     <span class="element-right"><a href="{crmURL p='civicrm/group/search/advanced' q="context=smog&gid=`$group.id`&reset=1&force=1"}">&raquo; Advanced Search</a></span>
{elseif $context EQ 'amtg'}
     <span class="element-right"><a href="{crmURL p='civicrm/contact/search/advanced' q="context=amtg&amtgID=`$group.id`&reset=1&force=1"}">&raquo; Advanced Search</a></span>
{else}
     <span class="element-right"><a href="{crmURL p='civicrm/contact/search/advanced'}">&raquo; Advanced Search</a></span>
{/if}
     </p>
 </div>
</fieldset>
