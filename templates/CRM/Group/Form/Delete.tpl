{* this template is used for confirmation of delete for a group  *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
        <dd>
    Are you sure you want to delete the group {$name}? This group currently has {$count} members
    in it.
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
