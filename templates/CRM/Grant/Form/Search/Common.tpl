            <tr>
		<td>
                    {$form.grant_report_received.label}<br />
	            {$form.grant_report_received.html}
              	</td>
		<td>
                    {$form.grant_status_id.label}<br />
                    {$form.grant_status_id.html}
                </td>
                <td>
                    {$form.grant_type_id.label}<br />
		    {$form.grant_type_id.html} 
                </td>
            </tr>
            <tr>
		<td>
                    {$form.grant_amount_low.label}<br />
                    {$form.grant_amount_low.html}
                </td> 
                <td colspan="2">
                    {$form.grant_amount_high.label}<br />
		    {$form.grant_amount_high.html}
                </td>
            </tr>
            <tr>
                <td>
                    {$form.grant_application_received_date_low.label}<br />
	            {$form.grant_application_received_date_low.html}&nbsp;
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_1}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_application_received_date_low  offset=3  trigger=trigger_search_grant_1}
               	</td>
		<td colspan="2">
		    {$form.grant_application_received_date_high.label}<br />
		    {$form.grant_application_received_date_high.html}&nbsp;&nbsp;
		    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_2}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_application_received_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_grant_2}
		   &nbsp;&nbsp; {$form.grant_application_received_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
	      </td>          
           </tr>
            <tr>
                <td>
                    {$form.grant_decision_date_low.label}<br />
               	    {$form.grant_decision_date_low.html}&nbsp;
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_3}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_decision_date_low  offset=3 trigger=trigger_search_grant_3}
                </td>
		<td colspan="2">
                    {$form.grant_decision_date_high.label}<br /> 
		    {$form.grant_decision_date_high.html}&nbsp;&nbsp; 
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_4}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_decision_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_grant_4}
	            &nbsp;&nbsp;{$form.grant_decision_date_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
               </td>          
           </tr>
           <tr>
              <td> 
                    {$form.grant_money_transfer_date_low.label}<br /> 
                    {$form.grant_money_transfer_date_low.html}&nbsp;
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_5}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_money_transfer_date_low offset=5 trigger=trigger_search_grant_5}
             </td>
	     <td colspan="2">                 
		    {$form.grant_money_transfer_date_high.label}<br /> 
		    {$form.grant_money_transfer_date_high.html}&nbsp; &nbsp;
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_6}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_money_transfer_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_grant_6}
	           &nbsp;&nbsp;{$form.grant_money_transfer_date_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
              </td>          
          </tr>
          <tr>
              <td>
                    {$form.grant_due_date_low.label}<br />
                    {$form.grant_due_date_low.html}&nbsp;
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_7}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_due_date_low  offset=3  trigger=trigger_search_grant_7}

              </td>
	      <td colspan="2">
                    {$form.grant_due_date_high.label}<br />
		    {$form.grant_due_date_high.html}&nbsp;&nbsp; 
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_grant_8}
                    {include file="CRM/common/calendar/body.tpl" dateVar=grant_due_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_grant_8}
		   &nbsp;&nbsp;{$form.grant_due_date_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
               </td>          
	  </tr>
	  {if $grantGroupTree}
	  <tr>
	       <td colspan="3">
		    {include file="CRM/Custom/Form/Search.tpl" groupTree=$grantGroupTree showHideLinks=false}</td>
	  </tr>
	  {/if}
