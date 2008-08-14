<fieldset>
  <dl>
    {foreach from=$locales item=locale}
      <dt>{$form.$locale.label}</dt><dd>{$form.$locale.html}</dd>
    {/foreach}
  </dl>
  <button type="button" onClick="
    dojo.xhrPost ({ldelim}
      // post the contents of the Form to the Form's URL
      url: dojo.byId('Form').action,
      form: 'Form',
      // on success, update the proper field in the form 'below' and hide popup
      load: function (data) {ldelim}
        dojo.byId('{$field}').value = dojo.byId('{$config->lcMessages}').value;
        dijit.byId('i18n-{$field}').hide();
      {rdelim},
      // on error, alert the user
      error: function (error) {ldelim}
        alert('Error: ', error);
      {rdelim}
    {rdelim});
  ">{ts}Save{/ts}</button>
</fieldset>
