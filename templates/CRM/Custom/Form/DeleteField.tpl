{* this template is used for confirmation of delete for a Fields  *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
        <dd>
          {ts 1=$name}WARNING: Deleting this custom field will result in the loss of all "%1" data. All the uf field(s) linked with "%1" will be deleted. This action cannot be undone. Do you want to continue?{/ts}
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
