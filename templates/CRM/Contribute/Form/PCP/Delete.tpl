{* this template is used for confirmation of delete for a group *}
<fieldset><legend>{ts}Delete Campaign Page {/ts}</legend>
<div class="messages status">
<dl>
	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
	<dd>{ts 1=$title}Are you sure you want to Campaign Page '%1'?{/ts}<br />
	{ts}This operation cannot be undone.{/ts}</dd>
</dl>
</div>

<div class="form-item">{$form.buttons.html}</div>
</fieldset>