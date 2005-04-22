{* this template is used for adding/editing relationship type  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $action eq 1}New{elseif $action eq 2}Edit {else} View{/if} Relationship Type</legend>
	<div>{$form.name_a_b.label}{$form.name_a_b.html}</div>
	<div>{$form.name_b_a.label}{$form.name_b_a.html}</div>
	<div>{$form.contact_type_a.label}{$form.contact_type_a.html}</div>
	<div>{$form.contact_type_b.label}{$form.contact_type_b.html}</div>
	<div>{$form.description.label}{$form.description.html}</div>
	{if $action neq 4}
        <div class="horizontal-position">
        <span class="two-col1">
            <span class="fields">{$form.buttons.html}</span>
        </span>
        <div class="spacer"></div>
        </div>
	{/if}
    </fieldset>
</div>
</form>
