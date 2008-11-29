{* this template is used for adding/editing email settings.  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Email Settings{/ts}{elseif $action eq 2}{ts}Edit Email Settings{/ts}{else}{ts}Delete Email Settings{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
	{ts}WARNING: Deleting this option will result in the loss of mail settings data.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
{else}
    <table class="form-layout-compressed">

	<tr><td class="label">{$form.name.label}</td><td>{$form.name.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Name of this group of settings.{/ts}</td></tr>

	<tr><td class="label">{$form.username.label}</td><td>{$form.username.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Username to use when polling.{/ts}</td></tr>

	<tr><td class="label">{$form.password.label}</td><td>{$form.password.html}</td>	</tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Password to use when polling.{/ts}</td></tr>
	
	<tr><td class="label">{$form.domain.label}</td><td>{$form.domain.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Email address domain (the part after @).{/ts}</td></tr>

	<tr><td class="label">{$form.return_path.label}</td><td>{$form.return_path.html}</td><tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Contents of the Return-Path header.{/ts}</td></tr>	

	<tr><td class="label">{$form.protocol.label}</td><td>{$form.protocol.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Name of the protocol to use for polling (like IMAP, POP3 or Maildir).{/ts}</td></tr>

	<tr><td class="label">{$form.port.label}</td><td>{$form.port.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Port to use when polling.{/ts}</td></tr>

	<tr><td class="label">{$form.localpart.label}</td><td>{$form.localpart.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Optional local part (like civimail+ for addresses like civimail+s.1.2@example.com).{/ts}</td></tr>

	<tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Folder to poll from when using IMAP, path to poll from when using Maildir, etc..{/ts}</td></tr>

	<tr><td class="label">{$form.is_ssl.label}</td><td>{$form.is_ssl.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Whether to use SSL or not.{/ts}</td></tr>

	<tr><td class="label">{$form.is_default.label}</td><td>{$form.is_default.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Whether this is the default set of settings for this domain.{/ts}</td></tr>	

	<tr><td class="label">&nbsp;</td><td>{$form.buttons.html}</td></tr>
    </table>
{/if}		
<div class="spacer"></div>
</fieldset>
</div>
