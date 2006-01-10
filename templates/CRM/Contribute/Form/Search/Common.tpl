        <tr> 
            <td class="label"> 
                {$form.contribution_from_date.label} 
            </td>
            <td>
                {$form.contribution_from_date.html} &nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
                {include file="CRM/common/calendar/body.tpl" dateVar=contribution_from_date startDate=startYear endDate=endYear offset=5 trigger=trigger1}
            </td>
            <td colspan="2"> 
                 {$form.contribution_to_date.label} {$form.contribution_to_date.html}<br />
                 &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger2}
                 {include file="CRM/common/calendar/body.tpl" dateVar=contribution_to_date startDate=startYear endDate=endYear offset=5 trigger=trigger2}
            </td> 
        </tr> 
        <tr> 
            <td class="label"> 
                {$form.contribution_min_amount.label} 
            </td> 
            <td>
                {$form.contribution_min_amount.html}
            </td> 
            <td colspan="2"> 
                  {$form.contribution_max_amount.label} {$form.contribution_max_amount.html} 
            </td> 
        </tr>
        <tr>
            <td class="label">{ts}Contribution Type{/ts}</td> 
            <td>{$form.contribution_type_id.html}</td> 
            <td class="label">{ts}Paid By{/ts}</td> 
            <td>{$form.payment_instrument_id.html}</td> 
        </tr>
        <tr>
            <td class="label">{ts}Status{/ts}</td> 
            <td colspan="3">{$form.contribution_status.html}</td>
        </tr>
        <tr>
            <td class="label">{ts}Thank-you date not set?{/ts}</td> 
            <td colspan="3">{$form.contribution_thankyou_date_isnull.html}</td>
        </tr>
        <tr>
            <td class="label">{ts}Receipt date not set?{/ts}</td> 
            <td colspan="3">{$form.contribution_receipt_date_isnull.html}</td>
        </tr>
