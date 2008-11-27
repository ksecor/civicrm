{* add/update/view custom data group *}

<div class="form-item">
    <fieldset><legend>{ts}Custom Data Group{/ts}</legend>
    <div id="help">
        <p>
        {ts}Use this form to setup the title, group-level help, and display characteristics of each group of Custom Data fields. The 'Display Style' you select determines whether this group is edited and displayed on the same screens as the standard contact field ('Inline' style), or has it's own menu tab ('Tab' style).{/ts}
        </p>
    </div>
    <dl>
    <dt>{$form.title.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_custom_group' field='title' id=$gid}{/if}</dt><dd>{$form.title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}For 'inline' display custom groups, this name will appear as the fieldset legend. If this group uses the 'tab' display style, this name will be used for the navigation tab.{/ts}</dd>
    <dt>{$form.extends.label}</dt><dd>{$form.extends.html}</dd>
    <dt class="extra-long-twenty">&nbsp;</dt><dd class="description">{ts}Select the type of record that this group of custom fields is applicable for. You can configure custom data for a specific type of contact (e.g. Individuals but NOT Organizations), ANY type of contact, or other record types such as activities and contributions.{/ts}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which custom data groups are presented when there are more than one. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
    </dl>
    <dl id="style">
    <dt>&nbsp;</dt><dd>{$form.is_multiple.html}&nbsp;{$form.is_multiple.label}</dd>	
    <div id="multiple">
    	<dt>{$form.min_multiple.label}</dt><dd>{$form.min_multiple.html}</dd>
        <dt>{$form.max_multiple.label}</dt><dd>{$form.max_multiple.html}</dd>
    </div>
    <dt>{$form.style.label}</dt><dd>{$form.style.html}</dd>
    <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}Select 'Inline' to include this group of fields in the main contact Add/Edit form and Contact Summary screens. Select 'Tab' to create a separate navigation tab for display and editing these values (generally for less frequently accessed and/or larger sets of fields). <strong>NOTE: This setting applies to custom groups used for Contact records only.</strong>{/ts}</dd>
    </dl>
    <dl>
    <dt>&nbsp;</dt><dd>{$form.collapse_display.html} {$form.collapse_display.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want only the title for this fieldset to be displayed when the page is initially loaded (fields are hidden).{/ts}</dd>
    <dt>&nbsp;</dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    <dt>{$form.help_pre.label} <!--{if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_custom_group' field='help_pre' id=$gid}{/if}--></dt><dd>{$form.help_pre.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of this group of fields.{/ts}</dd>
    <dt>{$form.help_post.label} <!--{if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_custom_group' field='help_post' id=$gid}{/if}--></dt><dd>{$form.help_post.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed below this group of fields.{/ts}</dd>
    {if $action ne 4}
        <dt>&nbsp;</dt>
        <dd>
        <div id="crm-submit-buttons">{$form.buttons.html}</div>
        </dd>
    {else}
        <dt>&nbsp;</dt>
        <dd>
        <div id="crm-done-button">{$form.done.html}</div>
        </dd>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p></p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/custom/group/field' q="action=browse&reset=1&gid=$gid"}">&raquo;  {ts}Custom Fields for this Group{/ts}</a>
    </div>
{/if}
{$initHideBlocks}
{literal}
<script type="text/Javascript">

showHideStyle();

var freeze = {/literal}"{$freeze}"{literal};

if ( !freeze ) {
     showRange();
}	


function showHideStyle()
{
	if( document.forms.Group['extends[0]'].value == "Contact"    ||
            document.forms.Group['extends[0]'].value == "Individual" ||
            document.forms.Group['extends[0]'].value == "Household"  ||
            document.forms.Group['extends[0]'].value == "Organization" )
	{
		show("style");
	} else {
		hide("style");
	}
}

function showRange()
{
	var checkbox = document.getElementsByName("is_multiple");

	if (checkbox[0].checked) {
           show('multiple');
        } else { 
	   hide('multiple');
  	} 
  }

</script>
{/literal}
