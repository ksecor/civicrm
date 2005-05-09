<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>Saved Search</legend>
 <div id="search-status">
  {$qill}<br />
 </div>
 <div class="form-item">
 <dl>
   <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
   <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   <dt></dt><dl>{$form.buttons.html}</dl>
 </dl>
 </div>
</fieldset>
</form>
