
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/WizardHeader.tpl}
<div id="help">
    {if $action eq 0}
        <p>{ts}need to add discription ....{/ts}</p>
    {else}
        {ts}Use this form to add premium for this contribution Page .{/ts}
    {/if}
</div>
 
<div class="form-item">
    <fieldset><legend>{ts}Configure Premiums{/ts}</legend>
    <dl>
     <dt>&nbsp;</dt><dd>{$form.premiums_active.html} {$form.premiums_active.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Premium feature enabled for this page{/ts}<br />
        <strong>{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}</strong></dd>	
  

    <dt>{$form.premiums_intro_title.label}</dt><dd>{$form.premiums_intro_title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Title for Premiums section.{/ts}</dd>

    
    <dt>{$form.premiums_intro_text.label}</dt><dd>{$form.premiums_intro_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Enter content for the introductory message. This will be displayed below the page title. You may include HTML formatting tags. You can also include images, as long as they are already uploaded to a server - reference them using complete URLs.{/ts}</dd>
    
    <dt>{$form.premiums_contact_email.label}</dt><dd>{$form.premiums_contact_email.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}This email address is included in receipts if it is populated and a premium has been selected.{/ts}</dd>
	
      <dt>{$form.premiums_contact_phone.label}</dt><dd>{$form.premiums_contact_phone.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}This phone number is included in receipts if it is populated and a premium has been selected.{/ts}</dd>

     <dt>{$form.premiums_display_min_contribution.label}</dt><dd>{$form.premiums_display_min_contribution.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Should we automatically display minimum contribution amount text after the premium descriptions.{/ts}</dd>
	
	
    </dl>
    </fieldset>
</div>

{if $action ne 4}
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
{else}
    <div id="crm-done-button">
        {$form.done.html}
    </div>
{/if} {* $action ne view *}
