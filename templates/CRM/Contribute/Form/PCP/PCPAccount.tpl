<div class="form-item">
{include file="CRM/UF/Form/Block.tpl" fields=$fields} 
{if $isCaptcha} 
{include file='CRM/common/ReCAPTCHA.tpl'} 
{/if}
<dl>
	<dt></dt>
	<dd class="html-adjust">{$form.buttons.html}</dd>
</dl>
</div>