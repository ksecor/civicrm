{*Javascript function controls showing and hiding of form elements based on html type.*}
{literal}
<script type="text/Javascript">
    function custom_option_html_type(form) { 
        var html_type = document.getElementsByName("data_type[1]")[0];
        var html_type_name = html_type.options[html_type.selectedIndex].value;
        var data_type = document.getElementsByName("data_type[0]")[0];
        if (data_type.selectedIndex < 4) {
            if (html_type_name != "Text") {
	    document.getElementById("showoption").style.display="block";		
            document.getElementById("hideDefaultValTxt").style.display="none";
            document.getElementById("hideDefaultValDef").style.display="none";
            document.getElementById("hideDescTxt").style.display="none";
            document.getElementById("hideDescDef").style.display="none";
            } else {
	    document.getElementById("showoption").style.display="none";
	    document.getElementById("showoption").style.display="none";
            document.getElementById("hideDefaultValTxt").style.display="block";
            document.getElementById("hideDefaultValDef").style.display="block";
            document.getElementById("hideDescTxt").style.display="block";
            document.getElementById("hideDescDef").style.display="block";
            }
        } else {
	    document.getElementById("showoption").style.display="none";
            document.getElementById("hideDefaultValTxt").style.display="block";
            document.getElementById("hideDefaultValDef").style.display="block";
            document.getElementById("hideDescTxt").style.display="block";
            document.getElementById("hideDescDef").style.display="block";
        }
	
	var radioOption, checkBoxOption;

	for (var i=1; i<=11; i++) {
	    radioOption = 'radio'+i;
	    checkBoxOption = 'checkbox'+i	
	    if (data_type.selectedIndex < 4) {
                 if (html_type_name != "Text") {
		     if (html_type_name == "CheckBox") {
	                 document.getElementById(checkBoxOption).style.display="block";
		         document.getElementById(radioOption).style.display="none";
		     } else {
                         document.getElementById(radioOption).style.display="block";	
		         document.getElementById(checkBoxOption).style.display="none";
		     }
		 }
	    }
	}

	if (data_type.selectedIndex < 4) {	
		if (html_type_name == "CheckBox" || html_type_name == "Radio") {
			document.getElementById("optionsPerLine").style.display="block";
			document.getElementById("optionsPerLineDef").style.display="block";
		} else {
			document.getElementById("optionsPerLine").style.display="none";
			document.getElementById("optionsPerLineDef").style.display="none";
		}
	}
			 

    }
</script>
{/literal}
<fieldset><legend>{ts}Custom Data Field{/ts}</legend>

    <div class="form-item">
        <dl>
        <dt>{$form.label.label}</dt><dd>{$form.label.html}</dd>
        <dt>{$form.data_type.label}</dt><dd>{$form.data_type.html}</dd>
        {if $action neq 4 and $action neq 2}
            <dt>&nbsp;</dt><dd class="description">{ts}Select the type of data you want to collect and store for this contact. Then select from the available HTML input field types (choices are based on the type of data being collected).{/ts}</dd>
        {/if}
        </dl>

    {if $action eq 1}
        {* Conditionally show table for setting up selection options - for field types = radio, checkbox or select *}
        <div id='showoption' style='display: none'>{ include file="CRM/Custom/Form/Optionfields.tpl"}</div>
    {/if}

        <dl>
	<dt id="optionsPerLine" {if $action eq 2 && ($form.data_type.value.0.0 < 4 && $form.data_type.value.1.0 EQ 'CheckBox' || $form.data_type.value.1.0 EQ 'Radio' )}style="display: block"{else} style="display: none" {/if}>{$form.options_per_line.label}</dt>	
	<dd id="optionsPerLineDef" {if $action eq 2 && ($form.data_type.value.0.0 < 4 && $form.data_type.value.1.0 EQ 'CheckBox' || $form.data_type.value.1.0 EQ 'Radio' )}style="display: block"{else} style="display: none" {/if}>{$form.options_per_line.html|crmReplace:class:two}</dd>

        <dt>{$form.weight.label}</dt><dd>{$form.weight.html|crmReplace:class:two}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which fields are displayed in a group. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/if}
        <dt id="hideDefaultValTxt" title="hideDefaultValTxt" {if $action eq 2 && ($form.data_type.value.0.0 < 4 && $form.data_type.value.1.0 NEQ 'Text')}style="display: none"{/if}>{$form.default_value.label}</dt>
        <dd id="hideDefaultValDef" title="hideDefaultValDef" {if $action eq 2 && ($form.data_type.value.0.0 < 4 && $form.data_type.value.1.0 NEQ 'Text')}style="display: none"{/if}>{$form.default_value.html}</dd>
        {if $action neq 4}
        <dt id="hideDescTxt" title="hideDescTxt" {if $action eq 2 && ($form.data_type.value.0.0 < 4 && $form.data_type.value.1.0 NEQ 'Text')}style="display: none"{/if}>&nbsp;</dt>
        <dd id="hideDescDef" title="hideDescDef" {if $action eq 2 && ($form.data_type.value.0.0 < 4 && $form.data_type.value.1.0 NEQ 'Text')}style="display: none"{/if}><span class="description">{ts}If you want to provide a default value for this field, enter it here.{/ts}</span></dd>
        {/if}
        <dt>{$form.help_post.label}</dt><dd>&nbsp;{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed to users for this field.{/ts}</dd>
        {/if}
        <dt>{$form.is_required.label}</dt><dd>&nbsp;{$form.is_required.html}</dd>
	<dt>{$form.is_searchable.label}</dt><dd>&nbsp;{$form.is_searchable.html}</dd>
	{* <dt>&nbsp;</dt><dd class="description">{ts}If you want to mark this field as search field in advanced search.{/ts}</dd>*}
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
    </dl>
    </div>

</fieldset>
	
<script type="text/javascript">
	{if ($optionRowError OR $fieldError) AND $action eq 1}
         custom_option_html_type(document.getElementById('Field'));
	{/if}
	</script>
{* Give link to view/edit choice options if in edit mode and html_type is one of the multiple choice types *}
{if $action eq 2 AND ($form.data_type.value.1.0 eq 'CheckBox' OR $form.data_type.value.1.0 eq 'Radio' OR $form.data_type.value.1.0 eq 'Select') }
    <div class="action-link">
        <a href="{crmURL p="civicrm/admin/custom/group/field/option" q="reset=1&amp;action=browse&amp;fid=`$id`"}">&raquo; {ts}View / Edit Multiple Choice Options{/ts}</a>
    </div>
{/if}
