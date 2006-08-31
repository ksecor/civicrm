{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}

<div id="help">
    <p>{ts}Use this form to configure Contribution Amount options. You can give contributors the ability to enter their own contribution amounts - and/or provide a fixed list of amounts. For fixed amounts, you can enter a label for each 'level' of contribution (e.g. Friend, Sustainer, etc.).{/ts}</p>
    <p>{ts}If you allow people to enter their own dollar amounts, you can also set minimum and maximum values.{/ts}</p>
</div>
 
<div class="form-item" id="map-field">
    <fieldset><legend>{ts}Contribution Amounts{/ts}</legend>
    <dl>

    <dt>{$form.amount_block_is_active.label}</dt><dd>{$form.amount_block_is_active.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Uncheck this box if you are using this contribution page for membership signup and renewal only - and you do NOT want users to select or enter any additional contribution amounts.{/ts}</dd>
    </dl>
    
    <dl>
    <dt>{$form.is_allow_other_amount.label}</dt><dd>{$form.is_allow_other_amount.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want to give users the option to enter their own contribution amount. Your page will then include a text field labeled <strong>Other Amount</strong>.{/ts}</dd>
    </dl>
    <div id="minMaxFields">
    <dl>
    <dt>{$form.min_amount.label}</dt><dd>{$form.min_amount.html}</dd> 
    <dt>{$form.max_amount.label}</dt><dd>{$form.max_amount.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If you have chosen to <strong>Allow Other Amounts</strong>, you can use the fields above to control minimum and/or maximum acceptable values (e.g. don't allow contribution amounts less than $5.00).{/ts}</dd>
    </dl>
    </div>
    <p>{ts}Use the table below to enter up to ten fixed contribution amounts. These will be presented as a list of radio button options. Both the label and dollar amount will be displayed.{/ts}</p>
    <table id="map-field-table">
    <tr class="columnheader"><th>{ts}Contribution Label{/ts}</th><th>{ts}Amount{/ts}</th><th>{ts}Default?{/ts}</th></tr>
    {section name=loop start=1 loop=11}
       {assign var=idx value=$smarty.section.loop.index}
       <tr><td class="even-row">{$form.label.$idx.html}</td><td>{$form.value.$idx.html}</td><td class="even-row">{$form.default.$idx.html}</td></tr>
    {/section}
    </table>
    </fieldset>
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
<script type="text/javascript">
	{literal}
    for( i=0; i < document.Amount.elements.length; i++) {
        if (document.Amount.elements[i].name == "is_allow_other_amount" && document.Amount.elements[i].checked == false ) {
            hide('minMaxFields');
        }
    }
    
	function minMax(chkbox) {
        if (chkbox.checked) {
		    show('minMaxFields');
		    return;
		} else {
		    hide('minMaxFields');
		    document.getElementById("min_amount").value = '';
		    document.getElementById("max_amount").value = '';
		    return;
		}
	}	
	{/literal}
</script>
