{include file="CRM/common/WizardHeader.tpl"}
<fieldset><legend>{ts}Online Registration{/ts}</legend>
<div id="help">
{capture assign=ppUrl}{crmURL p='civicrm/admin/paymentProcessor' q="reset=1"}{/capture}
{ts 1=$ppUrl}If you want to provide an Online Registration page for this event, check the first box below and then complete the fields on this form. You can offer online registration for both Paid and Free events. Paid events require that you have configured a <a href="%1">pament processor</a> for your site.{/ts}
</div>
<div class="form-item">
    <div id="register">
     <dl>
       <dt>{$form.is_online_registration.label}</dt><dd>{$form.is_online_registration.html}</dd>
       <dt>&nbsp;</dt><dd class="description">{ts}Enable or disable online registration for this event.{/ts}</dd>
     </dl>
    </div>
    <div id="registration_blocks">
     <div id="register_show">
        <dl>
            <dt>{$form.registration_link_text.label}</dt><dd>{$form.registration_link_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Display text for link from Event Information to Event Registration pages (e.g. 'Register Now!').{/ts}</dd>
        </dl>
        <dl>
            <dt>{$form.registration_start_date.label}</dt><dd>{$form.registration_start_date.html} 
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1 doTime=1}
            {include file="CRM/common/calendar/body.tpl" dateVar=registration_start_date offset=3 doTime=1 trigger=trigger_event_1 ampm=1}
            </dd>
        </dl>
        <dl>
            <dt>{$form.registration_end_date.label}</dt><dd>{$form.registration_end_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_2 doTime=1}
            {include file="CRM/common/calendar/body.tpl" dateVar=registration_end_date offset=3 doTime=1 trigger=trigger_event_2 ampm=1}
            </dd>
        </dl>
        <dl>
            <dt>{$form.is_multiple_registrations.label}</dt><dd>{$form.is_multiple_registrations.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Enable or disable multiple participant registration for this event.{/ts}</dd>
        </dl>
     </div>
    <div class="spacer"></div>
    <div id="registration">
        {*Registration Block*}
        <div id="registration_screen_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('registration_screen_show'); show('registration_screen'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Registration Screen{/ts}</label><br />
        </div>	

        <div id="registration_screen">
        <fieldset><legend><a href="#" onclick= "hide('registration_screen'); show('registration_screen_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Registration Screen{/ts}</legend>
        <dl>
            <dt>{$form.intro_text.label}</dt><dd>{$form.intro_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Introductory message / instructions for online event registration page (may include HTML formatting tags).{/ts}</dd>
            <dt>{$form.footer_text.label}</dt><dd>{$form.footer_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Optional footer text for registration screen.{/ts}</dd>
            <dt>{$form.custom_pre_id.label}</dt><dd>{$form.custom_pre_id.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Include additional fields on this registration form by configuring and selecting a CiviCRM Profile to be included at the top of the page (immediately after the introductory message).{/ts}{help id="event-profile"}</dd>
            <dt>{$form.custom_post_id.label}</dt><dd>{$form.custom_post_id.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Include additional fields on this registration form by configuring and selecting a CiviCRM Profile to be included at the bottom of the page.{/ts}</dd>
        </dl>
        </fieldset>
        </div>

        {*Confirmation Block*}
        <div id="confirm_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('confirm_show'); show('confirm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Confirmation Screen{/ts}</label><br />
        </div>	

        <div id="confirm">
        <fieldset><legend><a href="#" onclick="hide('confirm'); show('confirm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Confirmation Screen{/ts}</legend>
        <dl>
            <dt>{$form.confirm_title.label} <span class="marker">*</span></dt><dd>{$form.confirm_title.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Page title for screen where user reviews and confirms their registration information.{/ts}</dd>
            <dt>{$form.confirm_text.label}</dt><dd>{$form.confirm_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Optional instructions / message for Confirmation screen.{/ts}</dd>
            <dt>{$form.confirm_footer_text.label}</dt><dd>{$form.confirm_footer_text.html}</dd>       
            <dt>&nbsp;</dt><dd class="description">{ts}Optional page footer text for Confirmation screen.{/ts}</dd>
        </dl>
        </fieldset>
        </div>

         {*ThankYou Block*}
        <div id="thankyou_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('thankyou_show'); show('thankyou'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Thank-you Screen{/ts}</label><br />
        </div>	

        <div id="thankyou">
        <fieldset><legend><a href="#" onclick="hide('thankyou'); show('thankyou_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Thank-you Screen{/ts}</legend>
        <dl>
            <dt>{$form.thankyou_title.label} <span class="marker">*</span></dt><dd>{$form.thankyou_title.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Page title for registration Thank-you screen.{/ts}</dd>
            <dt>{$form.thankyou_text.label}</dt><dd>{$form.thankyou_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Optional message for Thank-you screen (may include HTML formatting).{/ts}</dd>
            <dt>{$form.thankyou_footer_text.label}</dt><dd>{$form.thankyou_footer_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Optional footer text for Thank-you screen (often used to include links to other pages/activities on your site).{/ts}</dd>
        </dl>
        </fieldset>
        </div>

        {* Confirmation Email Block *}
        <div id="mail_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('mail_show'); show('mail'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Confirmation Email{/ts}</label><br />
        </div>	

        <div id="mail">
        <fieldset><legend><a href="#" onclick="hide('mail'); show('mail_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Confirmation Email{/ts}</legend>
        {strip}
        <dl>
            <dt>{$form.is_email_confirm.label}</dt><dd>{$form.is_email_confirm.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Do you want a registration confirmation email sent automatically to the user? This email includes event date(s), location and contact information. For paid events, this email is also a receipt for their payment.{/ts}</dd>
        </dl>
        <div id="confirmEmail">
        <dl>
            <dt>{$form.confirm_email_text.label} </dt><dd>{$form.confirm_email_text.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Additional message or instructions to include in confirmation email.{/ts}</dd>
            <dt>{$form.confirm_from_name.label} <span class="marker">*</span></dt><dd>{$form.confirm_from_name.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}FROM name for email.{/ts}</dd>
            <dt>{$form.confirm_from_email.label} <span class="marker">*</span></dt><dd>{$form.confirm_from_email.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}FROM email address (this must be a valid email account wiht your SMTP email service provider).{/ts}</dd>
            <dt>{$form.cc_confirm.label}</dt><dd>{$form.cc_confirm.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}You can notify event organizers of each online registration by specifying an email address to receive a cc (carbon copy).{/ts}</dd>
            <dt>{$form.bcc_confirm.label}</dt><dd>{$form.bcc_confirm.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}You may specify an email address to receive a blind carbon copy (bcc) of the confirmation email.{/ts}</dd>
        </dl>
        </div>
        {/strip}
        </fieldset>
        </div>
    </div>
    </div> {*end of div registration_blocks*}
    <dl>
    <dt>&nbsp;</dt><dd>
        <div id="crm-submit-buttons">
        {$form.buttons.html}
        </div>
        </dd>
    </dl>  
</div> {* End of form-item div *}
</fieldset>

{include file="CRM/common/showHide.tpl"}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_online_registration"
trigger_value       ="" 
target_element_id   ="registration_blocks" 
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
