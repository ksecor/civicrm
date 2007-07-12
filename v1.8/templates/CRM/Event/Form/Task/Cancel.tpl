{* Confirmation of Cancel Registration *}
<div class="spacer"></div>
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}Are you sure you want to set status to Cancelled for the selected participants?{/ts}</p>
        <p>{include file="CRM/Event/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>

<div class="form-item">
 {$form.buttons.html}
</div>
