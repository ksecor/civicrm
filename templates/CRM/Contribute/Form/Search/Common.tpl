        <tr> 
            <td class="label"> 
                {$form.contribution_date_low.label} 
            </td>
            <td>
                {$form.contribution_date_low.html} &nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_contribution_1}
                {include file="CRM/common/calendar/body.tpl" dateVar=contribution_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_contribution_1}
            </td>
            <td colspan="2"> 
                 {$form.contribution_date_high.label} {$form.contribution_date_high.html}<br />
                 &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_contribution_2}
                 {include file="CRM/common/calendar/body.tpl" dateVar=contribution_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_contribution_2}
            </td> 
        </tr> 
        <tr> 
            <td class="label"> 
                {$form.contribution_amount_low.label} 
            </td> 
            <td>
                {$form.contribution_amount_low.html}
            </td> 
            <td colspan="2"> 
                  {$form.contribution_amount_high.label} {$form.contribution_amount_high.html} 
            </td> 
        </tr>
        <tr>
            <td class="label">{ts}Contribution Type{/ts}</td> 
            <td>{$form.contribution_type_id.html}</td> 
            <td colspan="2"><label>{ts}Paid By{/ts}</label> {$form.contribution_payment_instrument_id.html}</td> 
        </tr>
        <tr>
            <td class="label">{ts}Contribution Page{/ts}</td> 
            <td>{$form.contribution_page_id.html}</td> 
            <td colspan="2">{$form.contribution_receipt_date_isnull.html}&nbsp;<label>{ts}Receipt not sent?{/ts}</label></td>
        </tr>
        <tr>
            <td class="label">{ts}Status{/ts}</td> 
            <td>{$form.contribution_status.html}</td>
            <td colspan="2">{$form.contribution_thankyou_date_isnull.html}&nbsp;<label>{ts}Thank-you not sent?{/ts}</label></td>
        </tr>
        <tr>
            <td colspan="4">
            {include file="CRM/Custom/Form/Search.tpl" groupTree=$contributeGroupTree showHideLinks=false}
            </td>
        </tr>
