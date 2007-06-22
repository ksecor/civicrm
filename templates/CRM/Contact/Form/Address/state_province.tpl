{if $form.location.$index.address.state_province and $form.location.$index.address.country }
  <div class="form-item">
    <span class="labels">
    {ts}State / Province{/ts}
    </span>
    <span id="country{$index}_children" class="fields">
    </span>
  </div>
{else if $form.location.$index.address.state_province}
  <div class="form-item">
    <span class="labels">
    {$form.location.$index.address.state_province.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.state_province.html}
    </span>
  </div>
{/if}