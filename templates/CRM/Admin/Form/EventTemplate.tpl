{* this template is used for adding/editing event template.  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Event Template{/ts}{elseif $action eq 2}{ts}Edit Event Template{/ts}{else}{ts}Delete Event Template{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
	{ts}WARNING: Deleting this option will result in the loss event template.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
{else}
    <table class="form-layout-compressed">

	<tr><td class="label">{$form.template_title.label}</td><td>{$form.template_title.html}</td></tr>
	<tr><td class="label">{$form.event_type_id.label}</td><td>{$form.event_type_id.html}</td></tr>
	<tr><td class="label">{$form.default_role_id.label}</td><td>{$form.default_role_id.html}</td></tr>
	<tr><td class="label">{$form.participant_listing_id.label}</td><td>{$form.participant_listing_id.html}</td></tr>
	<tr><td class="label">{$form.is_public.label}</td><td>{$form.is_public.html}</td></tr>
	<tr><td class="label">{$form.is_monetary.label}</td><td>{$form.is_monetary.html}</td></tr>
	<tr><td class="label">{$form.is_online_registration.label}</td><td>{$form.is_online_registration.html}</td><tr>
	<tr><td class="label">{$form.is_active.label}</td><td>{$form.is_active.html}</td></tr>

	<tr><td class="label">&nbsp;</td><td>{$form.buttons.html}</td></tr>
    </table>
{/if}		
<div class="spacer"></div>
</fieldset>
</div>
