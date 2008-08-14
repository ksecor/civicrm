<fieldset>
  <dl>
    {foreach from=$locales item=locale}
      <dt>{$form.$locale.label}</dt><dd>{$form.$locale.html}</dd>
    {/foreach}
  </dl>
  <button type="button" onClick="
    dojo.xhrPost ({ldelim}
      url: dojo.byId('Form').action,
      form: 'Form',
      load: function (data) {ldelim}
        dojo.byId('{$field}').value = dojo.byId('{$config->lcMessages}').value;
        dijit.byId('i18n-{$field}').hide();
      {rdelim},
      error: function (error) {ldelim}
        alert('Error: ', error);
      {rdelim}
    {rdelim});
  ">{ts}Save{/ts}</button> <button type="button" onClick="dijit.byId('i18n-{$field}').hide()">{ts}Cancel{/ts}</button>
</fieldset>
