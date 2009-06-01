{* this template is used for confirmation of delete for event  *}
<div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
          {if $isTemplate}
            {ts}Warning: Deleting this event template will also delete associated Event Registration Page and Event Fee configurations.{/ts} {ts}This operation cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          {else}
            {ts}Warning: Deleting this event will also delete associated Event Registration Page and Event Fee configurations.{/ts} {ts}This operation cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
          {/if}
        </dd>
    </dl>
</div>

<div class="form-item">
    {$form.buttons.html}
</div>
