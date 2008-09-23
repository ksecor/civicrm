{include file="CRM/common/WizardHeader.tpl"}
<div id="form" class="form-item">
    <fieldset><legend>{ts}Configure Personal Campaign Page{/ts}</legend>
    <div id="help">
        {ts}HELP MESSAGE{/ts}
    </div>
    <table class="form-layout-compressed">
    	<tr><td style="width: 12em;">&nbsp;</td><td class="font-size11pt">{$form.is_active.html}&nbsp;{$form.is_active.label}</dd>
    </table>
    <div class="spacer"></div>
    
    <div id="pcpFields">
        <table class="form-layout-compressed">
         <tr><td class="label">{$form.approval_required.label}</td><td>{$form.approval_required.html}</td></tr>
   	 <tr><td class="label">{$form.url_logo.label}</span></td><td>{$form.url_logo.html}</td></tr>  
 	 <tr><td class="label">{$form.tell_a_friend.label}</td><td>{$form.tell_a_friend.html}</td></tr>  
	 <tr><td class="label">{$form.max_recipient_limit.label}</td><td>{$form.max_recipient_limit.html}</td></tr>  
	 <tr><td class="label">{$form.create_pcp.label}</td><td>{$form.create_pcp.html}</td></tr>  
        </table>
    </div>
    <div id="crm-submit-buttons">
        <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>  
    </div>
    </fieldset>

</div>      
{include file="CRM/common/showHide.tpl"}

{literal}
<script type="text/javascript">
	var is_act = document.getElementsByName('is_active');
  	if ( ! is_act[0].checked) {
           hide('pcpFields');
	} 
    function pcpBlock(chkbox) {
        if (chkbox.checked) {
	      show('pcpFields');
	      return;
        } else {
	      hide('pcpFields');
              return;
	   }
    }
</script>
{/literal}