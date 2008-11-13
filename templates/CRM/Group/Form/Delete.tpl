{* this template is used for confirmation of delete for a group  *}
<fieldset><legend>{ts}Delete Group {/ts}</legend>
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>
    {ts 1=$title}Are you sure you want to delete the group %1?{/ts}<br /><br />
    {if $count}
        {ts count=$count plural='This group currently has %count members in it.'}This group currently has one member in it.{/ts}
    {/if}
    {ts}Deleting this group will NOT delete the member contact records. However, all contact subscription information and history for this group will be deleted.{/ts} {ts}If this group is used in CiviCRM profiles, those fields will be reset.{/ts} {ts}This operation cannot be undone.{/ts}
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
</fieldset>