{include file="CRM/common/WizardHeader.tpl"}
<div class="form-item">
<fieldset><legend>{ts}Online Registration{/ts}</legend>
 <dl>
    	<dt>{$form.is_online_registration.label}</dt><dd>{$form.is_online_registration.html}</dd>
        <div id="registrationLink">
 	  <dt>{$form.registration_link_text.label}</dt><dd>{$form.registration_link_text.html}</dd>
        </div>
 </dl>
<div id="registration">
<br /><br />
    {*Registration Block*}
	<div id="registration_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('registration_show'); show('registration'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Registration Screen:{/ts}</label><br />
	</div>	

	<div id="registration">
    <fieldset><legend><a href="#" onclick= "hide('registration'); show('registration_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Registration Screen:{/ts}</legend>
    <dl>
    	<dt>{$form.intro_text.label}</dt><dd>{$form.intro_text.html}</dd>
	    <dt>{$form.footer_text.label}</dt><dd>{$form.footer_text.html}</dd>
    	<dt>{$form.custom_pre_id.label}</dt><dd>{$form.custom_pre_id.html}</dd>
	    <dt>{$form.custom_post_id.label}</dt><dd>{$form.custom_post_id.html}</dd>
    </dl>
    </fieldset>
	</div>

    {*Confirmation Block*}
	<div id="confirm_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('confirm_show'); show('confirm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Confirmation Screen:{/ts}</label><br />
	</div>	

	<div id="confirm">
    <fieldset><legend><a href="#" onclick="hide('confirm'); show('confirm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Confirmation Screen:{/ts}</legend>
    <dl>
    	<dt>{$form.confirm_title.label}</dt><dd>{$form.confirm_title.html}</dd>
	    <dt>{$form.confirm_text.label}</dt><dd>{$form.confirm_text.html}</dd>
    	<dt>{$form.confirm_footer_text.label}</dt><dd>{$form.confirm_footer_text.html}</dd>       
    </dl>
    </fieldset>
	</div>

    {*Mail Block*}
	<div id="mail_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('mail_show'); show('mail'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Send Confirmation Email{/ts}</label><br />
	</div>	

	<div id="mail">
    <fieldset><legend><a href="#" onclick="hide('mail'); show('mail_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Send Confirmation Email{/ts}</legend>
 {strip}
    <dl>
    	<dt>{$form.is_email_confirm.label}</dt><dd>{$form.is_email_confirm.html}</dd>
<div id="confirmEmail">
	    <dt>{$form.confirm_email_text.label}</dt><dd>{$form.confirm_email_text.html}</dd>
        <dt>{$form.confirm_from_name.label}</dt><dd>{$form.confirm_from_name.html}</dd>
        <dt>{$form.confirm_from_email.label}</dt><dd>{$form.confirm_from_email.html}</dd>
    	<dt>{$form.cc_confirm.label}</dt><dd>{$form.cc_confirm.html}</dd>
	    <dt>{$form.bcc_confirm.label}</dt><dd>{$form.bcc_confirm.html}</dd>       
</div>
    </dl>
 {/strip}
    </fieldset>
	</div>

<dl>
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
</dl>  
</fieldset>
</div>
{include file="CRM/common/showHide.tpl"}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_online_registration"
trigger_value       =""
target_element_id   ="registrationLink|registration" 
target_element_type ="block"
field_type          ="radio"
invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_email_confirm"
trigger_value       =""
target_element_id   ="confirmEmail" 
target_element_type ="block"
field_type          ="radio"
invert              = 0
}
