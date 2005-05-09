{* Search criteria form elements *}

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
        Full or partial name (last name, or first name, or organization name).
        </span>
     </div>
     <p>
     <span class="element-right"><a href="{crmURL p='civicrm/contact/search/advanced' q='reset=1'}">&gt;&gt; Advanced Search...</a></span>
     </p>
 </div>
</fieldset>