<form {$form.attributes}>
{$form.hidden}
<fieldset class="fieldset-property">
<div id="crm-container" class="form-item">
 <div class="verticle-position">
     <span class="horizontal-position">{$form.contact_type.label}{$form.contact_type.html}</span>
     <span class="horizontal-position">{$form.group_id.label}{$form.group_id.html}</span>
     <span class="element-right">{$form.category_id.label}{$form.category_id.html}</span>
     <div class="element-right">
         <span class="button-property">{$form.buttons.html}</span>
     </div>
 </div>
 <div class="verticle-position">
     <span class="horizontal-position">{$form.sort_name.label}{$form.sort_name.html}</span><br />
     <label class="horizontal-position">Enter full or partial last name or organization name to further limit the contacts included below</label>	
 </div>
 <div class="verticle-position">
     <span class="element-right">{$form.adv_search.html}</span>
 </div>
</div>
</fieldset>

<fieldset class="fieldset-property">
 <div id="crm-container" class="form-item">
     <span>{$form.action_id.label}{$form.action_id.html}</span><span class="button-property">{$form.go.html}</span>	
     {include file="CRM/Contact/Selector/Selector.tpl"}
 </div>
</fieldset>

</form>
