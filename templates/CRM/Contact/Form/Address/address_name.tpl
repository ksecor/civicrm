{if $form.location.$index.address.name}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.name.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.name.html}
    <br class="spacer"/>
    <span class="description font-italic">{ts}Name of this address block like "My House, Work Place,.." which can be used in address book {/ts}</span>
    </span>
</div>
{/if}