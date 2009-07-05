<div class="form-item">
<fieldset>
<legend>{ts}Create Printable PDF Letters{/ts}</legend>
<table class="form-layout-compressed">
</table>

{include file="CRM/Contact/Form/Task/PDFLetterCommon.tpl"}

<div class="spacer"> </div>

<dl>
{if $single eq false}
    <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
{/if}
</dl>
<dl>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
