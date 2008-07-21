{if ! $suppressForm}
<form {$form.attributes} >
{/if}

{include file="CRM/Form/body.tpl"}

{include file=$tplFile}

{if ! $suppressForm}
</form>
{/if}
