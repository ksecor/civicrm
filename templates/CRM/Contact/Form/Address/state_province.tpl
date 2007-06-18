{if $form.location.$index.address.state_province}
<div class="form-item">
    <span class="labels">
    {ts}State / Province{/ts}
    {*$form.location.$index.address.state_province_id.label*}
    </span>
    <span id="country{$index}_children" class="fields">
    {*$form.location.$index.address.state_province_id.html*}
    </span>
</div>
{/if}