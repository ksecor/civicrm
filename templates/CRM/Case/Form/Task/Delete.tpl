{* Confirmation of contribution deletes  *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}Are you sure you want to delete the selected cases? This will move the case(s) and all associated activities to the Trash.{/ts}</p>
        <p>{include file="CRM/Case/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>
<p>
<div class="form-item">
 {$form.buttons.html}
</div>
