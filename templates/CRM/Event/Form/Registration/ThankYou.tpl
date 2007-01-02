<div class="form-item">
    
 <table class="form-layout-compressed">
        {ts}Billing Information{/ts}
        <tr><td>{ts}<strong>First Name</strong>{/ts}</td><td>{$confirm.first_name}</td></tr>
        <tr><td>{ts}<strong>Middle Name</strong>{/ts}</td><td>{$confirm.middle_name}</td></tr>
        <tr><td>{ts}<strong>Last Name</strong>{/ts}</td><td>{$confirm.last_name} </td></tr>
        <tr><td>{ts}<strong>City</strong>{/ts}</td><td>{$confirm.city}</td></tr>
        <tr><td>{ts}<strong>State Province</strong>{/ts}</td><td>{$confirm.state_province_id}</td></tr>
        <tr><td>{ts}<strong>Postal Code</strong>{/ts}</td><td>{$confirm.postal_code}</td></tr>
        <tr><td>{ts}<strong>Country</strong>{/ts}</td><td>{$confirm.country_id}</td></tr>
    
 </table>
<table class="form-layout-compressed">
    <tr><td>{ts}<strong>Custom Fields</strong>{/ts}</td><td>{$confirm.custom_pre_id}</td></tr>
    <tr><td>{ts}<strong>Custom Fields</strong>{/ts}</td><td>{$confirm.custom_post_id}</td></tr>
</table>
   <div id="crm-submit-buttons">
     {$form.buttons.html}
   </div>
</div>
