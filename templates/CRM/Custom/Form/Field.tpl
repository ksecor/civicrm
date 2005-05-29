<fieldset><legend>{ts}Custom Data Field{/ts}</legend>

    <div class="form-item">
        <dl>
        <dt>{$form.label.label}</dt><dd>&nbsp;{$form.label.html}</dd>
        <dt>{$form.data_type.label}</dt><dd>&nbsp;{$form.data_type.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Select the type of data you want to collect. The appropriate HTML field type will be selected based on the type of data.{/ts}</dd>
        {/if}
        <dt>{$form.weight.label}</dt><dd>&nbsp;{$form.weight.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which fields are displayed in a group. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/if}
        <dt>{$form.default_value.label}</dt><dd>&nbsp;{$form.default_value.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}If you want to provide a default value for this field, enter it here.{/ts}</dd>
        {/if}
        <dt>{$form.help_post.label}</dt><dd>&nbsp;{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed to users for this field.{/ts}</dd>
        {/if}
        <dt>{$form.is_required.label}</dt><dd>&nbsp;{$form.is_required.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
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
