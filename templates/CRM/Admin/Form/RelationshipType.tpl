{* this template is used for adding/editing relationship types  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Relationship Type{/ts}{elseif $action eq 2}{ts}Edit Relationship Type{/ts}{elseif $action eq 8}{ts}Delete Relationship Type{/ts}{else}{ts}View Relationship Type{/ts}{/if}</legend>
	{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this option will result in the loss of all Relationship records of this type.{/ts} {ts}This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
	    <dl>
        <dt>{$form.label_a_b.label}</dt><dd>{$form.label_a_b.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Label for the relationship from Contact A to Contact B. EXAMPLE: Contact A is 'Parent of' Contact B.{/ts}</dd>
        <dt>{$form.label_b_a.label}</dt><dd>{$form.label_b_a.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Label for the relationship from Contact B to Contact A. EXAMPLE: Contact B is 'Child of' Contact A. You may leave this blank for relationships where the name is the same in both directions (e.g. Spouse).{/ts}</dd>
        <dt>{$form.contact_type_a.label}</dt><dd>{$form.contact_type_a.html}</dd>
        <dt>{$form.contact_type_b.label}</dt><dd>{$form.contact_type_b.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
          </dl>
    {/if}
	{if $action neq 4} {* action is not view *}
           <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
        {else}
            <dl><dt></dt><dd>{$form.done.html}</dd></dl>
        {/if}

</fieldset>
</div>
