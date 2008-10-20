{* Displays Test-drive mode header for Contribution pages. *}
{if $profileDisplay}
<div class="messages status">
<dl>
  	<dt><img src="{$config->resourceBase}i/Eyeball.gif" alt="{ts}Profile{/ts}"/></dt>
    	<dd><p><strong>{ts}Profile is not configured with Email address.{/ts}</strong></p></dd>
</dl>
</div>
{else}
<div class="form-item">
{include file="CRM/common/CMSUser.tpl"} 
{include file="CRM/UF/Form/Block.tpl" fields=$fields} 
{if $isCaptcha} 
{include file='CRM/common/ReCAPTCHA.tpl'} 
{/if}
<dl>
	<dt></dt>
	<dd class="html-adjust">{$form.buttons.html}</dd>
</dl>
</div>
{/if}