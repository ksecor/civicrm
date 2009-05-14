{if $form.location.$index.address.geo_code_1}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.geo_code_1.label},
    {$form.location.$index.address.geo_code_2.label}
    </span>
    <span class="fields">
        {$form.location.$index.address.geo_code_1.html},
        {$form.location.$index.address.geo_code_2.html}
        <br class="spacer"/>
        <span class="description font-italic">
            {ts}Latitude and longitude may be automatically populated by enabling a Mapping Provider.{/ts} {docURL page="Mapping and Geocoding"}
        </span>
    </span>
</div>
{/if}
