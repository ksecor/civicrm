<fieldset>
  <dl>
    {foreach from=$locales item=locale}
      <dt>{$form.$locale.label}</dt><dd>{$form.$locale.html}</dd>
    {/foreach}
  </dl>
  <button type="button" onClick="{literal}
    dojo.xhrPost ({
      url: dojo.byId('Form').action,
      form: 'Form',
      load: function (data) {
        dijit.byId('i18n-{/literal}{$field}{literal}').hide();
      },
      error: function (error) {
        alert('Error: ', error);
      }
    });
  {/literal}">{ts}Save{/ts}</button> <button type="button" onClick="dijit.byId('i18n-{$field}').hide()">{ts}Cancel{/ts}</button>
  <!--{$form.buttons.html}-->
</fieldset>
