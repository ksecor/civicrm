        <tr> 
            <td class="label"> 
                {$form.contribution_date_from.label} 
            </td>
            <td>
                {$form.contribution_date_from.html} &nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
                {include file="CRM/common/calendar/body.tpl" dateVar=contribution_date_from startDate=startYear endDate=endYear offset=5 trigger=trigger1}
            </td>
            <td colspan="2"> 
                 {$form.contribution_date_to.label} {$form.contribution_date_to.html}<br />
                 &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger2}
                 {include file="CRM/common/calendar/body.tpl" dateVar=contribution_date_to startDate=startYear endDate=endYear offset=5 trigger=trigger2}
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
            <td colspan="2"><label>{ts}Paid By{/ts}</label> {$form.payment_instrument_id.html}</td> 
        </tr>
        <tr>
            <td class="label">{ts}Status{/ts}</td> 
            <td>{$form.contribution_status.html}</td>
            <td colspan="2">{$form.contribution_receipt_date_isnull.html}&nbsp;<label>{ts}Receipt not sent?{/ts}</label></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td colspan="2">{$form.contribution_thankyou_date_isnull.html}&nbsp;<label>{ts}Thank-you not sent?{/ts}</label></td>
        </tr>
        <tr>
            <td colspan="4">
            {include file="CRM/Custom/Form/Search.tpl" groupTree=$contributeGroupTree showHideLinks=false}
            </td>
        </tr>