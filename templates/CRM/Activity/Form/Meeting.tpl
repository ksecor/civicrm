{* this template is used for adding/editing meeting  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Meeting{/ts}{else}{ts}Edit Meeting{/ts}{/if}</legend>
  <dl>
	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
	<dt>{$form.meeting_date.label}</dt><dd>{$form.meeting_date.html}</dd>
        <dt>{$form.location.label}</dt><dd>{$form.location.html|crmReplace:class:large}</dd>
        <dt>{$form.notes.label}</dt><dd>{$form.notes.html|crmReplace:class:huge}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
