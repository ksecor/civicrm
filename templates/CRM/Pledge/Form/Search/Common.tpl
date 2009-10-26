 <tr>
    <td>
     {$form.pledge_payment_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_payment_date_low}
    </td>
    <td>
     {$form.pledge_payment_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_payment_date_high}
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
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_create_date_low}
    </td>
    <td>
     {$form.pledge_create_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_create_date_high}
    </td> 
 </tr>
 <tr>
    <td>
     {$form.pledge_start_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_start_date_low}
    </td>
    <td>
     {$form.pledge_start_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_start_date_high}
    </td> 
 </tr>
 <tr> 
    <td>  
     {$form.pledge_end_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_end_date_low}
    </td>
    <td> 
     {$form.pledge_end_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=pledge_end_date_high}
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
{if $pledgeGroupTree}
 <tr>
    <td colspan="2">
      {include file="CRM/Custom/Form/Search.tpl" groupTree=$pledgeGroupTree showHideLinks=false}
    </td>
 </tr>
{/if}