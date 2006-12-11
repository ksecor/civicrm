<div class="form-item">
 <dl>
 	<dt>{$form.is_online_registration.label}</dt><dd>{$form.is_online_registration.html}</dd>
    <dt>{$form.regLinkText.label}</dt><dd>{$form.regLinkText.html}</dd>
 </dl>
	<div id="registration[show]" class="data-group-first">
        <a href="#" onclick="hide('registration[show]'); show('registration'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Registration Screen:{/ts}</label><br />
	</div>	

    {*Registration Block*}
	<div id="registration">
    <fieldset><legend><a href="#" onclick="hide('registration'); show('registration[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Registration Screen:{/ts}</legend>
    <dl>
    	<dt>{$form.intro_text.label}</dt><dd>{$form.intro_text.html}</dd>
	    <dt>{$form.footer_text.label}</dt><dd>{$form.footer_text.html}</dd>
    	<dt>{$form.participant_info_1.label}</dt><dd>{$form.participant_info_1.html}</dd>
	    <dt>{$form.participant_info_2.label}</dt><dd>{$form.participant_info_2.html}</dd>
    </dl>
    </fieldset>
	</div>

    {*Confirmation Block*}
	<div id="confirm[show]" class="data-group-first">
        <a href="#" onclick="hide('confirm[show]'); show('confirm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Confirmation Screen:{/ts}</label><br />
	</div>	

	<div id="confirm">
    <fieldset><legend><a href="#" onclick="hide('confirm'); show('confirm[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Confirmation Screen:{/ts}</legend>
    <dl>
    	<dt>{$form.confirm_title.label}</dt><dd>{$form.confirm_title.html}</dd>
	    <dt>{$form.confirm_text.label}</dt><dd>{$form.confirm_text.html}</dd>
    	<dt>{$form.confirm_footer_text.label}</dt><dd>{$form.confirm_footer_text.html}</dd>
    </dl>
    </fieldset>
	</div>

    {*Mail Block*}
	<div id="mail[show]" class="data-group-first">
        <a href="#" onclick="hide('mail[show]'); show('mail'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Send Confirmation Email{/ts}</label><br />
	</div>	

	<div id="mail">
    <fieldset><legend><a href="#" onclick="hide('mail'); show('mail[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Send Confirmation Email{/ts}</legend>
    <dl>
    	<dt>{$form.is_email_confirm.label}</dt><dd>{$form.is_email_confirm.html}</dd>
	    <dt>{$form.confirm_email_text.label}</dt><dd>{$form.confirm_email_text.html}</dd>
    	<dt>{$form.cc_confirm.label}</dt><dd>{$form.cc_confirm.html}</dd>
	    <dt>{$form.bcc_confirm.label}</dt><dd>{$form.bcc_confirm.html}</dd>
    </dl>
    </fieldset>
	</div>

<dl>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
</dl>   
</div>
{if $action eq 1 or $action eq 2 }		 

<script type="text/javascript">
var regElement1 = document.getElementById('registration');
var regElement2 = document.getElementById('registration[show]');

{if $showReg }
  regElement1.style.display = 'block';
  regElement2.style.display = 'none';    
{else}
  regElement1.style.display = 'none';
  regElement2.style.display = 'block';  
{/if}

var confirmElement1 = document.getElementById('confirm');
var confirmElement2 = document.getElementById('confirm[show]');

{if $confirmReg }
  confirmElement1.style.display = 'block';
  confirmElement2.style.display = 'none';    
{else}
  confirmElement1.style.display = 'none';
  confirmElement2.style.display = 'block';  
{/if}


var mailElement1 = document.getElementById('mail');
var mailElement2 = document.getElementById('mail[show]');

{if $mailReg }
  mailElement1.style.display = 'block';
  mailElement2.style.display = 'none';    
{else}
  mailElement1.style.display = 'none';
  mailElement2.style.display = 'block';  
{/if}
{/if}
</script>

