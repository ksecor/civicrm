{if $action eq 1 or $action eq 2 or $action eq 4}
<form {$form.attributes}>
<fieldset>
<div class="form-item">
  {$form.label.label} {$form.label.html}
</div>
<div class="form-item">
  {$form.data_type.label} {$form.data_type.html}
</div>
<div class="form-item">
  {$form.html_type.label} {$form.html_type.html}
</div>
<div class="form-item">
  {$form.default_value.label} {$form.default_value.html}
</div>
<div class="form-item">
  {$form.is_required.label} {$form.is_required.html}
</div>
<div class="form-item">
  {$form.is_active.label} {$form.is_active.html}
</div>

{if $action ne 4}
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 {else}
   <div class="form-item">{$form.done.html}</div>
{/if} {* $action ne view *}

</fieldset>
</form>
{/if}
