{include file="CRM/common/WizardHeader.tpl"}
<div id="help">
    {if $context EQ 'Contribute'}
        {assign var=enduser value="contributor"}
        {assign var=pageType value="Online Contribution page"}
        {ts}Tell a Friend gives your contributors an easy way to spread the word about this fundraising campaign. The contribution thank-you page will include a link to a form where they can enter their friends' email addresses, along with a personalized message. CiviCRM will record these solicitation activities, and will add the friends to your database.{/ts}
    {elseif $context EQ 'Event'}
        {assign var=enduser value="participant"}
        {assign var=pageType value="Event Information page"}
        {ts}Tell a Friend gives registering participants an easy way to spread the word about this event. The registration thank-you page will include a link to a form where they can enter their friends' email addresses, along with a personalized message. CiviCRM will record these solicitation activities, and will add the friends to your database.{/ts}
    {elseif $context EQ 'Pledge'}
        {assign var=enduser value="pledge"}
        {assign var=pageType value="Pledge Information page"}
        {ts}Tell a Friend gives registering pledge signers an easy way to spread the word about this pledge. The registration thank-you page will include a link to a form where they can enter their friends' email addresses, along with a personalized message. CiviCRM will record these solicitation activities, and will add the friends to your database.{/ts}	
    {/if}
</div>

<div id="form" class="form-item">
    <fieldset><legend>{ts}Tell a Friend{/ts}</legend>
    
    <dl>
    	<dt></dt><dd>{$form.is_active.html}&nbsp;{$form.is_active.label}</dd>
    </dl>
    <div id="friendFields">
    <table class="form-layout">
        <tr><td class="label">{$form.title.label}</td><td>{$form.title.html}</td></tr>   
        <tr><td class="label">{$form.intro.label}</td><td>{$form.intro.html}<br />
        <span class="description">{ts 1=$enduser}This message is displayed to the %1 at the top of the Tell a Friend form. You may include HTML tags to add formatting or links.{/ts}</span></td></tr>     
        <tr><td class="label">{$form.suggested_message.label}</td><td>{$form.suggested_message.html}<br />
        <span class="description">{ts 1=$enduser}Provides the %1 with suggested text for their personalized message to their friends.{/ts}</span></td></tr> 
        <tr><td class="label">{$form.general_link.label}</td><td>{$form.general_link.html}<br />
        <span class="description">{ts 1=$pageType}A link to this %1 is automatically included in the email sent to friends. If you ALSO want to include a link providing general information about your organization, enter that link here (e.g <em>http://www.example.org/</em>){/ts}</span></td></tr>
	<tr><td class="label">{$form.thankyou_title.label}</td><td>{$form.thankyou_title.html}</td></tr>            
	<tr><td class="label">{$form.thankyou_text.label}</td><td>{$form.thankyou_text.html}<br />
        <span class="description">{ts 1=$enduser}Your message thanking the %1 for helping to spread the word. You may include HTML tags to add formatting or links.{/ts}</span></td></tr>
    </table>	
    </div>

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
           hide('friendFields');
	}
       function friendBlock(chkbox) {
           if (chkbox.checked) {
	      show('friendFields');
	      return;
           } else {
	      hide('friendFields');
    	      return;
	   }
       }
</script>
{/literal}
