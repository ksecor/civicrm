{* this template is used for adding/editing a mailing component  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Mailing Component{/ts}{else}{ts}Edit Mailing Component{/ts}{/if}</legend>
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.component_type.label}</dt><dd>{$form.component_type.html}</dd>
    <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt>{$form.body_text.label}</dt><dd>{$form.body_text.html}</dd>
    <dt>{$form.body_html.label}</dt><dd>{$form.body_html.html}</dd>
    <dt>{$form.is_default.label}</dt><dd>{$form.is_default.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
