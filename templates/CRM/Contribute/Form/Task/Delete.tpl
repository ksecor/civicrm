{* Confirmation of contribution deletes  *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}Are you sure you want to delete the selected contributions? This delete operation cannot be undone and will delete all transactions and activity history associated with these contributions.{/ts}</p>
        <p>{include file="CRM/Contribute/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>
<p>
<div class="form-item">
 {$form.buttons.html}
</div>
