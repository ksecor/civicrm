<fieldset><legend>{ts}CiviCRM Profile Field{/ts}</legend>
    <div class="form-item">
        <dl>
        <dt>{$form.field_name.label}</dt><dd>{$form.field_name.html}</dd>
        {if $action neq 4}
        <dt> </dt><dd class="description">{ts}Select the CiviCRM field you want to share (expose) on the User Account screens.{/ts}</dd>
        {/if}
        <dt>{$form.is_required.label}</dt><dd>{$form.is_required.html}</dd>
        {if $action neq 4}
        <dt></dt><dd class="description">{ts}Are users required to complete this field?{/ts}</dd>
        {/if}
        <dt>{$form.is_view.label}</dt><dd>{$form.is_view.html}</dd>
        {if $action neq 4}
        <dt></dt><dd class="description">{ts}If checked, users can view but not edit this field for their account.{/ts}</dd>
        {/if}
        <dt>{$form.is_registration.label}</dt><dd>{$form.is_registration.html}</dd>
        {if $action neq 4}
        <dt></dt><dd class="description">{ts}Do you want to include this field in the new account registration form?{/ts}</dd>
        {/if}
        <dt>{$form.visibility.label}</dt><dd>{$form.visibility.html}</dd>
        {if $action neq 4}
        <dt></dt><dd class="description">{ts}Where is this field displayed?{/ts}</dd>
        {/if}
        <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which fields are displayed in a group. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/if}
        <dt>{$form.listings_title.label}</dt><dd>{$form.listings_title.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}When this field is used to aggregate a user listings page, what is the title of that page?{/ts}</dd>
        {/if}
        <dt>{$form.is_match.label}</dt><dd>&nbsp;{$form.is_match.html}</dd>
        {if $action neq 4}
        <dt></dt><dd class="description">{ts}Is this field used to map a newly registered user to an existing contact record?{/ts}</dd>
        {/if}
        <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html}</dd>
        {if $action neq 4}
        <dt></dt><dd class="description">{ts}Explanatory text displayed to users for this field. All fields marked as 'Key to Contacts' will be combined when evaluating a match.{/ts}</dd>
        {/if}
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        </dl>
    </div>
    
    <div id="crm-submit-buttons" class="form-item">
    <dl>
    {if $action ne 4}
        <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
    {else}
        <dt>&nbsp;</dt><dd>{$form.done.html}</dd>
    {/if} {* $action ne view *}
    <dl>
    </div>

</fieldset>
