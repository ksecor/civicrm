{debug}
<form {$form.attributes}>
{$form.hidden}
<fieldset>
<div>
 <div>
     <span>{$form.contact_type.label}{$form.contact_type.html}
     {$form.group_id.label}{$form.group_id.html}
     {$form.category_id.label}{$form.category_id.html}</span>
 </div>
 <div>
     <span>{$form.sort_name.label}{$form.sort_name.html}</span>
 </div>
 <div>    
     <label>Enter full or partial last name or organization name to further limit the contacts included below</label>	
 </div>
 <div float="left">
     <span>{$form.buttons.html}</span>
     <span>{$form.adv_search.html}</span>
 </div>
</div>
</fieldset>

<fieldset>
 <div>
     {$form.action_id.label}{$form.action_id.html}{$form.go.html}	
     {include file="CRM/Contact/Selector/Selector.tpl"}
 </div>
</fieldset>

</form>
