{include file="CRM/common/WizardHeader.tpl"}
{include file="CRM/Mailing/Form/Count.tpl"}
<div id="help">
{ts}You can either <strong>upload</strong> the mailing content from your computer OR <strong>compose</strong> the content on this screen.
Hold your mouse over the help (?) icon for more information on formats and requirements.{/ts} {help id="content-intro"} 
</div>
<div class="form-item">
  <fieldset>
    <table class="form-layout-compressed">
        <tr><td class="label">{$form.from_email.label}</td>
            <td>{$form.from_name.html}<br /><span class="description">Name</span></td>
            <td>{$form.from_email.html}<br /><span class="description">Email Address</span></td></tr>
        <tr><td class="label">{$form.subject.label}</td><td colspan="2">{$form.subject.html|crmReplace:class:huge}</td></tr>
        <tr><td></td><td colspan="2">{$form.upload_type.label} {$form.upload_type.html} {help id="upload-compose"}</td></tr>
    </table>
  </fieldset>

  <fieldset id="compose_id"><legend>{ts}Compose On-screen{/ts}</legend>
      <dl class="html-adjust"> 
	{if $template_value}
		<script type="text/javascript">
  			dojo.addOnLoad( function( ) {ldelim}
    			dojo.widget.byId( 'template' ).setAllValues( '{$template_value[0]}', '{$template_value[1]}' ) 
			{rdelim} );
		</script>
	{/if} 
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

  <fieldset id="upload_id"><legend>{ts}Upload Content{/ts}</legend>
        <dl>
        <dt class="label extra-long-fourty">{$form.textFile.label}</dt>
        <dd>{$form.textFile.html}<br />
            <span class="description">{ts}Browse to the <strong>TEXT</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></span>
        </dd>
        <dt class="label extra-long-fourty">{$form.htmlFile.label}</dt>
        <dd>{$form.htmlFile.html}<br />
            <span class="description">{ts}Browse to the <strong>HTML</strong> message file you have prepared for this mailing.{/ts}<br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank" title="{ts}Help on messages. Opens a new window.{/ts}">{ts}More information and sample messages...{/ts}</a></span>
        </dd>
        </dl>
  </fieldset>

  <fieldset><legend>{ts}Header / Footer{/ts}</legend>
    <dl>
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
       var ed = dojo.widget.byId('html_message');
       dojo.byId('text_message').value=tokens[0];
       ed.editNode.innerHTML = tokens[2];
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
