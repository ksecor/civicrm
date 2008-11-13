<div id="demographics" class="form-item">
    <table class="form-layout">
       <tr>
        <td>
            {$form.birth_date_low.label|replace:'-':'<br />'}&nbsp;&nbsp; 
	    {$form.birth_date_low.html}&nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=birth_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_1}&nbsp;&nbsp;&nbsp;
	   
            {$form.birth_date_high.label}&nbsp;&nbsp;
            {$form.birth_date_high.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=birth_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_2}
        </td>
       </tr>
      <tr>
        <td>
           {$form.deceased_date_low.label|replace:'-':'<br />'}&nbsp;&nbsp;
           {$form.deceased_date_low.html} &nbsp;
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_3}
           {include file="CRM/common/calendar/body.tpl" dateVar=deceased_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_3}&nbsp;&nbsp;&nbsp;

           {$form.deceased_date_high.label}&nbsp;&nbsp;
           {$form.deceased_date_high.html}&nbsp;
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_4}
           {include file="CRM/common/calendar/body.tpl" dateVar=deceased_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_4}
        </td>    
      </tr>
      <tr>
         <td>
            {$form.gender.label}<br />
            {$form.gender.html}
         </td>
      </tr>
    </table>            
    </div>
</div>

