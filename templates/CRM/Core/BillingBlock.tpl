{if $form.credit_card_number}
    <div id="payment_information">
        <fieldset><legend>{ts}Credit Card Information{/ts}</legend> 
            {if $paymentProcessor.billing_mode & 2 and !$hidePayPalExpress }
                <table class="form-layout-compressed">
                	<tr>
                		<td class="description">{ts}If you have a PayPal account, you can click the PayPal button to continue. Otherwise, fill in the credit card and billing information on this form and click <strong>Continue</strong> at the bottom of the page.{/ts}</td>
                	</tr>
                	<tr>
                		<td>{$form.$expressButtonName.html} <span style="font-size: 11px; font-family: Arial, Verdana;">Save time. Checkout securely. Pay without sharing your financial information. </span></td>
                	</tr>
                </table>
            {/if} 

            {if $paymentProcessor.billing_mode & 1}
                <table class="form-layout-compressed">
                	<tr>
                		<td class="label">{$form.credit_card_type.label}</td>
                		<td colspan="2">{$form.credit_card_type.html}</td>
                	</tr>
                	<tr>
                		<td class="label">{$form.credit_card_number.label}</td>
                		<td colspan="2">{$form.credit_card_number.html}<br />
                		<span class="description">{ts}Enter numbers only, no spaces or dashes.{/ts}</span></td>
                	</tr>
                	<tr>
                		<td class="label">{$form.cvv2.label}</td>
                		<td style="vertical-align: top;">{$form.cvv2.html}</td>
                		<td><img src="{$config->resourceBase}i/mini_cvv2.gif" alt="{ts}Security Code Location on Credit Card{/ts}" style="vertical-align: text-bottom;" /><br />
                		<span class="description">{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}</span></td>
                	</tr>
                	<tr>
                		<td class="label">{$form.credit_card_exp_date.label}</td>
                		<td colspan="2">{$form.credit_card_exp_date.html}</td>
                	</tr>
                </table>
                </fieldset>

                <fieldset><legend>{ts}Billing Name and Address{/ts}</legend>
                    <table class="form-layout-compressed">
                        <tr>
                            <td colspan="2"><span class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</span></td>
                        </tr>
                        <tr>
                            <td class="label">{$form.billing_first_name.label}</td>
                            <td>{$form.billing_first_name.html}</td>
                        </tr>
                        <tr>
                            <td class="label">{$form.billing_middle_name.label}</td>
                            <td>{$form.billing_middle_name.html}</td>
                        </tr>
                        <tr>
                            <td class="label">{$form.billing_last_name.label}</td>
                            <td>{$form.billing_last_name.html}</td>
                        </tr>
                        {assign var=n value=street_address-$bltID}
                        <tr>
                            <td class="label">{$form.$n.label}</td>
                            <td>{$form.$n.html}</td>
                        </tr>
                        {assign var=n value=city-$bltID}
                        <tr>
                            <td class="label">{$form.$n.label}</td>
                            <td>{$form.$n.html}</td>
                        </tr>
                        {assign var=n value=country_id-$bltID}
                        <tr>
                            <td class="label">{$form.$n.label}</td>
                            <td>{$form.$n.html}</td>
                        </tr>
                        {assign var=n value=state_province_id-$bltID}
                        <tr>
                            <td class="label">{$form.$n.label}</td>
                            <td>{$form.$n.html}</td>
                        </tr>
                        {assign var=n value=postal_code-$bltID}
                        <tr>
                            <td class="label">{$form.$n.label}</td>
                            <td>{$form.$n.html}</td>
                        </tr>
                    </table>
                </fieldset>
            {else}
                </fieldset>
            {/if}
    </div>
{/if}