{* Confirmation of contact deletes  *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
    <dd>
        <p>Are you sure you want to Delete the selected contacts. A Delete operation cannot be undone.</p>
        <p>{include file="CRM/Contact/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>
<p>
<div class="form-item">
 {$form.buttons.html}
</div>
