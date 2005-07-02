<fieldset><legend>{ts}Custom Data Field{/ts}</legend>

    <div class="form-item">
        <dl>
        <dt>{$form.label.label}</dt><dd>{$form.label.html}</dd>
        <dt>{$form.data_type.label}</dt><dd>{$form.data_type.html}</dd>
        {if $action neq 4 and $action neq 2}
            <dt>&nbsp;</dt><dd class="description">{ts}Select the type of data you want to collect and store for this contact.{/ts}</dd>
        {/if}
        <dt>{$form.html_type.label}</dt><dd>{$form.html_type.html}</dd>
        {if $action neq 4 and $action neq 2}
            <dt>&nbsp;</dt><dd class="description">{ts}Select from the available HTML input field types (choices are based on the type of data being collected).{/ts}</dd>
        {/if}
        </dl>

        {if $action eq 1}
	{if $optionRowError}
	<div id='showOptionError' style='display: none'>{ include file="CRM/Custom/Form/OptionFieldsError.tpl"}</div>
	{else}
        {* Conditionally show table for setting up selection options - for field types = radio, checkbox or select *}
        <div id='showoption' style='display: none'>{ include file="CRM/Custom/Form/Optionfields.tpl"}</div>
	{/if}
        {/if}

        <dl>
        <dt>{$form.weight.label}</dt><dd>{$form.weight.html|crmReplace:class:two}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which fields are displayed in a group. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/if}
        <dt id="hideDefaultValTxt" name="hideDefaultValTxt" {if $action eq 2 && $form.data_type.value.0 < 4}style="display: none"{/if}>{$form.default_value.label}</dt>
        <dd id="hideDefaultValDef" name="hideDefaultValDef" {if $action eq 2 && $form.data_type.value.0 < 4}style="display: none"{/if}>{$form.default_value.html}</dd>
        {if $action neq 4}
        <dt id="hideDescTxt" name="hideDescTxt" {if $action eq 2 && $form.data_type.value.0 < 4}style="display: none"{/if}>&nbsp;</dt>
        <dd id="hideDescDef" name="hideDescDef" {if $action eq 2 && $form.data_type.value.0 < 4}style="display: none"{/if}><span class="description">{ts}If you want to provide a default value for this field, enter it here.{/ts}</span></dd>
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
	
<script type="text/javascript">
	{if $optionRowError AND $action eq 1}
	    show('showOptionError');
	{/if}
	{if $fieldError AND $action eq 1}
	    show('showoption');	
	{/if}
	</script>
{* Give link to view/edit choice options if in edit mode and html_type is one of the multiple choice types *}
{if $action eq 2 AND ($html_type eq 'Checkbox' OR $html_type eq 'Radio' OR $html_type eq 'Select') }
    <div class="action-link">
        <a href="{crmURL p="civicrm/admin/custom/group/field/option" q="reset=1&action=browse&fid=`$id`"}">&raquo; Multiple Choice Options</a>
    </div>
{/if}
