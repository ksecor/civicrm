{if $form.location.$index.address.county_id}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.county_id.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.county_id.html}
    </span>
</div>
{/if}