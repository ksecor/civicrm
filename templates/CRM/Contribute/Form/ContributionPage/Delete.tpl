{* this template is used for confirmation of delete for a group  *}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
    {ts 1=$title}"You cannot delete this Conribution Page because it has already been used to submit a contribution or membership payment. It is recommended that your disable the page instead of deleting it, to preserve the integrity of your contribution records. If you do want to completely delete this contribution page, you first need to search for and delete all of the contribution transactions associated with this page in CiviContribute."{/ts}
        </dd>
      </dl>
    </div>

<div class="form-item">
    {$form.buttons.html}
</div>
