  <fieldset><legend>{ts}Attachment(s){/ts}</legend>
  <dl>
        <dt class="label extra-long-fourty">{$form.attachFile_1.label}</dt>
        <dd>{$form.attachFile_1.html}<br />
            <span class="description">{ts 1=$config->maxAttachments}Browse to the <strong>file</strong> you want attached. You can have a maximum of %1 attachments{/ts}</span>
        </dd>
{section name=attachLoop start=2 loop=$config->maxAttachments+1}
{assign var=index value=$smarty.section.attachLoop.index}
{assign var=attachName value="attachFile_"|cat:$index}
        <dt class="label extra-long-fourty">&nbsp;</dt>
        <dd>{$form.$attachName.html}</dd>
{/section}
{if $currentAttachmentURL}
        <dt class="label extra-long-fourty">&nbsp;</dt>
        <dd>{$form.is_delete_attachment.html}&nbsp;{$form.is_delete_attachment.label}<br/>
          <span class="description">{ts}Current Attachments{/ts}: {$currentAttachmentURL}</span>
        </dd>
{/if}
  </dl>
  </fieldset>
