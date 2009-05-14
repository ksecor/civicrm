{if $form.location.$index.address.state_province_id }
  <div class="form-item">
    <span class="labels">
      {$form.location.$index.address.state_province_id.label}
    </span>
    <span class="fields">
      {$form.location.$index.address.state_province_id.html}
    </span>
  </div>
{/if}
