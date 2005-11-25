<fieldset>
    <div class="form-item">                    
    {strip} 
        <table class="form-layout"> 
        <tr>                                          
            <td class="font-size12pt">{$form.sort_name.label}</td> 
            <td>{$form.sort_name.html}    
                <div class="description font-italic"> 
                    {ts}Complete OR partial contact name OR email.{/ts} 
                </div>
            </td> 
            <td class="label">{$form.buttons.html}</td>        
        </tr>
        <tr> 
            <td><label>{ts}Contribution Type{/ts}</label><br /> 
                {$form.contribution_type_id.html} 
            </td> 
            <td><label>{ts}Payment Instrument{/ts}</label><br /> 
                {$form.payment_instrument_id.html} 
            </td> 
            <td><label>{ts}Contribution Status{/ts}</label><br /> 
                {$form.contribution_status.html} 
            </td>
        </tr>
        <tr> 
            <td class="label"> 
                {$form.contribution_from_date.label} 
            </td> 
            <td> 
                 {$form.contribution_from_date.html} &nbsp; {$form.contribution_to_date.label} {$form.contribution_to_date.html} 
            </td> 
        </tr> 
        <tr> 
            <td class="label"> 
                {$form.contribution_min_amount.label} 
            </td> 
            <td> 
                 {$form.contribution_min_amount.html} &nbsp; {$form.contribution_max_amount.label} {$form.contribution_max_amount.html} 
            </td> 
        </tr> 
        </table>
    {/strip}
    </div>
</fieldset>