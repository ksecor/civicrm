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
			<dt>{$form.is_approval_needed.label}</dt><dd>{$form.is_approval_needed.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Administrator will need to approve new Personal Campaign Pages before they are available publically.{/ts}</dd>

			<dt>{$form.supporter_profile_id.label} <span class="marker"> *</span></dt><dd>{$form.supporter_profile_id.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}This profile is used to collect or update basic information (e.g. name and email address) from users while they are creating a Personal Campaign Page. The profile will include creating a user account if they don't already have one.{/ts}</dd>

			<dt>{$form.is_tellfriend_enabled.label}</dt><dd>{$form.is_tellfriend_enabled.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Can the "owner" of a Personal Campaign Page use the Tell-a-Friend function to invite people to visit their page and make a contribution?{/ts}</dd>

			<dt>{$form.tellfriend_limit.label}</dt><dd>{$form.tellfriend_limit.html|crmReplace:class:four}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}How many recipients can they send emails to at one time? You may want to limit this to prevent large mail blasts from being sent.{/ts}</dd>

			<dt>{$form.link_text.label}</dt><dd>{$form.link_text.html|crmReplace:class:huge}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Text of the link inviting constituents to create a Personal Contribution Page. This link will appear on the Contribution Thank-you page as well as on each Personal Campaign Page.{/ts}</dd>
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
        } else {
	    hide('pcpFields');
	}
    }
</script>
{/literal}
