<script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>
<form {$form.attributes}>
{$form.hidden}

{* Begin Browse Criteria section *}
<fieldset>
 <div class="form-item">
     <span class="horizontal-position">{$form.contact_type.label}{$form.contact_type.html}</span>
     <span class="horizontal-position">{$form.group_id.label}{$form.group_id.html}</span>
     <span class="element-right">{$form.category_id.label}{$form.category_id.html}</span>
 </div>
 <div class="form-item">
     <span class="horizontal-position">
     {$form.sort_name.label}{$form.sort_name.html}
     </span>
     <span class="element-right">{$form.buttons.html}</span>
     <div class="description">
        <span class="horizontal-position">
        Enter full or partial last name or organization name to further limit the contacts included below.
        </span>
     </div>
     <p>
     <span class="element-right">{$form.adv_search.html}</span>
     </p>
 </div>
</fieldset>
{* END Browse Criteria section *}

{if $rowsEmpty}

    {* No matches for search criteria *}
    <div class="messages status">
        <img src="crm/i/inform.gif" alt="status"> &nbsp;
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

{else}

    {* Begin Actions/Results section *}
    <fieldset>
     <div class="form-item">
       <span class="horizontal-position">
         {$form.action_id.label}{$form.action_id.html} &nbsp; &nbsp; {$form.go.html}
       </span>
       <span class="element-right">Select: {$form.select_all.html} | {$form.select_none.html}</span>
     </div>  

     <p>
       {include file="CRM/Contact/Selector.tpl"}
     </p>

    </fieldset>
    {* END Actions/Results section *}

{/if}
</form>
