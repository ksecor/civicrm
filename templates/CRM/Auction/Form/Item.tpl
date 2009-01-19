<div class="form-item"> 
<fieldset><legend>{ts}Item Information{/ts}</legend>
    <table class="form-layout-compressed">
         <tr><td class="label">{$form.title.label}</td><td>{$form.title.html}</td></tr>
         <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}</td></tr>

         <tr><td class="label">{$form.quantity.label}</td><td>{$form.quantity.html|crmReplace:class:four}<br />
         <tr><td class="label">{$form.retail_value.label}</td><td>{$form.retail_value.html|crmReplace:class:four}<br />
         <tr><td class="label">{$form.min_bid_value.label}</td><td>{$form.min_bid_value.html|crmReplace:class:four}<br />
         <tr><td class="label">{$form.min_bid_increment.label}</td><td>{$form.min_bid_increment.html|crmReplace:class:four}<br />
         <tr><td class="label">{$form.buy_now_value.label}</td><td>{$form.buy_now_value.html|crmReplace:class:four}<br />

         <tr><td>&nbsp;</td><td>{$form.is_group.html} {$form.is_group.label}<br />
         <tr><td>&nbsp;</td><td>{$form.is_active.html} {$form.is_active.label}</td></tr> 
        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>

{include file="CRM/Form/attachment.tpl" context="pcpCampaign"}

    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset>     
</div>
