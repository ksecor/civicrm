{if $form.location.$index.address.postal_code}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.postal_code.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.postal_code.html}
    {if $form.location.$index.address.postal_code_suffix.html}
        - {$form.location.$index.address.postal_code_suffix.html}
        <br class="spacer"/>
        <span class="description font-italic">{ts}Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).{/ts}</span>
    {/if}
    </span>
</div>
{/if}