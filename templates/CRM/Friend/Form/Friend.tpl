{include file="CRM/common/WizardHeader.tpl"}

<div id="form" class="form-item">
    <fieldset><legend>{ts}Tell A Friend{/ts}</legend>
    <dl>
    	<dt></dt><dd>{$form.is_active.html}&nbsp;{$form.is_active.label}</dd>
    </dl>
    <div id="friendFields">
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>   
    <dt>{$form.intro.label}</dt><dd>{$form.intro.html}</dd>     
    <dt>{$form.suggested_message.label}</dt><dd>{$form.suggested_message.html}</dd>                            
    <dt>{$form.general_link.label}</dt><dd>{$form.general_link.html}</dd>             
    <dt>{$form.thankyou_title.label}</dt><dd>{$form.thankyou_title.html}</dd>            
    <dt>{$form.thankyou_text.label}</dt><dd>{$form.thankyou_text.html}</dd>    
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd> 
    </dl>	
    </div>
    </fieldset>
 </div>      

{literal}
<script type="text/javascript">
	var is_act = document.getElementsByName('is_active');
  	if ( ! is_act[0].checked) {
           hide('friendFields');
	}
       function friendBlock(chkbox) {
           if (chkbox.checked) {
	      show('friendFields');
	      return;
           } else {
	      hide('friendFields');
    	      return;
	   }
       }
</script>
{/literal}