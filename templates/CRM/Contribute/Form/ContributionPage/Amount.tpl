{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}

<div id="help">
    <p>{ts}Use this form to configure Contribution Amount options. You can enable recurring contributions (if your payment processor is &quot;PayPal
     Website Payments-Standard). You can give contributors the ability to enter their own contribution amounts
     - and/or provide a fix
ed list of amounts. For fixed amounts, you can enter a label for each 'level' of contribution (e.g. Friend, Sustainer, etc.).{/ts}</p>
    <p>{ts}If you allow people to enter their own dollar amounts, you can also set minimum and maximum values.{/ts}</p>
</div>
 
<div class="form-item">
    <fieldset><legend>{ts}Contribution Amounts{/ts}</legend>
    <table class="form-layout-compressed">  
        <tr><th scope="row" class="label">{$form.is_monetary.label}</th>
            <td>{$form.is_monetary.html}<br />
            <span class="description">{ts}Uncheck this box if you are using this contribution page for free membership signup ONLY, or to solicit in-kind / non-monetary donations such as furniture, equipment.. etc.{/ts}</span></td></tr>
        <tr><th scope="row" class="label">{$form.amount_block_is_active.label}</th>
            <td>{$form.amount_block_is_active.html}<br />
            <span class="description">{ts}Uncheck this box if you are using this contribution page for membership signup and renewal only - and you do NOT want users to select or enter any additional contribution amounts.{/ts}</span></td></tr>

    {if $form.is_recur}
        <tr><th scope="row" class="label">{$form.is_recur.label}</th>
            <td>{$form.is_recur.html}<br />
                <span class="description">{ts}Check this box if you want to give users the option to make recurring contributions. (This feature requires that you use &quot;PayPal Website Standard&quot; as your payment processor.){/ts}
    {/if}    

    <tr><th scope="row" class="label">{$form.is_allow_other_amount.label}</th>
        <td>{$form.is_allow_other_amount.html}<br />
            <span class="description">{ts}Check this box if you want to give users the option to enter their own contribution amount. Your page will then include a text field labeled <strong>Other Amount</strong>.{/ts}</td></tr>

    <tr><td>&nbsp;</td><td>

    <div id="minMaxFields">
    <tr><th scope="row" class="label">{$form.min_amount.label}</th>
        <td>{$config->defaultCurrencySymbol}&nbsp;{$form.min_amount.html}</td></tr> 
    <tr><th scope="row" class="label">{$form.max_amount.label}</th>
        <td>{$config->defaultCurrencySymbol}&nbsp;{$form.max_amount.html}<br />
            <span class="description">{ts 1=$config->defaultCurrencySymbol}If you have chosen to <strong>Allow Other Amounts</strong>, you can use the fields above to control minimum and/or maximum acceptable values (e.g. don't allow contribution amounts less than %15.00).{/ts}</span></td></tr>
    </div>

    </table>
  
    <p>{ts}Use the table below to enter up to ten fixed contribution amounts. These will be presented as a list of radio button options. Both the label and dollar amount will be displayed.{/ts}</p>
    <table id="map-field-table">
    <tr class="columnheader"><th scope="column">{ts}Contribution Label{/ts}</th><th scope="column">{ts}Amount{/ts}</th><th scope="column">{ts}Default?{/ts}</th></tr>
    {section name=loop start=1 loop=11}
       {assign var=idx value=$smarty.section.loop.index}
       <tr><td class="even-row">{$form.label.$idx.html}</td><td>{$config->defaultCurrencySymbol}&nbsp;{$form.value.$idx.html}</td><td class="even-row">{$form.default.$idx.html}</td></tr>
    {/section}
    </table>
    </td></tr></table>

    </fieldset>
</div>
<div id="crm-submit-buttons">
   <dl><dt></dt><dd> {$form.buttons.html}<br></dd></dl>
</div>

{literal}
<script type="text/javascript">
	var element_other_amount = document.getElementsByName('is_allow_other_amount');
  	if (! element_other_amount[0].checked) {
	  hide('minMaxFields');
	}
	var amount_block = document.getElementsByName('amount_block_is_active');
  	if ( ! amount_block[0].checked) {
	  hide('amountFields');
    }

	function minMax(chkbox) {
        if (chkbox.checked) {
		    show('minMaxFields', 'table-row');
		    return;
		} else {
		    hide('minMaxFields');
		    document.getElementById("min_amount").value = '';
		    document.getElementById("max_amount").value = '';
		    return;
		}
	}	
	function amountBlock(chkbox) {
        if (chkbox.checked) {
		   show('amountFields', 'table-row');
		    return;
		} else {
		    hide('amountFields', 'table-row');
    	    return;
		}
	}	
</script>
{/literal}
