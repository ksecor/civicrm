<fieldset>
  <dl>
    {foreach from=$locales item=locale}
      {assign var='elem' value="$field $locale"|replace:' ':'_'}
      <dt>{$form.$elem.label}</dt><dd>{$form.$elem.html}</dd>
    {/foreach}
  </dl>
  {if $context == 'dialog'}
    <button type="button" onClick="
      // submit with Ajax
      dojo.xhrPost ({ldelim}
        // post the contents of the Form to the Form's URL
        url: dojo.byId('Form').action,
        form: 'Form',
        // on success update the proper field in the form 'below' and hide popup
        load: function (data) {ldelim}
          dojo.byId('{$field}').value = dojo.byId('{$field}_{$config->lcMessages}').value;
          dijit.byId('i18n-{$field}-dialog').hide();
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
