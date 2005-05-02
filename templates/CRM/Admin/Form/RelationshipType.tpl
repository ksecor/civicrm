{* this template is used for adding/editing relationship type  *}
<form {$form.attributes}>
<p>
<fieldset><legend>{if $action eq 1}New{elseif $action eq 2}Edit {else} View{/if} Relationship Type</legend>
	<div class="form-item">{$form.name_a_b.label}{$form.name_a_b.html}</div>
	<div class="form-item">{$form.name_b_a.label}{$form.name_b_a.html}</div>
	<div class="form-item">{$form.contact_type_a.label}{$form.contact_type_a.html}</div>
	<div class="form-item">{$form.contact_type_b.label}{$form.contact_type_b.html}</div>
	<div class="form-item">{$form.description.label}{$form.description.html}</div>
	{if $action neq 4}
        <div class="form-item">
            {$form.buttons.html}
        </div>
	{/if}
    </fieldset>
</p>
</form>
