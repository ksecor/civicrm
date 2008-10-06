<div id="case-search" class="form-item">
<tr>
       	<td>
	        {$form.case_subject.label}<br />
	        {$form.case_subject.html}
        </td>
        <td>
            {$form.case_status_id.label}<br /> 
	        {$form.case_status_id.html}	
        </td>                    
    </tr>     
    <tr>
        <td>
            {$form.case_type_id.label}<br />
            {$form.case_type_id.html}
        </td>
        <td> 
            {$form.case_start_date_low.label|replace:'-':'<br />'} 
            {$form.case_start_date_low.html}&nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=case_start_date_low  offset=3  doTime=1 trigger=trigger_search_case_1}
             	 &nbsp;&nbsp;&nbsp;&nbsp;
            {$form.case_start_date_high.label}
  	        {$form.case_start_date_high.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=case_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_case_2}
       </td>          
    </tr>
 {if $caseGroupTree}
    <tr>
        <td colspan="2">
            {include file="CRM/Custom/Form/Search.tpl" groupTree=$caseGroupTree showHideLinks=false}
        </td>
    </tr>
 {/if}
</div>