<fieldset class="collapsible">
 <table class="form-layout">  
            <tr>
                <td class="label">
                        {$form.case_subject.label}
                </td>
                <td>
                        {$form.case_subject.html}
                </td>
                <td colspan="2">
                        {$form.case_status_id.label} &nbsp;&nbsp;&nbsp;     {$form.case_status_id.html}
                </td>                    
            </tr>
           
            <tr>
                <td class="label">
                        {$form.case_type_id.label}
                </td>
                <td>
                        {$form.case_type_id.html}
                </td>
            </tr>            
            <tr>
                <td class="label"> 
                        {$form.case_start_date_low.label} 
                </td>
                <td> 
                        {$form.case_start_date_low.html}&nbsp;<br />
                        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_1}
                        {include file="CRM/common/calendar/body.tpl" dateVar=case_start_date_low  offset=3  doTime=1 trigger=trigger_search_case_1}
                </td>
                <td colspan="2"> 
                        {$form.case_start_date_high.label} {$form.case_start_date_high.html}<br /> &nbsp; &nbsp; 
                        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_2}
                        {include file="CRM/common/calendar/body.tpl" dateVar=case_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_case_2}
                </td>          
            </tr>
            
           
 </table>
</fieldset>