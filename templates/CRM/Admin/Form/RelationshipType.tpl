{* this template is used for adding/editing relationship type  *}
<form {$form.attributes}>
<p>
<fieldset><legend>{if $action eq 1}{ts}New Relationship Type{/ts}{elseif $action eq 2}{ts}Edit Relationship Type{/ts}{else} {ts}View Relationship Type{/ts}{/if}</legend>
	<div class="form-item">
        <dl>
        <dt>{$form.name_a_b.label}</dt><dd>{$form.name_a_b.html}</dd>
        <dt>{$form.name_b_a.label}</dt><dd>{$form.name_b_a.html}</dd>
        <dt>{$form.contact_type_a.label}</dt><dd>{$form.contact_type_a.html}</dd>
        <dt>{$form.contact_type_b.label}</dt><dd>{$form.contact_type_b.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        {if $action neq 4} {* action is not view *}
            <dt></dt><dd>{$form.buttons.html}</dd>
        {else}
            <dt></dt><dd>{$form.done.html}</dd>
        {/if}
        </dl>
    </div>
</fieldset>
</p>
</form>
