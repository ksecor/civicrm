<fieldset>
  <dl>
    {foreach from=$locales item=locale}
      {assign var='elem' value="$field $locale"|replace:' ':'_'}
      <dt>{$form.$elem.label}</dt><dd>{$form.$elem.html}</dd>
    {/foreach}
  </dl>
  {if $context == 'dialog'}
    <button type="button" onClick="
      // update the proper field in the form 'below' and submit with Ajax
      dojo.byId('{$field}').value = dojo.byId('{$field}_{$config->lcMessages}').value;
      dojo.xhrPost ({ldelim}
        // post the contents of the Form to the Form's URL
        url: dojo.byId('Form').action,
        form: 'Form',
        // on success hide popup
        load: function (data) {ldelim}
          dijit.byId('i18n-{$field}').hide();
        {rdelim},
        // on error alert the user
        error: function (error) {ldelim}
          alert('Error: ', error);
        {rdelim}
      {rdelim});
    ">{ts}Save{/ts}</button>
  {else}
    {$form.buttons.html}
  {/if}
</fieldset>
