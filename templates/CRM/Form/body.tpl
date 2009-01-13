{include file="CRM/common/stateCountry.tpl"}

{if $form.javascript}
  {$form.javascript}
{/if}

{if $form.hidden}
  <div>{$form.hidden}</div>
{/if}

{if ! $suppressForm and count($form.errors) gt 0}
   <div class="messages error">
   {ts}Please correct the following errors in the form fields below:{/ts}
   <ul id="errorList">
   {foreach from=$form.errors key=errorName item=error}
      {if is_array($error)}
         <li>{$error.label} {$error.message}</li>
      {else}
         <li>{$error}</li>
      {/if}
   {/foreach}
   </ul>
   </div>
{/if}
