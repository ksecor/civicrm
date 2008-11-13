{* this template is used for confirmation of delete for a Fields  *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
          {ts 1=$title}WARNING: Deleting this custom field will result in the loss of all '%1' data. Any Profile form and listings field(s) linked with '%1' will also be deleted.{/ts} {ts}This action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
