{* this template is used for adding/editing relationship type  *}
<form {$form.attributes}>
<p>
<fieldset><legend>{if $action eq 1}New{elseif $action eq 2}Edit {else} View{/if} Relationship Type</legend>
	<div class="form-item">
        <dl>
        <dt>{$form.name_a_b.label}</dt><dd>{$form.name_a_b.html}</dd>
        <dt>{$form.name_b_a.label}</dt><dd>{$form.name_b_a.html}</dd>
        <dt>{$form.contact_type_a.label}</dt><dd>{$form.contact_type_a.html}</dd>
        <dt>{$form.contact_type_b.label}</dt><dd>{$form.contact_type_b.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        {if $action neq 4}
            <dt></dt><dd>{$form.buttons.html}</dd>
        {/if}
        </dl>
    </div>
</fieldset>
</p>
</form>
