{* Step 1 of New Pledge Wizard, and Edit Pledge Settings form.  *}

{include file="CRM/common/WizardHeader.tpl"}
{capture assign=mapURL}{crmURL p='civicrm/admin/setting/mapping' q="reset=1"}{/capture}

{assign var=pledgeID value=$id}
<div class="form-item"> 
<fieldset><legend>{ts}Pledge Settings{/ts}</legend>
         <fieldset><legend>{ts}Pledge text{/ts}</legend>
	     <table class="form-layout-compressed">	
         <tr><td class="label">{$form.creator_name.label}</td><td>{$form.creator_name.html}<br />
             <span class="description">{ts}Creator for this particular pledge.{/ts}</td></tr>
         <tr><td class="label">{$form.creator_pledge_desc.label}</td><td>{$form.creator_pledge_desc.html}<br />
             <span class="description">{ts}Pledge that the creator makes.{/ts}</td></tr>
         <tr><td class="label">{$form.signers_limit.label}</td><td>{$form.signers_limit.html}<br />
            <span class="description">
                {ts}To make the pledge successful, the minimum no. of people that need to sign up.{/ts}
            </span></td></tr>
         <tr><td class="label">{$form.signer_description_text.label}</td><td>{$form.signer_description_text.html}</td></tr>
         <tr><td class="label">{$form.signer_pledge_desc.label}</td><td>{$form.signer_pledge_desc.html}</td></tr>
	 </table>
         </fieldset>
	 <table class="form-layout-compressed">	
	 <tr><td class="label">{$form.deadline.label}</td><td>{$form.deadline.html}</td></tr>
         <tr><td>&nbsp;</td><td>{include file="CRM/common/calendar/desc.tpl" trigger=trigger_pledge doTime=1}
         {include file="CRM/common/calendar/body.tpl" dateVar=deadline offset=3 doTime=1 trigger=trigger_pledge ampm=1}</td></tr>
 	 <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}</td></tr>
	 <tr><td class="label">{$form.creator_description.label}</td><td>{$form.creator_description.html}</td></tr>
	 <tr><td>&nbsp;</td><td>{$form.is_active.html} {$form.is_active.label}</td></tr> 
	 <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>
    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset>     
</div>

