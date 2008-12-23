{include file="CRM/common/WizardHeader.tpl"}
<div id="form" class="form-item">
	<fieldset><legend>{ts}Configure Personal Campaign Pages{/ts}</legend> 
	{if !$profile} 
	{capture assign=pUrl}{crmURL p='civicrm/admin/uf/group' q="reset=1"}{/capture}
	<div class="status message">
		{ts 1=$pUrl}No Profile with user account registration option has been configured / enabled for your site.You need to <a href='%1'>configure Supporter profile</a> first. It will be used for gathering information about users, who would like to create Personal Contribution Pages.{/ts}
	</div>
	{/if}
	<div id="help">
		{ts}Use this form to configure Personal Campaign Pages. {/ts}
	</div>
       <dl>
         <dt></dt><dd>{$form.is_active.html} &nbsp;{$form.is_active.label}</dd>
         <dt>&nbsp;</dt><dd class="description">{ts}Enable Personal Campaign Pages in this Contribution Page?{/ts}</dd>
       </dl>
	<div class="spacer"></div>

	<div id="pcpFields">
		<dl>
			<dt>{$form.is_approval_needed.label}</dt><dd>{$form.is_approval_needed.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Is administrator approval required for new Personal Campaign Pages?{/ts}</dd>

			<dt>{$form.supporter_profile_id.label} <span class="marker"> *</span></dt><dd>{$form.supporter_profile_id.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Text{/ts}</dd>

			<dt>{$form.is_tellfriend_enabled.label}</dt><dd>{$form.is_tellfriend_enabled.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Text{/ts}</dd>

			<dt>{$form.tellfriend_limit.label}</dt><dd>{$form.tellfriend_limit.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Text{/ts}</dd>

			<dt>{$form.link_text.label}</dt><dd>{$form.link_text.html}</dd>
			<dt>&nbsp;</dt><dd class="description">{ts}Text{/ts}</dd>
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
