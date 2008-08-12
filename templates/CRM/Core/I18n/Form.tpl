<dl>
  {foreach from=$locales item=locale}
    <dt>{$form.$locale.label}</dt><dd>{$form.$locale.html}</dd>
  {/foreach}
</dl>
{$form.buttons.html}
