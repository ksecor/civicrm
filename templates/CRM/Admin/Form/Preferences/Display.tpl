{* this template is used for editing Site Preferences  *}
<div class="form-item">
<fieldset><legend>{if $action eq 2 or $action eq 1}{ts}Site Preferences{/ts}{elseif $action eq 4}{ts}View Site Preferences{/ts}{/if}</legend>
      <dl>
        <dt>{$form.contact_view_options.label}</dt><dd>{$form.contact_view_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Select the <strong>tabs</strong> that should be displayed when viewing a contact record.
            EXAMPLE: If your organization does not keep track of 'Relationships', then un-check this option to simplify the screen display.{/ts}</dd>
        <dt>{$form.contact_edit_options.label}</dt><dd>{$form.contact_edit_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Select the sections that should be included when adding or editing a contact record.
            EXAMPLE: If your organization does not record Gender and Birth Date for individuals, then simplify the form by un-checking this option.{/ts}</dd>
        <dt>{$form.advanced_search_options.label}</dt><dd>{$form.advanced_search_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Select the sections that should be included in the Advanced Search form.
            EXAMPLE: If you don't track Relationships - then you do not need this section included in the search form - simplify the form by un-checking this option.{/ts}</dd>
        <dt>{$form.user_dashboard_options.label}</dt><dd>{$form.user_dashboard_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Select the sections that should be included in the Contact Dashboard.
            EXAMPLE: If you don't ever want show a constituent their own contribution history on their dashboard, un-check that option.{/ts}</dd>

	{if $action neq 4} {* action is not view *}
           <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
        {else}
            <dl><dt></dt><dd>{$form.done.html}</dd></dl>
        {/if}

</fieldset>
</div>
