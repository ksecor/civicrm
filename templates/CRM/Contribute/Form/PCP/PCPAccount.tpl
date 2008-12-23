{* Displays Test-drive mode header for Contribution pages. *}

{ts 1=$campaignName}This wizard allows you to create you own Personal Campaign Page. It's a simple page with a link to Contribution Page, where you can present your motivation and convice other to support "%1" campaign.{/ts}

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