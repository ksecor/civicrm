{* this template is used for adding/editing/deleting contribution *} 
{if $cdType }
  {include file="CRM/Custom/Form/CustomData.tpl"}
{elseif $priceSetId}
  {include file="CRM/Price/Form/PriceSet.tpl"}
{elseif $showAdditionalInfo and $formType }
  {include file="CRM/Contribute/Form/AdditionalInfo/$formType.tpl"}
{else}
{if $contributionMode == 'test' }
    {assign var=contribMode value="TEST"}
{elseif $contributionMode == 'live'}
    {assign var=contribMode value="LIVE"}
{/if}

{if !$email and $action neq 8 and $context neq 'standalone'}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{ts}You will not be able to send an automatic email receipt for this contribution because there is no email address recorded for this contact. If you want a receipt to be sent when this contribution is recorded, click Cancel and then click Edit from the Summary tab to add an email address before recording the contribution.{/ts}</p>
    </dd>
  </dl>
</div>
{/if}
{if $contributionMode}
<div id="help">
    {ts 1=$displayName 2=$contribMode}Use this form to submit a new contribution on behalf of %1. <strong>A %2 transaction will be submitted</strong> using the selected payment processor.{/ts}
</div>
<div class="crm-submit-buttons">{$form.buttons.html}</div>
<fieldset><legend>{ts}Credit Card Contribution{/ts}</legend>
{else}
<div class="crm-submit-buttons">{$form.buttons.html}</div>
<fieldset><legend>{if $action eq 1 or $action eq 1024}{ts}New Contribution{/ts}{elseif $action eq 8}{ts}Delete Contribution{/ts}{else}{ts}Edit Contribution{/ts}{/if}</legend> 
{/if}
   {if $action eq 8} 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          <dd> 
          {ts}WARNING: Deleting this contribution will result in the loss of the associated financial transactions (if any).{/ts} {ts}Do you want to continue?{/ts} 
          </dd> 
       </dl> 
      </div> 
      </fieldset>
   {else}
      {if $isOnline}{assign var=valueStyle value=" class='view-value'"}{else}{assign var=valueStyle value=""}{/if}
      <table class="form-layout-compressed">
        {if $context neq 'standalone'}
            <tr>
                <td class="font-size12pt right"><strong>{ts}Contributor{/ts}</strong></td><td class="font-size12pt"><strong>{$displayName}</strong></td>
            </tr>
        {else}
            {include file="CRM/Contact/Form/NewContact.tpl"}
        {/if}
        {if $contributionMode}
           <tr><td class="label nowrap">{$form.payment_processor_id.label}<span class="marker"> * </span></td><td>{$form.payment_processor_id.html}</td></tr>
        {/if}
        <tr><td class="label">{$form.contribution_type_id.label}</td><td{$valueStyle}>{$form.contribution_type_id.html}&nbsp;
        {if $is_test}
        {ts}(test){/ts}
        {/if} {help id="id-contribution_type"}
        </td></tr>
	
	{if $action eq 2 and $line_items}
	<tr>
            <td class="label">{ts}Contribution Amount{/ts}</td>
            <td>{include file="CRM/Event/Form/LineItems.tpl"}</td>
        </tr>
	{else}
        <tr>
            <td class="label">{$form.total_amount.label}</td>
	    <td {$valueStyle}>
	    <span id='totalAmount'>{$form.total_amount.html|crmMoney:$currency}</span> 
	    {if $hasPriceSets}
	    <span id='totalAmountORPriceSet'> {ts}OR{/ts}</span>
	    <span id='selectPriceSet'>{$form.price_set_id.html}</span>
	    <fieldset id="PriceSetFields" style="display:none;"></fieldset>
	    {/if}
	    <span class="description">{ts}Actual amount given by contributor.{/ts}</span>
            </td>
        </tr>
        {/if}

        <tr><td class="label">{$form.source.label}</td><td{$valueStyle}>{$form.source.html} {help id="id-contrib_source"}</td></tr>

        {if $contributionMode}
            {if $email and $outBound_option != 2}
                <tr><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html}</td></tr>
                <tr><td class="label">&nbsp;</td><td class="description">{ts}Automatically email a receipt for this contribution to {$email}?{/ts}</td></tr>
            {elseif $context eq 'standalone' and $outBound_option != 2 }
                <tr id="email-receipt" style="display:none;"><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html} <span class="description">{ts}Automatically email a receipt for this contribution to {/ts}<span id="email-address"></span>?</span></td></tr>
            {/if}
            <tr id="receiptDate"><td class="label">{$form.receipt_date.label}</td><td>{$form.receipt_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=receipt_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date that a receipt was sent to the contributor.{/ts}</span></td></tr>
        {/if}
        {if !$contributionMode}
            <tr><td class="label">{$form.receive_date.label}</td>
                <td{$valueStyle}>{if $hideCalender neq true}{$form.receive_date.html}{else}{$receive_date|truncate:10:''|crmDate}{/if}
            {if $hideCalender neq true}
                 {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_1}
                 {include file="CRM/common/calendar/body.tpl" dateVar=receive_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_1}
            {/if}
            </td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}The date this contribution was received.{/ts}</td></tr>
            <tr><td class="label">{$form.payment_instrument_id.label}</td><td{$valueStyle}>{$form.payment_instrument_id.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Leave blank for non-monetary contributions.{/ts}</td></tr>
            {if $showCheckNumber || !$isOnline}  
                <tr id="checkNumber"><td class="label">{$form.check_number.label}</td><td>{$form.check_number.html|crmReplace:class:six}</td></tr>
            {/if}
            <tr><td class="label">{$form.trxn_id.label}</td><td{$valueStyle}>{$form.trxn_id.html|crmReplace:class:twelve} {help id="id-trans_id"}</td></tr>
            {if $email and $outBound_option != 2}
                <tr><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html} <span class="description">{ts}Automatically email a receipt for this contribution to {$email}?{/ts}</span></td></tr>
            {elseif $context eq 'standalone' and $outBound_option != 2 }
                <tr id="email-receipt" style="display:none;"><td class="label">{$form.is_email_receipt.label}</td><td>{$form.is_email_receipt.html} <span class="description">{ts}Automatically email a receipt for this contribution to {/ts}<span id="email-address"></span>?</span></td></tr>
            {/if}
            <tr id="receiptDate"><td class="label">{$form.receipt_date.label}</td><td>{$form.receipt_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=receipt_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_2}<br />
            <span class="description">{ts}Date that a receipt was sent to the contributor.{/ts}</span></td></tr>
            <tr><td class="label">{$form.contribution_status_id.label}</td><td>{$form.contribution_status_id.html}
            {if $contribution_status_id eq 2}{if $is_pay_later }: {ts}Pay Later{/ts} {else}: {ts}Incomplete Transaction{/ts}{/if}{/if}</td></tr>

            {* Cancellation fields are hidden unless contribution status is set to Cancelled *}
            <tr id="cancelInfo"> 
                <td>&nbsp;</td> 
                <td><fieldset><legend>{ts}Cancellation Information{/ts}</legend>
                <table class="form-layout-compressed">
                  <tr id="cancelDate"><td class="label">{$form.cancel_date.label}</td><td>{$form.cancel_date.html}
                   {if $hideCalendar neq true}
                     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_4}
                     {include file="CRM/common/calendar/body.tpl" dateVar=cancel_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_4}
                   {/if}
                   </td></tr>
                  <tr id="cancelDescription"><td class="label">&nbsp;</td><td class="description">{ts}Enter the cancellation date, or you can skip this field and the cancellation date will be automatically set to TODAY.{/ts}</td></tr>
                  <tr id="cancelReason"><td class="label" style="vertical-align: top;">{$form.cancel_reason.label}</td><td>{$form.cancel_reason.html|crmReplace:class:huge}</td></tr>
               </table>
               </fieldset>
               </td>
            </tr>
        {/if}

        <tr><td class="label">{$form.soft_credit_to.label}</td>
            <td>{$form.soft_credit_to.html} {help id="id-soft_credit"}</td>
        </tr>

      </table>

    <div id="customData"></div>

    {*include custom data js file*}
    {include file="CRM/common/customData.tpl"}
    </fieldset>

