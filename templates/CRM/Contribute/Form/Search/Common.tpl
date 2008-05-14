        <tr> 
            <td> 
                {$form.contribution_date_low.label} 
            <br />
                {$form.contribution_date_low.html} &nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_contribution_1}
                {include file="CRM/common/calendar/body.tpl" dateVar=contribution_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_contribution_1}
            </td>
            <td colspan="2"> 
                 {$form.contribution_date_high.label}<br /> {$form.contribution_date_high.html}<br />
                 {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_contribution_2}
                 {include file="CRM/common/calendar/body.tpl" dateVar=contribution_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_contribution_2}
            </td> 
        </tr> 
        <tr> 
            <td> 
                {$form.contribution_amount_low.label} 
            <br />
                {$form.contribution_amount_low.html}
            </td> 
            <td colspan="2"> 
                  {$form.contribution_amount_high.label} <br /> {$form.contribution_amount_high.html} 
            </td> 
        </tr>
        <tr>
            <td><label>{ts}Contribution Type{/ts}</label> 
            <br />{$form.contribution_type_id.html}</td> 
            <td colspan="2"><label>{ts}Paid By{/ts}</label> <br /> {$form.contribution_payment_instrument_id.html}</td> 
        </tr>
        <tr>
            <td><label>{ts}Contribution Page{/ts}</label> 
            <br />{$form.contribution_page_id.html}</td> 
            <td colspan="2">
                  {$form.contribution_receipt_date_isnull.html}&nbsp;{$form.contribution_receipt_date_isnull.label}<br />
                  {$form.contribution_thankyou_date_isnull.html}&nbsp;{$form.contribution_thankyou_date_isnull.label}
                            
           </td>
        </tr>
        <tr>
            <td><label>{ts}Status{/ts}</label> 
                  {$form.contribution_status_id.html}</td>
            <td colspan="2"><br />
                  {$form.contribution_test.html}&nbsp;{$form.contribution_test.label}<br />
                  {$form.contribution_pay_later.html}&nbsp;{$form.contribution_pay_later.label}
            </td>
        </tr>
        <tr>
            <td>{$form.contribution_in_honor_of.label} 
            <br />{$form.contribution_in_honor_of.html}</td>
            <td colspan="2">
                  {$form.contribution_recurring.html}&nbsp;{$form.contribution_recurring.label}
            </td>
        </tr>
        <tr>
            <td> {$form.contribution_source.label}
            <br />{$form.contribution_source.html}</td>
        </tr>
        <tr>        
            <td>{$form.contribution_transaction_id.label} 
            <br />{$form.contribution_transaction_id.html}</td> 
        </tr>
        <tr>
            <td colspan="4">
            {include file="CRM/Custom/Form/Search.tpl" groupTree=$contributeGroupTree showHideLinks=false}
            </td>
        </tr>
