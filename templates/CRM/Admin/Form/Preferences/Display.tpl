{* this template is used for editing Site Preferences  *}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<div class="form-item">
<fieldset><legend>{if $action eq 2 or $action eq 1}{ts}Site Preferences{/ts}{elseif $action eq 4}{ts}View Site Preferences{/ts}{/if}</legend>
      <table class="form-layout">
        <tr><td class="label">{$form.contact_view_options.label}</td><td>{$form.contact_view_options.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description">{ts 1="http://wiki.civicrm.org/confluence//x/NCs" 2=$docURLTitle}Select the <strong>tabs</strong> that should be displayed when viewing a contact record. EXAMPLE: If your organization does not keep track of 'Relationships', then un-check this option to simplify the screen display. Tabs for Contributions, Pledges, Memberships, Events, Grants and Cases are also hidden if the corresponding component is not enabled (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}</td></tr>
        <tr><td class="label">{$form.contact_edit_options.label}</td><td>{$form.contact_edit_options.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description">{ts}Select the sections that should be included when adding or editing a contact record. EXAMPLE: If your organization does not record Gender and Birth Date for individuals, then simplify the form by un-checking this option.{/ts}</td></tr>
        <tr><td class="label">{$form.advanced_search_options.label}</td><td>{$form.advanced_search_options.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description">{ts}Select the sections that should be included in the Basic and Advanced Search forms. EXAMPLE: If you don't track Relationships - then you do not need this section included in the advanced search form. Simplify the form by un-checking this option.{/ts}</td></tr>
        <tr><td class="label">{$form.user_dashboard_options.label}</td><td>{$form.user_dashboard_options.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description">{ts}Select the sections that should be included in the Contact Dashboard. EXAMPLE: If you don't want constituents to view their own contribution history, un-check that option.{/ts}</td></tr>

        <tr><td class="label">{$form.wysiwyg_editor.label}</td><td>{$form.wysiwyg_editor.html}</td></tr>
        <tr><td>&nbsp;</td><td class="description">{ts}Select the HTML WYSIWYG Editor provided for fields that allow HTML formatting.{/ts}</td></tr>
	{if $action neq 4} {* action is not view *}
           <tr><td></td><td>{$form.buttons.html}</td></tr>
        {else}
            <tr><td></td><td>{$form.done.html}</td></tr>
        {/if}
    </table>
</fieldset>
</div>
