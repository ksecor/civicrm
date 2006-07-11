{* Confirmation of membership deletes  *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}Are you sure you want to delete the selected memberships? This delete operation cannot be undone and will delete all transactions and activity history associated with these members.{/ts}</p>
        <p>{include file="CRM/Member/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>
<p>
<div class="form-item">
 {$form.buttons.html}
</div>
