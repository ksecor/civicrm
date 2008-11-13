<div class="form-item">
<fieldset>
  <legend>{ts}Standalone Registration Form{/ts}</legend>

<dl>
    <dt>{$form.user_unique_id.label}</dt><dd>{$form.user_unique_id.html}</dd>
</dl>
<dl>
    <dt>{$form.email.label}</dt><dd>{$form.email.html}</dd>
</dl>

    {include file="CRM/UF/Form/Block.tpl" fields=$custom}

  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>

</fieldset>
</div>
