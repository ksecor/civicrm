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
        {ts}Use this form to configure Personal Campaign pages.{/ts}
    </div>
    <table class="form-layout-compressed">
        <tr><td style="width: 278px;">&nbsp;</td>
        <td class="font-size11pt">{$form.is_active.html}&nbsp;{$form.is_active.label}</td></tr>
    </table>
    <div class="spacer"></div>
    <div id="pcpFields">
       <table class="form-layout-compressed">
         <tr>
            <td class="label">{$form.is_approval_needed.label}</td>
            <td>{$form.is_approval_needed.html}
                <br /><span class="description">Administrator approval required for new Personal Campaign Pages.</span>
            </td>
         </tr>
         <tr><td class="label">{$form.supporter_profile_id.label}</td><td>{$form.supporter_profile_id.html}</td></tr>
         <tr><td class="label">{$form.is_tellfriend_enabled.label}</td><td>{$form.is_tellfriend_enabled.html}</td></tr>  
   	     <tr><td class="label">{$form.tellfriend_limit.label}</td><td>{$form.tellfriend_limit.html}</td></tr>  
	     <tr><td class="label">{$form.link_text.label}</td><td>{$form.link_text.html}</td></tr>  
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
        } else {
	    hide('pcpFields');
	}
    }
</script>
{/literal}