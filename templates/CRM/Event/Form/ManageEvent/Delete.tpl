{* this template is used for confirmation of delete for event  *}
<div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
           Warning: Deleting this event will also delete associated Event Registration Page and Event Fee configurations. This operation can not be undone. Do you want to continue?
        </dd>
    </dl>
</div>

<div class="form-item">
    {$form.buttons.html}
</div>
