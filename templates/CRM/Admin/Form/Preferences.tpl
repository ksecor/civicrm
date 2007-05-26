{* this template is used for adding/editing relationship types  *}
<div class="form-item">
<fieldset><legend>{if $action eq 2 or $action eq 1}{ts}Edit System Config{/ts}{elseif $action eq 8}{ts}Delete System Config{/ts}{else}{ts}View System Config{/ts}{/if}</legend>
	{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}WARNING: Deleting this option will result in the loss of all configuration settings for the sysem.{/ts}{ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
     {else}
      <dl>
        <dt>{$form.location_count.label}</dt><dd>{$form.location_count.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Number of locations to be displayed when creating and/or editing a contact.{/ts}</dd>
        <dt>{$form.contact_summary_options.label}</dt><dd>{$form.contact_summary_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Tabs that should be displayed in the View Contacts Page{/ts}</dd>
        <dt>{$form.edit_contact_options.label}</dt><dd>{$form.edit_contact_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Sections that should be displayed in the Create/Edit Contacts Page{/ts}</dd>
        <dt>{$form.advanced_search_options.label}</dt><dd>{$form.advanced_search_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Sections that should be displayed in the Advanced Search Form{/ts}</dd>
        <dt>{$form.user_dashboard_options.label}</dt><dd>{$form.user_dashboard_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Sections that should be displayed in the User Dashboard{/ts}</dd>
    {/if}
	{if $action neq 4} {* action is not view *}
           <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
        {else}
            <dl><dt></dt><dd>{$form.done.html}</dd></dl>
        {/if}

</fieldset>
</div>
