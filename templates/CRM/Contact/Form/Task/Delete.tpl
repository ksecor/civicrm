{* Confirmation of contact deletes  *}
<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}
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

</form>