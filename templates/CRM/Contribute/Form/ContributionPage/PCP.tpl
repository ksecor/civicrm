{include file="CRM/common/WizardHeader.tpl"}
<div id="pcp-form" class="form-item">
	<fieldset> 
	{if !$profile} 
	{capture assign=pUrl}{crmURL p='civicrm/admin/uf/group' q="reset=1"}{/capture}
	<div class="status message">
		{ts 1=$pUrl}No Profile with a user account registration option has been configured / enabled for your site. You need to <a href='%1'>configure a Supporter profile</a> first. It will be used to collect or update basic information from users while they are creating a Personal Campaign Page.{/ts}
	</div>
	{/if}
	<div id="help">
		{ts 1=$title}Allow constituents to create their own personal fundraising pages linked to this contribution page (%1).{/ts}
	</div>
    <dl>
        <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    </dl>
	<div class="spacer"></div>

	<div id="pcpFields">
		<dl>
			<dt>{$form.is_approval_needed.label}</dt><dd>{$form.is_approval_needed.html} {help id="id-approval_needed"}</dd>

           	<dt>{$form.notify_email.label}</dt><dd>{$form.notify_email.html} {help id="id-notify"}</dd>

			<dt>{$form.supporter_profile_id.label} <span class="marker"> *</span></dt><dd>{$form.supporter_profile_id.html} {help id="id-supporter_profile"}</dd>

			<dt>{$form.is_tellfriend_enabled.label}</dt><dd>{$form.is_tellfriend_enabled.html} {help id="id-is_tellfriend"}</dd>
        
            <div id="tflimit">
			    <dt>{$form.tellfriend_limit.label}</dt><dd>{$form.tellfriend_limit.html|crmReplace:class:four} {help id="id-tellfriend_limit"}</dd>
            </div>
			<dt>{$form.link_text.label}</dt><dd>{$form.link_text.html|crmReplace:class:huge} {help id="id-link_text"}</dd>
		</dl>
	</div>
	<div class="spacer"></div>
	<div id="crm-submit-buttons">
	<dl>
		<dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
	</dl>
	</div>
	</fieldset>
</div>
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    = "is_active"
    trigger_value       = "true"
    target_element_id   = "pcpFields" 
    target_element_type = "table-row"
    field_type          = "radio"
    invert              = "false"
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    = "is_tellfriend_enabled"
    trigger_value       = "true"
    target_element_id   = "tflimit" 
    target_element_type = "table-row"
    field_type          = "radio"
    invert              = "false"
}