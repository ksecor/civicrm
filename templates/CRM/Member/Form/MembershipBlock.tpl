{* Configure Membership signup/renewal block for an Online Contribution page *}
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<div id="help">
    {ts 1="http://wiki.civicrm.org/confluence//x/ZSk" 2=$docURLTitle}Use this form to enable and configure a Membership Signup and Renewal section for this Online Contribution Page. If you're not using this page for membership signup, leave the <strong>Enabled</strong> box un-checked (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}
</div>

<div id="form" class="form-item">
 <fieldset><legend>{ts}Configure Membership Section{/ts}</legend>
  {if $form.membership_type.html}    
    <dl>
     <dt></dt><dd>{$form.is_active.html} &nbsp;{$form.is_active.label}</dd>
     <dt>&nbsp;</dt><dd class="description">{ts}Include a Membership Signup section in this Online Contribution page?{/ts}</dd>
    </dl>
    <div id="memberFields">
    <dl>
    <dt>{$form.new_title.label}</dt><dd>{$form.new_title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Membership section title - for new member signups.{/ts}</dd>

    <dt>{$form.new_text.label}</dt><dd>{$form.new_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Membership section introductory text - for new member signups.{/ts}</dd>
   
    <dt>{$form.renewal_title.label}</dt><dd>{$form.renewal_title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Membership section title - displayed to renewing members.{/ts}</dd>

    <dt>{$form.renewal_text.label}</dt><dd>{$form.renewal_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Membership section introductory text - displayed to renewing members.{/ts}</dd>
    <dt>{$form.membership_type.label}</dt> 
    <dd>
        {assign var="count" value="1"}
           {strip}
            <table class="report">
            <tr class="columnheader" style="vertical-align:top;"><th style="border-right: 1px solid #4E82CF;">{ts}Include these membership types{/ts}:</th><th>{ts}Default{/ts}:<br />
            (&nbsp;<a href="#" title="unselect" onclick="unselectRadio('membership_type_default', 'MembershipBlock'); return false;" >unselect</a>&nbsp;)</th></tr>
            {assign var="index" value="1"}
               {foreach name=outer key=key item=item from=$form.membership_type}
                  {if $index < 10}
                    {assign var="index" value=`$index+1`}
                  {else}
                  <tr>  
                   <td class="labels font-light">{$form.membership_type.$key.html}</td>
                   <td class="labels font-light">{$form.membership_type_default.$key.html}</td>
                   </tr>
                  {/if}
               {/foreach}
           </table>
           {/strip}
      </dd>  
    <dt></dt><dd>{$form.is_required.html}&nbsp;{$form.is_required.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If checked, user must signup for one of the displayed membership options before continuing.{/ts}</dd>

    <dt></dt><dd>{$form.is_separate_payment.html}&nbsp;{$form.is_separate_payment.label} {help id="id-separate-pay"}</dd>
    <dt>&nbsp;</dt><dd class="description">
        {ts}Check this box if you are including both Membership Signup/Renewal AND a Contribution Amount section, AND you want the membership fee to be charged separately from any additional contribution amount.{/ts}</dd>

    <dt></dt><dd>{$form.display_min_fee.html}&nbsp;{$form.display_min_fee.label} {help id="id-display-fee"}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Display the membership fee along with the membership name and description for each membership option?{/ts}</dd>
    </dl>

   </div>
  {else}
      <div class="status message">
         {ts}You need to have at least one <a href="{crmURL p="civicrm/admin/member/membershipType" q="reset=1"}">Membership Type</a> to enable Member Signup.{/ts}
      </div>
  {/if} 
  {if $action ne 4}
      <div id="crm-submit-buttons">
          <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>  
      </div>
  {else}
      <div id="crm-done-button">
           <dl><dt></dt><dd>{$form.buttons.html}<br></dd></dl>
      </div>
  {/if} {* $action ne view *}
 </fieldset>
</div>

{literal}
<script type="text/javascript">
	var is_act = document.getElementsByName('is_active');
  	if ( ! is_act[0].checked) {
           hide('memberFields');
	}
       function memberBlock(chkbox) {
           if (chkbox.checked) {
	      show('memberFields');
	      return;
           } else {
	      hide('memberFields');
    	      return;
	   }
       }
</script>
{/literal}
