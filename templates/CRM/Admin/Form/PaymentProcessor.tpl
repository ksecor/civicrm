{* this template is used for adding/editing location type  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Payment Procesor{/ts}{elseif $action eq 2}{ts}Edit Payment Procesor{/ts}{else}{ts}Delete Payment Procesor{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this option may result in some transaction pages being rendered inactive.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
    <dt>{$form.processor.label}</dt><dd>{$form.processor.html}</dd>
    <dt>&nbsp;</dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    <dt>&nbsp;</dt><dd>{$form.is_default.html} {$form.is_default.label}</dd>

<fieldset>
<legend>Processor Details for Live Site</legend>
    <dt>{$form.user_name.label}</dt><dd>{$form.user_name.html}</dd>
    <dt>{$form.password.label}</dt><dd>{$form.password.html}</dd>
    <dt>{$form.signature.label}</dt><dd>{$form.signature.html}</dd>
    <dt>{$form.url_site.label}</dt><dd>{$form.url_site.html}</dd>
    <dt>{$form.url_button.label}</dt><dd>{$form.url_button.html}</dd>
    <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
</fieldset>

<fieldset>
<legend>Processor Details for Test Site</legend>
    <dt>{$form.test_user_name.label}</dt><dd>{$form.test_user_name.html}</dd>
    <dt>{$form.test_password.label}</dt><dd>{$form.test_password.html}</dd>
    <dt>{$form.test_signature.label}</dt><dd>{$form.test_signature.html}</dd>
    <dt>{$form.test_url_site.label}</dt><dd>{$form.test_url_site.html}</dd>
    <dt>{$form.test_url_button.label}</dt><dd>{$form.test_url_button.html}</dd>
    <dt>{$form.test_subject.label}</dt><dd>{$form.test_subject.html}</dd>
</fieldset>

</dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>
