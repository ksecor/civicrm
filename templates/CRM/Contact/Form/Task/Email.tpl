{* Confirmation of contact deletes  *}
{if $single eq false}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
    <dd>
        <p>{ts}Are you sure you want to email the selected contact(s). An email operation cannot be undone.{/ts}</p>
        <p>{include file="CRM/Contact/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>
{/if}
<p>
<div class="form-item">
<fieldset>
<legend>
{ts}Email Contact(s){/ts}
</legend>
<dl>
<dt>From</dt><dd>{$from|escape}</dd>
{if $single eq false}
<dt>To</dt><dd>{$to|escape}</dd>
{else}
<dt>{$form.to.label}</dt><dd>{$form.to.html}</dd>
{/if}
<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
<dt>{$form.message.label}</dt><dd>{$form.message.html}</dd>
<dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</fieldset>
</div>
