{include file="CRM/common/WizardHeader.tpl"}
{include file="CRM/Mailing/Form/Count.tpl"}
<div id="help">
{ts}You can either <strong>upload</strong> the mailing content from your computer OR <strong>compose</strong> the content on this screen. Hold your mouse over the help (?) icon for more information on formats and requirements.{/ts} {help id="content-intro"} 
</div>
<div class="form-item">
  <fieldset>
    <table class="form-layout-compressed">
        <tr><td class="label">{$form.from_email_address.label}</td>
            <td>{$form.from_email_address.html}</td>
        </tr>
        <tr><td class="label">{$form.subject.label}</td>
            <td colspan="2">{$form.subject.html|crmReplace:class:huge}</td>
        </tr>
        <tr><td></td><td colspan="2">{$form.override_verp.label}{$form.override_verp.html}<br /><span class="description">{ts}If checked default VERP address in Reply-To will be override by From address{/ts}</span></td></tr>  
        <tr><td></td><td colspan="2">{$form.upload_type.label} {$form.upload_type.html} {help id="upload-compose"}</td></tr>
    </table>
  </fieldset>

  <fieldset id="compose_id"><legend>{ts}Compose On-screen{/ts}</legend>
	{include file="CRM/Contact/Form/Task/EmailCommon.tpl" upload=1 noAttach=1}
  </fieldset>

  {capture assign=docLink}{docURL page="Sample CiviMail Messages" text="More information and sample messages..."}{/capture}
  <fieldset id="upload_id"><legend>{ts}Upload Content{/ts}</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.textFile.label}</td>
            <td>{$form.textFile.html}<br />
                <span class="description">{ts}Browse to the <strong>TEXT</strong> message file you have prepared for this mailing.{/ts}<br /> {$docLink}</span>
            </td>
        </tr>
        <tr>
            <td class="label">{$form.htmlFile.label}</td>
            <td>{$form.htmlFile.html}<br />
                <span class="description">{ts}Browse to the <strong>HTML</strong> message file you have prepared for this mailing.{/ts}<br /> {$docLink}</span>
            </td>
        </tr>
    </table>
  </fieldset>

  {include file="CRM/Form/attachment.tpl"}

  <fieldset><legend>{ts}Header / Footer{/ts}</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.header_id.label}</td>
            <td>{$form.header_id.html}<br />
                <span class="description">{ts}You may choose to include a pre-configured Header block above your message.{/ts}</span>
            </td>
        </tr>
        <tr>
            <td class="label">{$form.footer_id.label}</td>
            <td>{$form.footer_id.html}<br />
                <span class="description">{ts}You may choose to include a pre-configured Footer block below your message. This is a good place to include the required unsubscribe, opt-out and postal address tokens.{/ts}</span>
            </td>
        </tr>
    </table> 
  </fieldset>

  <dl>
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
  </dl>
</div>

{* -- Javascript for showing/hiding the upload/compose options -- *}
{include file="CRM/common/showHide.tpl"}
{literal}
<script type="text/javascript">
    showHideUpload();
    function showHideUpload()
    { 
	if (document.getElementsByName("upload_type")[0].checked) {
            hide('compose_id');
	    show('upload_id');	
        } else {
            show('compose_id');
	    hide('upload_id');
            verify( );
        }
    }
</script>
{/literal}
