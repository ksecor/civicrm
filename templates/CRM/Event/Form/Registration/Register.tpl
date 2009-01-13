{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}
{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">
{if $event.intro_text}
    <div id="intro_text">
        <p>{$event.intro_text}</p>
    </div>
{/if}

{if $priceSet}
    <fieldset><legend>{$event.fee_label}</legend>
    <dl>
{if $priceSet.help_pre}
  <dt>&nbsp;</dt>
  <dd class="description">{$priceSet.help_pre}</dd>
{/if}
    {foreach from=$priceSet.fields item=element key=field_id}
        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>
            {assign var="count" value="1"}
                <table class="form-layout-compressed">
                    <tr>
                    {foreach name=outer key=key item=item from=$form.$element_name}
                        {if is_numeric($key) }
                                <td class="labels font-light">{$form.$element_name.$key.html}</td>
                            {if $count == $element.options_per_line}
                            {assign var="count" value="1"}
                            </tr>
                            <tr>
                            {else}
                                {assign var="count" value=`$count+1`}
                            {/if}
                        {/if}
	              {/foreach}
                    </tr>
                </table>
            </dd>
        {else}
            {assign var="name" value=`$element.name`}
            {assign var="element_name" value="price_"|cat:$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>&nbsp;{$form.$element_name.html}</dd>
        {/if}
        {if $element.help_post}
            <dt>&nbsp;</dt>
            <dd class="description">{$element.help_post}</dd>
        {/if}
    {/foreach}
<div id="pricelabel" style="display:none">
<dt>Total Fee(s) </dt>
<dd id="pricevalue"></dd>
</div>
{if $priceSet.help_post}
  <dt>&nbsp;</dt>
  <dd class="description">{$priceSet.help_post}</dd>
{/if}
    </dl>
    </fieldset>
    <dl>
        {if $form.is_pay_later}
            <dt>&nbsp;</dt>
            <dd>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</dd>
        {/if}
    </dl>
{else}
    {if $paidEvent}
     <table class="form-layout-compressed">
        <tr><td class="label nowrap">{$event.fee_label} <span class="marker">*</span></td>
            <td>&nbsp;</td>
            <td>{$form.amount.html}</td>
        </tr>
        {if $form.is_pay_later}
        <tr><td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</td>
        </tr>
        {/if}
    </table>
    {/if}
{/if}

{assign var=n value=email-$bltID}
<table class="form-layout-compressed">
    <tr><td class="label nowrap">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
 </table>
 {if $form.additional_participants.html}
    <div id="noOfparticipants_show">
        <a href="#" class="button" onclick="hide('noOfparticipants_show'); show('noOfparticipants'); document.getElementById('additional_participants').focus(); return false;"><span>&raquo; {ts}Register additional people for this event{/ts}</span></a>
    </div><div class="spacer"></div>
 {/if}
    <div id="noOfparticipants" style="display:none">
        <div class="form-item">
            <table class="form-layout">
            <tr><td><a href="#" onclick="hide('noOfparticipants'); show('noOfparticipants_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a></a>
                    <label>{$form.additional_participants.label}</label></td>
                <td>{$form.additional_participants.html|crmReplace:class:two}<br />
                    <span class="description">{ts}You will be able to enter registration information for each additional person after you complete this page and click Continue.{/ts}</span>
                </td>
       	    </tr>
            </table>
        </div>
    </div> 

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

{* Put PayPal Express button after customPost block since it's the submit button in this case. *}
{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}
    {assign var=expressButtonName value='_qf_Register_next_express'}
    <fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
    <table class="form-layout-compressed">
    <tr><td class="description">{ts}Click the PayPal button to continue.{/ts}</td></tr>
    <tr><td>{$form.$expressButtonName.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td></tr>
    </table>
    </fieldset>
{/if}

   <div id="crm-submit-buttons">
     {$form.buttons.html}
   </div>

    {if $event.footer_text}
        <div id="footer_text">
            <p>{$event.footer_text}</p>
        </div>
    {/if}
</div>

{* Hide Credit Card Block and Billing information if registration is pay later. *}
{if $form.is_pay_later and $hidePaymentInformation} 
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_pay_later"
    trigger_value       =""
    target_element_id   ="payment_information" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 1
}
{/if}
{literal} 
<script type="text/javascript">
var totalfee=0;
var symbol = '{/literal}{$currencySymbol}{literal}';
if(document.Register.scriptFee.value){
  totalfee = parseFloat(document.Register.scriptFee.value);
  document.getElementById('pricelabel').style.display = "block";
  document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalfee;
  document.Register.scriptFee.value = parseFloat('0');
}
var price = new Array();
if(document.Register.scriptArray.value){
  price = document.Register.scriptArray.value.split(',');

}
function addPrice(priceVal, priceId) {
  var op  = document.getElementById(priceId).type;
  var ele = document.getElementById(priceId).name.substr(6);
  if (op == 'checkbox') {
    var chek = ele.split('[');
    ele = chek[0];
  }
  if(!price[ele]) {
    price[ele] = parseFloat('0');
  }
  var addprice = 0;
  var priceset = 0;
  if(op != 'select-one') {
    priceset = priceVal.split(symbol);
  }
  
  if (priceset != 0) {
    var addprice = parseFloat(priceset[1]);
  }
  switch(op)
    {
    case 'checkbox':
      if(document.getElementById(priceId).checked) {
	totalfee   += addprice;
	price[ele] += addprice;
      }else{
	totalfee   -= addprice;
	price[ele] -= addprice;
      }
      break;    
      
    case 'radio':
      totalfee = parseFloat(totalfee) + addprice - parseFloat(price[ele]);
      price[ele] = addprice;
      break;
      
    case 'text':
      var textval = parseFloat(document.getElementById(priceId).value);
      var curval = textval * addprice;
      if(textval>=0){
	totalfee = parseFloat(totalfee) + curval - parseFloat(price[ele]);
	price[ele] = curval;
      }else {
	totalfee = parseFloat(totalfee) - parseFloat(price[ele]);	
	price[ele] = parseFloat('0');
      }

      break;
      
    case 'select-one':
      var index = parseInt(document.getElementById(priceId).selectedIndex);
      var myarray = ['','{/literal}{$selectarray}{literal}'];
      if(index>0) {
	var selectvalue = myarray[index].split(symbol);
	totalfee = parseFloat(totalfee) + parseFloat(selectvalue[1]) - parseFloat(price[ele]);
	price[ele] = parseFloat(selectvalue[1]);
      }else {
	totalfee = parseFloat(totalfee) - parseFloat(price[ele]);
	price[ele] = parseFloat('0');
      }	
      break;
      
    }//End of swtich loop
  
  if( totalfee>0 ){
    document.getElementById('pricelabel').style.display = "block";
    document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalfee;
    document.Register.scriptFee.value = totalfee;
    document.Register.scriptArray.value = price;
  } else{
    document.getElementById('pricelabel').style.display = "none";
  }
}
    function allowParticipant( ) {
	var additionalParticipant = document.getElementById('additional_participants').value; 
	var validNumber = "";
	for( i = 0; i< additionalParticipant.length; i++ ) {
	    if ( additionalParticipant.charAt(i) >=1 || additionalParticipant.charAt(i) <=9 ) {
		validNumber += additionalParticipant.charAt(i);
	    } else {
		document.getElementById('additional_participants').value = validNumber;
	    }
	}
    }
</script>
{/literal} 
