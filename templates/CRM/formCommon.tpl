{if $form.hidden}
  {$form.hidden}
{/if}

{if count($form.errors) gt 0}
   <div class="messages error">
   Please correct the following errors in the form fields below:
   <ul id="errorList">
   {foreach from=$form.errors key=name item=error}
      {if is_array($error)}
         <li>{$error.label} {$error.message}</li>
      {else}
         <li>{$error}</li>
      {/if}
   {/foreach}
   </ul>
   </div>
{/if}


