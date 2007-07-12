{* Confirmation of contact deletes  *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
    <dd>
        <p>{ts  1=$displayName}Are you sure you want to delete the contact record and all related information for <strong>%1</strong>?{/ts}</p>
        <p>{ts}This operation cannot be undone.{/ts}</p>
	
    </dd>
    
  </dl>
</div>
<p>
<div class="form-item">
 {$form.buttons.html}
</div>
