 <tr>
    <td>
     {$form.pledge_payment_date_low.label} 
     <br />
     {$form.pledge_payment_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_5}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_payment_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_5}
    </td>
    <td>
     {$form.pledge_payment_date_high.label}
    <br />
     {$form.pledge_payment_date_high.html}&nbsp
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_6}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_payment_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_6}
    </td> 
 </tr>
 <tr>
    <td colspan="2">
     <label>{ts}Pledge Payment Status{/ts} 
     <br />{$form.pledge_payment_status_id.html}
    </td>
 </tr>
 <tr>
    <td> 
     <label>{ts}Pledge Amounts{/ts} 
     <br />
     {$form.pledge_amount_low.label} {$form.pledge_amount_low.html} &nbsp;&nbsp; {$form.pledge_amount_high.label} {$form.pledge_amount_high.html}
    </td>
    <td>
     <label>{ts}Pledge Status{/ts} 
     <br />{$form.pledge_status_id.html}
    </td>
 </tr>
 <tr>
    <td>
     {$form.pledge_create_date_low.label} 
     <br />
     {$form.pledge_create_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_7}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_create_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_7}
    </td>
    <td>
     {$form.pledge_create_date_high.label}
    <br />
     {$form.pledge_create_date_high.html}&nbsp
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_8}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_create_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_8}
    </td> 
 </tr>
 <tr>
    <td>
     {$form.pledge_start_date_low.label} 
     <br />
     {$form.pledge_start_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_1}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_start_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_1}
    </td>
    <td>
     {$form.pledge_start_date_high.label}
    <br />
     {$form.pledge_start_date_high.html}&nbsp
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_2}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_2}
    </td> 
 </tr>
 <tr> 
    <td>  
     {$form.pledge_end_date_low.label} 
     <br />
     {$form.pledge_end_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_3}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_end_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_3}
    </td>
    <td> 
     {$form.pledge_end_date_high.label}
    <br />
     {$form.pledge_end_date_high.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_pledge_4}
     {include file="CRM/common/calendar/body.tpl" dateVar=pledge_end_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_pledge_4}
    </td> 
 </tr>
 <tr>
    <td>
     <label>{ts}Contribution Type{/ts}</label> 
     <br />{$form.pledge_contribution_type_id.html}
    </td>
    <td>
      <label>{ts}Contribution Page{/ts}</label> 
      <br />{$form.pledge_contribution_page_id.html}
    </td> 
 </tr>
 <tr> 
    <td>
     {$form.pledge_in_honor_of.label} 
     <br />{$form.pledge_in_honor_of.html}
    </td>
    <td>
     {$form.pledge_test.html}&nbsp;{$form.pledge_test.label}
    </td>
 </tr>
 <tr>
    <td colspan="2">
      {include file="CRM/Custom/Form/Search.tpl" groupTree=$pledgeGroupTree showHideLinks=false}
    </td>
 </tr>
