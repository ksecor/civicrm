{* Base template for case activities like - Open Case, Change Case Type/Status ..*}
<fieldset><legend id="caseBlockTitle">{ts}Case Action{/ts}</legend>
{include file="CRM/Case/Form/Activity/$caseAction.tpl"}
{include file="CRM/Custom/Form/CustomData.tpl"}
</fieldset>
