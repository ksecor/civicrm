{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}

{include file="CRM/common/TrackingFields.tpl"}

{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">

{* moved to tpl since need to show only for primary participant page *}
{if $requireApprovalMsg || $waitlistMsg}
  <div id = "id-waitlist-approval-msg" class="messages status">
    <dl>
	{if $requireApprovalMsg}<dd id="id-req-approval-msg">{$requireApprovalMsg}</dd>{/if}
        {if $waitlistMsg}<dd id="id-waitlist-msg">{$waitlistMsg}</dd>{/if} 
    </dl>
  </div>
{/if}

{if $event.intro_text}
    <div id="intro_text">
        <p>{$event.intro_text}</p>
    </div>
{/if}

{if $form.additional_participants.html}
    <div class="additional_participants-section" id="noOfparticipants">
        <table class="form-layout-compressed">
            <tr>
                <td class="label" style="width: 15%;">{$form.additional_participants.label}</td>
        		<td>&nbsp;</td>
                <td>
                    {$form.additional_participants.html} &nbsp; ({ts}including yourself{/ts})<br />
                    <span class="description">{ts}Fill in your registration information on this page. If you are registering additional people, you will be able to enter their registration information after you complete this page and click &quot;Continue&quot;.{/ts}</span>
                </td>
           	</tr>
        </table>
    </div><div class="spacer"></div> 
{/if}

{if $priceSet}
    <fieldset id="priceset"><legend>{$event.fee_label}</legend>
        {include file="CRM/Price/Form/PriceSet.tpl"}
    </fieldset>
    {if $form.is_pay_later}
    <dl id="is-pay-later">
	<dt>&nbsp;</dt>
        <dd>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</dd>
    </dl>
    {/if}

{else}
    {if $paidEvent}
    <div class="paidEvent-section">
	<table class="form-layout-compressed">
	    <tr>
		<td class="label nowrap">{$event.fee_label} <span class="marker">*</span></td>
		<td>&nbsp;</td>
		<td>{$form.amount.html}</td>
	    </tr>
	    {if $form.is_pay_later}
	        <tr id="is-pay-later">
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
		        <td>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</td>
	        </tr>
	    {/if}
 	</table>
 	</div><div class="spacer"></div>
    {/if}
{/if}

{assign var=n value=email-$bltID}
<table class="form-layout-compressed">
    <tr>
	    <td class="label nowrap">{$form.$n.label}</td><td>{$form.$n.html}</td>
    </tr>
</table>


{* User account registration option. Displays if enabled for one of the profiles on this page. *}
{include file="CRM/common/CMSUser.tpl"}

{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 

{if $paidEvent}   
    {include file='CRM/Core/BillingBlock.tpl'} 
{/if}        

{include file="CRM/UF/Form/Block.tpl" fields=$customPost}   

{if $isCaptcha}
    {include file='CRM/common/ReCAPTCHA.tpl'}
{/if}

<div id="paypalExpress">
{* Put PayPal Express button after customPost block since it's the submit button in this case. *}
{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express' and $buildExpressPayBlock}
    {assign var=expressButtonName value='_qf_Register_upload_express'}
    <fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
    <table class="form-layout-compressed">
	<tr>
	    <td class="description">{ts}Click the PayPal button to continue.{/ts}</td>
	</tr>
	<tr>
	    <td>{$form.$expressButtonName.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td>
	</tr>
    </table>
    </fieldset>
{/if}
</div>

<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>

{if $event.footer_text}
    <div id="footer_text">
        <p>{$event.footer_text}</p>
    </div>
{/if}
</div>

{literal} 
<script type="text/javascript">

    function allowParticipant( ) { 		
	{/literal}{if $allowGroupOnWaitlist}{literal}
	    var additionalParticipants = cj('#additional_participants').val();	
	    allowGroupOnWaitlist( additionalParticipants );
	{/literal}{/if}{literal}
    }

    {/literal}{if ($form.is_pay_later or $bypassPayment) and $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}
    {literal} 
       showHidePayPalExpressOption( );
    {/literal}{/if}{literal}

    function showHidePayPalExpressOption( )
    {
	var payLaterElement = {/literal}{if $form.is_pay_later}true{else}false{/if}{literal};
	if ( ( cj("#bypass_payment").val( ) == 1 ) ||
	     ( payLaterElement && document.getElementsByName('is_pay_later')[0].checked ) ) {
		show("crm-submit-buttons");
		hide("paypalExpress");
	} else {
		show("paypalExpress");
		hide("crm-submit-buttons");
	}
    }

    {/literal}{if ($form.is_pay_later or $bypassPayment) and $showHidePaymentInformation}{literal} 
       showHidePaymentInfo( );
    {/literal} {/if}{literal}

    function showHidePaymentInfo( )
    {	
	var payLater = {/literal}{if $form.is_pay_later}true{else}false{/if}{literal};

	if ( ( cj("#bypass_payment").val( ) == 1 ) ||
	     ( payLater && document.getElementsByName('is_pay_later')[0].checked ) ) {
	     hide( 'payment_information' );		
	} else {
             show( 'payment_information' );
	}
    }
    
    {/literal}{if $allowGroupOnWaitlist}{literal}
       allowGroupOnWaitlist( 0 );
    {/literal}{/if}{literal}
    
    function allowGroupOnWaitlist( additionalParticipants )
    {	
      if ( !additionalParticipants ) {
      	additionalParticipants = cj('#additional_participants').val();
      }
      if ( additionalParticipants == '' ) {
           additionalParticipants = 0;
      }
      var availableRegistrations = {/literal}'{$availableRegistrations}'{literal};
      var totalParticipants = parseInt( additionalParticipants ) + 1;

      var isrequireApproval = {/literal}'{$requireApprovalMsg}'{literal};
 
      if ( totalParticipants > availableRegistrations ) {
         cj( "#id-waitlist-msg" ).show( );
         cj( "#id-waitlist-approval-msg" ).show( );

         //set the value for hidden bypass payment. 
         cj( "#bypass_payment").val( 1 );

         //hide pay later.
         {/literal}{if $form.is_pay_later}{literal} 
	    cj("#is-pay-later").hide( );
         {/literal} {/if}{literal}
 
      }	else {
         if ( isrequireApproval ) {
            cj( "#id-waitlist-approval-msg" ).show( );
            cj( "#id-waitlist-msg" ).hide( );
         } else {
            cj( "#id-waitlist-approval-msg" ).hide( );
         }
         //reset value since user don't want or not eligible for waitlist 
         cj( "#bypass_payment").val( 0 );

         //need to show paylater if exists.
         {/literal}{if $form.is_pay_later}{literal} 
	    cj("#is-pay-later").show( );
         {/literal} {/if}{literal}
      }

      //now call showhide payment info.
      {/literal}
      {if ($form.is_pay_later or $bypassPayment) and $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}{literal} 
         showHidePayPalExpressOption( );
      {/literal}{/if}
      {literal}
  
      {/literal}{if ($form.is_pay_later or $bypassPayment) and $showHidePaymentInformation}{literal} 
         showHidePaymentInfo( );
      {/literal}{/if}{literal}
    }
</script>
{/literal} 
