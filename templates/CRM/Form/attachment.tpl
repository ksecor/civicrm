{if $form.attachFile_1}
<div id="attachments_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('attachments_show'); show('attachments'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Attachment(s){/ts}</label><br />
</div>

<div id="attachments" class="section-shown">
<fieldset><legend><a href="#" onclick="hide('attachments'); show('attachments_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Attachment(s){/ts}</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.attachFile_1.label}</td>
            <td>{$form.attachFile_1.html}<br />
                <span class="description">{ts 1=$numAttachments}Browse to the <strong>file</strong> you want attached. You can have a maximum of %1 attachment(s).{/ts}</span>
            </td>
        </tr>
{section name=attachLoop start=2 loop=$numAttachments+1}
    {assign var=index value=$smarty.section.attachLoop.index}
    {assign var=attachName value="attachFile_"|cat:$index}
        <tr>
            <td class="label"></td>
            <td>{$form.$attachName.html}</td>
        </tr>
{/section}
{if $currentAttachmentURL}
    <tr>
        <td class="label">{ts}Current Attachment(s){/ts}</td>
        <td class="view-value"><strong>{$currentAttachmentURL}</strong></td>
    </tr>
    <tr>
        <td class="label">&nbsp;</td>
        <td>{$form.is_delete_attachment.html}&nbsp;{$form.is_delete_attachment.label}<br />
            <span class="description">{ts}Check this box and click Save to delete all current attachments.{/ts}</span>
        </td>
    </tr>
{/if}
    </table>
</fieldset>
</div>

{literal}
<script type="text/javascript">
    hide('attachments_show');
</script>
{/literal}

{/if}