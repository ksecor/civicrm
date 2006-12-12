{* this template used to build location block *}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset><legend>{ts}Event Location{/ts}</legend>
    {include file="CRM/Contact/Form/Location.tpl"}
    <dl>
     <dt></dt><dd>{$form.buttons.html}</dd>
   </dl>
</fieldset>
</div>
