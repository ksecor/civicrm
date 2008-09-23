{include file="CRM/common/WizardHeader.tpl"}
<div id="form" class="form-item">
    <fieldset><legend>{ts}Configure Personal Campaign Page{/ts}</legend>
    {if !$profile}
        {capture assign=pUrl}{crmURL p='civicrm/admin/uf/group' q="reset=1"}{/capture}
        <div class="status message">
                {ts 1=$pUrl}No Supporter Profile has been configured / enabled for your site.You need to  <a href='%1'>configure Supporter profile</a> first.{/ts}
        </div>
    {/if}
    <div id="help">
        {ts}HELP MESSAGE{/ts}
    </div>
    <table class="form-layout-compressed">
    	<tr><td style="width: 12em;">&nbsp;</td><td class="font-size11pt">{$form.pcp_enabled.html}&nbsp;{$form.pcp_enabled.label}</dd>
    </table>
    <div class="spacer"></div>
    
    <div id="pcpFields">
        <table class="form-layout-compressed">
         <tr><td class="label">{$form.pcp_inactive.label}</td><td>{$form.pcp_inactive.html}</td></tr>
         <tr><td class="label">{$form.supporter_profile.label}</td><td>{$form.supporter_profile.html}</td></tr>
   	 <tr><td class="label">{$form.pcp_tellfriend_enabled.label}</td><td>{$form.pcp_tellfriend_enabled.html}</td></tr>  
	 <tr><td class="label">{$form.pcp_tellfriend_limit.label}</td><td>{$form.pcp_tellfriend_limit.html}</td></tr>  
	 <tr><td class="label">{$form.pcp_link_text.label}</td><td>{$form.pcp_link_text.html}</td></tr>  
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
	var is_act = document.getElementsByName('pcp_enabled');
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