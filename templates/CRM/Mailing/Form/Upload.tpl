{include file="CRM/common/WizardHeader.tpl"}
{include file="CRM/Mailing/Form/Count.tpl"}
<div id="help">
<p>
    {ts}Before completing this step, you must create one or two files containing your mailing content.{/ts} {help id="id-upload"}
</p>
<p>
    {ts}CiviMail email messages must include an unsubscribe link, an opt-out link, and the postal address of your organization. These elements help reduce the chances of your email being categorized as SPAM.{/ts} 
    <a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a>
</p>
</div>

<div class="form-item">
  <fieldset><legend>{ts}Content{/ts}</legend>
    <dl>
    <dt class="label">{$form.from_name.label}</dt><dd>{$form.from_name.html}</dd>
    <dt class="label">{$form.from_email.label}</dt><dd>{$form.from_email.html}</dd>
    <dt class="label">{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
    <dt class="label">{$form.upload_type.label}</dt><dd>{$form.upload_type.html}</dd>
    </dl>
    <fieldset id="compose_id">
      <dl class="html-adjust">  
	{if $templates}<dt>{$form.template.label}</dt><dd>{$form.template.html}</dd>{/if}
  	<dt>{$form.text_message.label}</dt><dd>{$form.text_message.html}</dd>
        <dt>{$form.html_message.label}</dt> 
        <dd style="border: 1px solid black; ">{$form.html_message.html}</dd>
      </dl>  
    <div id="editMessageDetails" class="form-item">
      <dl>
         <dt>&nbsp;</dt><dd>{$form.updateTemplate.html}&nbsp;{$form.updateTemplate.label}</dd>
         <dt>&nbsp;</dt><dd>{$form.saveTemplate.html}&nbsp;{$form.saveTemplate.label}</dd>
      </dl>
    </div>
     <div id="saveDetails" class="form-item">
        <dl> 
          <dt>{$form.saveTemplateName.label}</dt><dd>{$form.saveTemplateName.html}</dd>
        </dl>
      </div>
    </fieldset>
    <fieldset id="upload_id"><dl><dt class="label extra-long-fourty">{$form.textFile.label}</dt>
        <dd>{$form.textFile.html}<br />
            <span class="description">{ts}Browse to the <strong>TEXT</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></span>
        </dd>
    <dt class="label extra-long-fourty">{$form.htmlFile.label}</dt>
        <dd>{$form.htmlFile.html}<br />
            <span class="description">{ts}Browse to the <strong>HTML</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></span>
        </dd></fieldset>
    <dt class="label extra-long-fourty">{$form.header_id.label}</dt>
        <dd>{$form.header_id.html}<br />
            <span class="description">{ts}You may choose to include a pre-configured Header block above your message.{/ts}</span>
        </dd>
    <dt class="label extra-long-fourty">{$form.footer_id.label}</dt>
        <dd>{$form.footer_id.html}<br />
            <span class="description">{ts}You may choose to include a pre-configured Footer block below your message. This is a good place to include the required unsubscribe, opt-out and postal address tokens.{/ts}</span>
        </dd>
    </dl> 
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
        }
    }
   
    function selectValue(val)
    {
       var tokens = val.split( "^A" );
       var ed = dojo.widget.byId('editor4');
       dojo.byId('text_message').value=tokens[0];
       ed._htmlEditNode.value=tokens[2];
    }
 
     function verify( select )
     {
	if ( document.getElementsByName("saveTemplate")[0].checked  == false) {
	    document.getElementById("saveDetails").style.display = "none";
	}

	document.getElementById("editMessageDetails").style.display = "block";
	document.getElementById("saveTemplateName").disabled = false;
     }

     function showSaveDetails(chkbox) 
     {
	if (chkbox.checked) {
	    document.getElementById("saveDetails").style.display = "block";
	    document.getElementById("saveTemplateName").disabled = false;
	} else {
	    document.getElementById("saveDetails").style.display = "none";
	    document.getElementById("saveTemplateName").disabled = true;
	}
     }

    document.getElementById("saveDetails").style.display = "none";
    document.getElementById("editMessageDetails").style.display = "none";
</script>
{/literal}
