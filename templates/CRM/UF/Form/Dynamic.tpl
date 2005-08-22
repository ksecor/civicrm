{if ! empty( $fields )}
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

    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {assign var=n value=$field.name}
        <tr><td class="label">{$form.edit.$n.label}</td><td>{$form.edit.$n.html}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td>&nbsp;</td><td class="description">{ts}{$field.help_post}{/ts}</td></tr>
        {/if}
    {/foreach}
    </table>
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}