{* this template is used for confirmation of delete for a group  *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
        <dd>
    {ts 1=$name}Are you sure you want to delete the group %1?{/ts}
    {ts count=$count plural='This group currently has %count members in it.'}This group currently has one member in it.{/ts}
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
