
{if $form.javascript}
  {$form.javascript}
{/if}

{if $form.hidden}
  {$form.hidden}
{/if}

{if count($form.errors) gt 0}
   <div class="messages error">
   {ts}Please correct the following errors in the form fields below:{/ts}
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

<div class="form-item">
    <dl>
{foreach from=$fields item=field key=name}
{assign var=n value=$field.name}
<dt>{$form.edit.$n.label}</dt><dd>{$form.edit.$n.html}</dd>
{/foreach}
    </dl>
</div>
