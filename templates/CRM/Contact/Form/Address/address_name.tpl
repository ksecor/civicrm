{if $form.location.$index.address.address_name}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.address_name.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.address_name.html}
    <br class="spacer"/>
    <span class="description font-italic">{ts}Name of this aadress block like "nickname,c/o Company Inc.,.." which can be used in address book {/ts}</span>
    </span>
</div>
{/if}