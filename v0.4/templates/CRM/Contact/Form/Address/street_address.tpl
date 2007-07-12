{if $form.location.$index.address.street_address}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.street_address.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.street_address.html}
    <br class="spacer"/>
    <span class="description font-italic">{ts}Street number, street name, apartment/unit/suite - OR P.O. box{/ts}</span>
    </span>
</div>
{/if}