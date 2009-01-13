{if $form.attachFile_1}
{if $action EQ 4 AND $currentAttachmentURL} {* For View action we exclude the form fields and just show any current attachments. *}
    <fieldset><legend>{ts}Attachment(s){/ts}</legend>
    <table class="form-layout-compressed">
    <tr>
        <td class="label">{ts}Current Attachment(s){/ts}</td>
        <td class="view-value"><strong>{$currentAttachmentURL}</strong></td>
    </tr>
    </table>
    </fieldset>

{elseif $action NEQ 4}
    {if $context EQ 'pcpCampaign'}
        {capture assign=attachTitle}{ts}Include a Picture or an Image{/ts}{/capture}
    {else}
        {capture assign=attachTitle}{ts}Attachment(s){/ts}{/capture}
    {/if}
    <div id="attachments_show" class="section-hidden section-hidden-border">
      <a href="#" onclick="hide('attachments_show'); show('attachments'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{$attachTitle}</label><br />
    </div>

    <div id="attachments" class="section-shown">
    <fieldset><legend><a href="#" onclick="hide('attachments'); show('attachments_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{$attachTitle}</legend>
        {if $context EQ 'pcpCampaign'}
            <div class="description">{ts}You can upload a picture or image to include on your page. Your file should be in .jpg, .gif, or .png format.{/ts}</div>
        {/if}
        <table class="form-layout-compressed">
            <tr>
                <td class="label">{$form.attachFile_1.label}</td>
                <td>{$form.attachFile_1.html}<br />
                    <span class="description">{ts}Browse to the <strong>file</strong> you want to upload.{/ts}{if $numAttachments GT 1} {ts 1=$numAttachments}You can have a maximum of %1 attachment(s).{/ts}{/if}</span>
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
        var attachmentUrl = {/literal}'{$currentAttachmentURL}'{literal};
        if ( attachmentUrl ) {
            show( "attachments" );
            hide( "attachments_show" );
        } else {
            hide( "attachments" );
            show( "attachments_show" );
        }
    </script>
    {/literal}
    {/if}
{/if}

