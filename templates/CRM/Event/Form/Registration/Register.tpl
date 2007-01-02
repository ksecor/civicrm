<div class="form-item">
 {ts}<strong>{$eventPage.intro_text}{/ts}<br/>	
{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 
 <table class="form-layout-compressed">
    <tr><td class="label nowrap">{$form.amount.label}</td><td>{$form.amount.html}</td></tr>
        
        <table class="form-layout-compressed">
        {ts}Billing Name and Address{/ts}
        <tr><td colspan="2" class="description">{ts}Enter the name as shown on your credit or debit card, and the billing address for this card.{/ts}</td></tr>
        <tr><td class="label">{$form.first_name.label} {$reqMark}</td><td>{$form.first_name.html}</td></tr>
        <tr><td class="label">{$form.middle_name.label}</td><td>{$form.middle_name.html}</td></tr>
        <tr><td class="label">{$form.last_name.label} {$reqMark}</td><td>{$form.last_name.html}</td></tr>
        <tr><td class="label">{$form.street_address.label} {$reqMark}</td><td>{$form.street_address.html}</td></tr>
        <tr><td class="label">{$form.city.label} {$reqMark}</td><td>{$form.city.html}</td></tr>
        <tr><td class="label">{$form.state_province_id.label} {$reqMark}</td><td>{$form.state_province_id.html}</td></tr>
        <tr><td class="label">{$form.postal_code.label} {$reqMark}</td><td>{$form.postal_code.html}</td></tr>
        <tr><td class="label">{$form.country_id.label} {$reqMark}</td><td>{$form.country_id.html}</td></tr>
        </table> 
        {include file="CRM/UF/Form/Block.tpl" fields=$customPost}    
    

 </table>
	 {ts}<strong>{$eventPage.footer_text}{/ts}<br/>	  
   <div id="crm-submit-buttons">
     {$form.buttons.html}
   </div>
</div>

