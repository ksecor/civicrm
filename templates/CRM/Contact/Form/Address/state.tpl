{if $form.location.$index.address.state}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.state.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.state.html}
    </span>
</div>
{/if}