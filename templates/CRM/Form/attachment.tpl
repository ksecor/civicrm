  <fieldset><legend>{ts}Attachment(s){/ts}</legend>
  <dl>
        <dt class="label extra-long-fourty">{$form.attachFile_1.label}</dt>
        <dd>{$form.attachFile_1.html}<br />
            <span class="description">{ts}Browse to the <strong>file</strong> you want attached. You can have a maximum of 3 attachments{/ts}</span>
        </dd>
        <dt class="label extra-long-fourty">&nbsp;</dt>
        <dd>{$form.attachFile_2.html}</dd>
        <dt class="label extra-long-fourty">&nbsp;</dt>
        <dd>{$form.attachFile_3.html}</dd>
{if $currentAttachmentURL}
        <dt class="label extra-long-fourty">&nbsp;</dt>
        <dd>{$form.is_delete_attachment.html}&nbsp;{$form.is_delete_attachment.label}<br/>
          <span class="description">{ts}Current Attachments{/ts}: {$currentAttachmentURL}</span>
        </dd>
{/if}
  </dl>
  </fieldset>
