            <tr><td class="label">{$form.grant_status_id.label}</td><td>{$form.grant_status_id.html}</td>

                <td class="label">{$form.grant_type_id.label}</td><td>{$form.grant_type_id.html}</td> </tr>
            <tr><td class="label">{$form.grant_amount_total.label}</td><td>{$form.grant_amount_total.html}</td> 
                <td class="label">{$form.grant_report_received.label}</td><td>{$form.grant_report_received.html}</td></tr>
                
           <tr><td class="label"> {$form.grant_application_received_date_low.label} </td>
                <td> {$form.grant_application_received_date_low.html}&nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_1}
                {include file="CRM/common/calendar/body.tpl" dateVar=grant_application_received_date_low  offset=3  doTime=1 trigger=trigger_search_grant_1}
                </td>
              <td colspan="2"> {$form.grant_application_received_date_high.label} {$form.grant_application_received_date_high.html}<br /> &nbsp; &nbsp; 
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_2}
                {include file="CRM/common/calendar/body.tpl" dateVar=grant_application_received_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_grant_2}
                </td>          
            </tr>
            
         <tr><td class="label"> {$form.grant_money_transfer_date_low.label} </td>
                <td> {$form.grant_money_transfer_date_low.html}&nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_3}
                {include file="CRM/common/calendar/body.tpl" dateVar=grant_money_transfer_date_low offset=5  doTime=1 trigger=trigger_search_grant_3}
                </td>
                <td colspan="2"> {$form.grant_money_transfer_date_high.label} {$form.grant_money_transfer_date_high.html}<br /> &nbsp; &nbsp; 
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_4}
                {include file="CRM/common/calendar/body.tpl" dateVar=grant_money_transfer_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_grant_4}
                </td>          
            </tr>
            