{literal}
<script type="text/javascript">
    cj( function( ) {
        {/literal}
        buildCustomData( '{$customDataType}' );
        {if $customDataSubType}
        buildCustomData( '{$customDataType}', {$customDataSubType} );
        {/if}
        {literal}
    });

    var showPane = "";
    cj(function() {
        cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ");

        cj('.accordion .head').hover( function() { cj(this).addClass( "ui-state-hover");
        }, function() { cj(this).removeClass( "ui-state-hover");
    }).bind('click', function() { 
        var checkClass = cj(this).find('span').attr( 'class' );
        var len        = checkClass.length;
        if ( checkClass.substring( len - 1, len ) == 's' ) {
            cj(this).find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-e');
        } else {
            cj(this).find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-s');
        }
        cj(this).next().toggle(); return false; }).next().hide();
        if ( showPane.length > 1 ) {
            eval("showPane =[ '" + showPane.substring( 0,showPane.length - 2 ) +"]");
            cj.each( showPane, function( index, value ) {
                cj('span#'+value).removeClass().addClass('ui-icon ui-icon-triangle-1-s');
                loadPanes( value )  ;
                cj("div."+value).show();
            });
        }
    
        cj('.head').one( 'click', function() { loadPanes( cj(this).children().attr('id') );  });    
    });


    function loadPanes( id ) {
        var url = "{/literal}{crmURL p='civicrm/contact/view/contribution' q='snippet=4&formType=' h=0}{literal}" + id;
        if ( ! cj('div.'+id).html() ) {
            var loading = '<img src="{/literal}{$config->resourceBase}i/loading.gif{literal}" alt="{/literal}{ts}loading{/ts}{literal}" />&nbsp;{/literal}{ts}Loading{/ts}{literal}...';
            cj('div.'+id).html(loading);
        }
        cj.ajax({
            url    : url,
            success: function(data) { 
                cj('div.'+id).html(data);
            }
        });
    }
    var url = "{/literal}{$dataUrl}{literal}";

    cj('#soft_credit_to').autocomplete( url, { width : 180, selectFirst : false, matchContains: true
        }).result( function(event, data, formatted) { cj( "#soft_contact_id" ).val( data[1] );
    });
    {/literal}
    {if $context eq 'standalone' and $outBound_option != 2 }
    {literal}
    cj( function( ) {
        cj("#contact").blur( function( ) {
            checkEmail( );
        });
        checkEmail( );
    });
    function checkEmail( ) {
        var contactID = cj("input[name=contact_select_id]").val();
        if ( contactID ) {
            var postUrl = "{/literal}{crmURL p='civicrm/ajax/checkemail' h=0}{literal}";
            cj.post( postUrl, {contact_id: contactID},
                function ( response ) {
                    if ( response ) {
                        cj("#email-receipt").show( );
                        cj("#email-address").html( response );
                    } else {
                        cj("#email-receipt").hide( );
                    }
                }
            );
        }
    }
    {/literal}
    {/if}
