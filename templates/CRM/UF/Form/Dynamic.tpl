{if ! empty( $fields )}
{debug}
<div id="crm-container"> {* wrap in crm-container div so crm styles are used *}

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
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <dt>&nbsp;</dt><dd class="description">{ts}{$field.help_post}{/ts}</dd>
        {/if}
    {/foreach}
    </dl>
    </div>
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}