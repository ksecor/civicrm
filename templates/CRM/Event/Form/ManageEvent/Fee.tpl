{* this template is used for adding event  *}
{include file="CRM/common/WizardHeader.tpl"}
{capture assign="adminPriceSets"}{crmURL p='civicrm/admin/price' q="reset=1"}{/capture}

<div class="form-item">
<fieldset><legend>{ts}Event Fees{/ts}</legend>
    {if !$paymentProcessor}
        {capture assign=ppUrl}{crmURL p='civicrm/admin/paymentProcessor' q="reset=1"}{/capture}
        <div class="status message">
                {ts 1=$ppUrl 2=$docURLTitle 3="http://wiki.civicrm.org/confluence//x/ihk"}No Payment Processor has been configured / enabled for your site. If this is a Paid Event, you will need to <a href="%1">configure a Payment Processor</a> first. Then return to this screen and assign the processor to this Event. (<a href="%3" target="_blank" title="%2">read more...</a>){/ts}
        </div>
    {/if}
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.is_monetary.label}</dt><dd>{$form.is_monetary.html}</dd>
    </dl>

    <div id="event-fees">
        {if $paymentProcessor}
        <div id="paymentProcessor">
            <dl>
              <dt>{$form.payment_processor_id.label}</dt><dd>{$form.payment_processor_id.html}</dd>
              <dt>&nbsp;</dt><dd class="description">{ts 1="http://wiki.civicrm.org/confluence//x/ihk" 2=$docURLTitle}Select the payment processor to be used to process Event Registration fees for this event. (<a href="%1" target="_blank" title="%2">read more...</a>){/ts}</dd>
            </dl>
        </div>
        {/if}
           
        <div id="contributionType">
            <dl>
            <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}This contribution type will be assigned to payments made by participants when they register online.{/ts}
            <dt>{$form.fee_label.label}<span class="marker"> *</span>
	    </dt><dd>{$form.fee_label.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}This label is displayed with the list of event fees.{/ts}
            </dl>
        </div>

        <div id="payLater">
          <dl>
             <dt>&nbsp;</dt><dd>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</dd>
             <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want to give users the option to mail in their payment.{/ts}</dd>
          </dl>
        </div>

        <div id="payLaterOptions">
          <dl>
             <dt>{$form.pay_later_text.label}</dt><dd>{$form.pay_later_text.html}</dd>
             <dt>{$form.pay_later_receipt.label}</dt><dd>{$form.pay_later_receipt.html}</dd>
          </dl>
        </div>

        <div id="priceSet">
            <dl>
            <dt>{$form.price_set_id.label}</dt><dd>{$form.price_set_id.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts 1=$adminPriceSets}Select a pre-configured Price Set to offer multiple individually priced options for event registrants. Otherwise, select &quot;-none-&quot, and enter one or more fee levels in the table below. Create or edit Price Sets <a href="%1">here</a>.{/ts}</dd>
            </dl>
        </div>
        
        <fieldset id="map-field"><legend>{ts}Fee Levels{/ts}</legend>
        <p>{ts}Use the table below to enter descriptive labels and amounts for up to ten event fee levels. These will be presented as a list of radio button options. Both the label and dollar amount will be displayed.{/ts}</p>
        <table id="map-field-table">
        <tr class="columnheader"><th scope="column">{ts}Fee Label{/ts}</th><th scope="column">{ts}Amount{/ts}</th><th scope="column">{ts}Default?{/ts}</th></tr>
        {section name=loop start=1 loop=11}
           {assign var=idx value=$smarty.section.loop.index}
           <tr><td class="even-row">{$form.label.$idx.html}</td><td>{$config->defaultCurrencySymbol()}&nbsp;{$form.value.$idx.html}</td><td class="even-row">{$form.default.$idx.html}</td></tr>
        {/section}
        </table>
        </fieldset>
    </div>
    
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}

{literal} 
<script type="text/javascript">
// Re-show Fee Level grid if Price Set select has been set to none.
if ( document.getElementById('price_set_id').options[document.getElementById('price_set_id').selectedIndex].value == '' ) {
    show( 'map-field' );
}

if ( document.getElementsByName('is_monetary')[0].checked ) {
    show( 'event-fees', 'block' );
}
</script>
{/literal} 

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_pay_later"
    trigger_value       =""
    target_element_id   ="payLaterOptions" 
    target_element_type ="block"
    field_type          ="radio"
    invert              = 0
}