</script>

<div class="accordion ui-accordion ui-widget ui-helper-reset">
    {* Additional Detail / Honoree Information / Premium Information  Fieldset *}
    {foreach from=$allPanes key=paneName item=paneValue}
        <h3 class="head"><span class="ui-icon ui-icon-triangle-1-e" id="{$paneValue.id}"></span><a href="#">{$paneName}</a></h3>
        <div class="{$paneValue.id}"></div>
        {if $paneValue.open eq 'true'}
            {literal}<script type="text/javascript"> showPane += "{/literal}{$paneValue.id}{literal}"+"','";</script>{/literal}
        {/if}
    {/foreach}
</div>

{/if}
<br />
<div class="crm-submit-buttons">{$form.buttons.html}</div>
    {literal}
    <script type="text/javascript">
     function verify( ) {
       var element = document.getElementsByName("is_email_receipt");
        if ( element[0].checked ) {
         var ok = confirm( '{/literal}{ts}Click OK to save this contribution record AND send a receipt to the contributor now{/ts}{literal}.' );    
          if (!ok ) {
            return false;
          }
        }
     }
     function status() {
       document.getElementById("cancel_date[M]").value = "";
       document.getElementById("cancel_date[d]").value = "";
       document.getElementById("cancel_date[Y]").value = "";
       document.getElementById("cancel_reason").value = "";
     }

    </script>
    {/literal}


{if $action neq 8}  
{if $email and $outBound_option != 2}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_email_receipt"
    trigger_value       =""
    target_element_id   ="receiptDate" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}
{/if}
{if !$contributionMode} 
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="contribution_status_id"
    trigger_value       = '3'
    target_element_id   ="cancelInfo" 
    target_element_type ="table-row"
    field_type          ="select"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="payment_instrument_id"
    trigger_value       = '4'
    target_element_id   ="checkNumber" 
    target_element_type ="table-row"
    field_type          ="select"
    invert              = 0
}
{/if} 
{/if} {* not delete mode if*}      

    {* include jscript to warn if unsaved form field changes *}
    {include file="CRM/common/formNavigate.tpl"}

{/if} {* closing of main custom data if *} 


{literal}
<script type="text/javascript" >

 // load form during form rule.
 {/literal}
 {if $buildPriceSet}{literal}buildAmount( );{/literal}
 {/if}
 {literal}


function buildAmount( priceSetId ) {

  if ( !priceSetId ) priceSetId = cj("#price_set_id").val( );

  var fname = '#PriceSetFields';
  if ( !priceSetId ) {
      // hide price set fields.
      cj( fname ).hide( ); 

      // show/hide price set amount and total amount.
      cj( "#totalAmountORPriceSet" ).show( );
      cj( "#totalAmount").show( );

      return;
  }

  var dataUrl = {/literal}"{crmURL h=0 q='snippet=4'}"{literal} + '&priceSetId=' + priceSetId;

  var response = cj.ajax({
		         url: dataUrl,
			 async: false
			}).responseText;
  cj( fname ).show( ).html( response );

  // freeze total amount text field.
  cj( "#total_amount").val( '' );

  cj( "#totalAmountORPriceSet" ).hide( );
  cj( "#totalAmount").hide( );
  
}
</script>
{/literal}
