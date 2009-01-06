{* add/update/view custom data group *}

<div class="form-item">
    <fieldset><legend>{ts}Custom Data Group{/ts}</legend>

    <div id="help">
        {ts}Use Custom Data Groups to add sets of logically related fields to a specific type of CiviCRM record (e.g. contact records, contribution records, etc.).{/ts} {help id="id-group_intro"}
    </div>
    <dl>
    <dt>{$form.title.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_custom_group' field='title' id=$gid}{/if}</dt><dd>{$form.title.html} {help id="id-title"}</dd>
    <dt>{$form.extends.label}</dt><dd>{$form.extends.html} {help id="id-extends"}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html} {help id="id-weight"}</dd>
    </dl>

    <div id="style"> {* This section shown only when Used For = Contact, Individ, Org or Household. *}
        <dl><dt>&nbsp;</dt><dd>{$form.is_multiple.html}&nbsp;{$form.is_multiple.label}</dd></dl>
        <div id="multiple">
            {*<dt>{$form.min_multiple.label}</dt><dd>{$form.min_multiple.html}</dd>*}
            <dl><dt>{$form.max_multiple.label}</dt><dd>{$form.max_multiple.html}</dd></dl>
        </div>
        <dl><dt>{$form.style.label}</dt><dd>{$form.style.html} {help id="id-display_style}</dd></dl>
    </div>
    
    <dl>
    <dt>&nbsp;</dt><dd>{$form.collapse_display.html} {$form.collapse_display.label} {help id="id-collapse"}</dd>
    <dt>&nbsp;</dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    <dt>{$form.help_pre.label} <!--{if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_custom_group' field='help_pre' id=$gid}{/if}-->{help id="id-help_pre"}</dt><dd>{$form.help_pre.html}</dd>
    <dt>{$form.help_post.label} <!--{if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_custom_group' field='help_post' id=$gid}{/if}-->{help id="id-help_post"}</dt><dd>{$form.help_post.html}</dd>
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
