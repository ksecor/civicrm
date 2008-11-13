{* this template is used for confirmation of delete for location  *}
<div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
           {ts}Warning: Deleting this location will also delete associated address, email, phone, etc related information. This operation cannot be undone. Do you want to continue?{/ts}
        </dd>
    </dl>
</div>

<div class="form-item">
    {$form.buttons.html}
</div>
