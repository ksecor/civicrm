{*debug*}
<script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>
<form {$form.attributes}>
{$form.hidden}

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

<fieldset>
 <div class="form-item">
   <span class="horizontal-position">
     {$form.action_id.label}{$form.action_id.html} &nbsp; &nbsp; {$form.go.html}
   </span>
   <span class="element-right">Select: {$form.select_all.html} | {$form.select_none.html}</span>
 </div>  

 <p>
   {include file="CRM/Contact/Selector/Selector.tpl"}
 </p>

</fieldset>
</form>
