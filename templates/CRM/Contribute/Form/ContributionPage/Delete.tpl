{* this template is used for confirmation of delete for a group  *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
    {ts 1=$title}Are you sure you want to delete the contribution page "%1"?{/ts}<br /><br />
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
