{* this template is used for confirmation of delete for a file  *}
<fieldset><legend>{ts}Delete Attached File{/ts}</legend>
    <div class="status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>
          {ts}WARNING: Are you sure you want to delete the attached file?{/ts}
        </dd>
      </dl>
    </div>

<dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
</fieldset>
