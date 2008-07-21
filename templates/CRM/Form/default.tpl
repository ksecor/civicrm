{if isset($suppressForm) and ! $suppressForm}
<form {$form.attributes} >
{/if}

{include file="CRM/Form/body.tpl"}

{include file=$tplFile}

{if isset($suppressForm) and ! $suppressForm}
</form>
{/if}
