
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
<div id="help">
    {ts}Use this form to configure the Membership section for this Online Contribution Page. You can hide the section completely by un-checking the Enabled field. You can set separate section titles and introductory messages for new memberships and for renewals.{/ts}
</div>
  <div id="form" class="form-item">
    <fieldset><legend>{ts}Configure Membership Section{/ts}</legend>
    <dl>
     <dt></dt><dd>{$form.is_active.html} &nbsp;{$form.is_active.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts 1=$title}Is a Membership Section included in this Online Contributions page?(%1){/ts}</dd>	
    <dt>{$form.new_title.label}</dt><dd>{$form.new_title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Title to display at top of membership section for new member signup{/ts}</dd>

    <dt>{$form.new_text.label}</dt><dd>{$form.new_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Text to display below title of membership section for new member signup.{/ts}</dd>
   
    <dt>{$form.renewal_title.label}</dt><dd>{$form.renewal_title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Title for member renewal.{/ts}</dd>

    <dt>{$form.renewal_text.label}</dt><dd>{$form.renewal_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Text for member renewal.{/ts}</dd>
    {if $form.membership_type}
    <dt>{$form.membership_type.label}</dt> 
    <dd>
        {assign var="count" value="1"}
           {strip}
             <table border="1">
            <tr> <td>Check Membership Types to Include on this Page</td><td>Default</td></tr>
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
     {/if}
    <dt></dt><dd>{$form.is_required.html}&nbsp;{$form.is_required.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If checked, user must select one of the displayed membership types.{/ts}</dd>

    <dt></dt><dd>{$form.display_min_fee.html}&nbsp;{$form.display_min_fee.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}{/ts}</dd>

    <dt></dt><dd>{$form.is_separate_payment.html}&nbsp;{$form.is_separate_payment.label} </dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Should the membership payment transaction be processed separately from any additional contribution on this page.{/ts}</dd>
	
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

