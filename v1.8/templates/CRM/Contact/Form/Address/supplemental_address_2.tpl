{if $form.location.$index.address.supplemental_address_2}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.supplemental_address_2.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.supplemental_address_2.html}
    {*<div class="description font-italic">{ts}Supplemental address info, e.g. c/o, department name, building name, etc.{/ts}</div>*}
    <br class="spacer"/>
    <span class="description font-italic">{ts}Supplemental address info, e.g. c/o, department name, building name, etc.{/ts}</span>
    </span>
</div>
{/if}
