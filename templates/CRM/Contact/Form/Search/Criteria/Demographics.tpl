<div id="demographics" class="form-item">
    <table class="form-layout">
       <tr>
        <td>
            {$form.birth_date_low.label|replace:'-':'<br />'}&nbsp;&nbsp; 
	        {include file="CRM/common/jcalendar.tpl" elementName=birth_date_low}&nbsp;&nbsp;&nbsp;
            {$form.birth_date_high.label}&nbsp;&nbsp;
            {include file="CRM/common/jcalendar.tpl" elementName=birth_date_high}
        </td>
       </tr>
      <tr>
        <td>
           {$form.deceased_date_low.label|replace:'-':'<br />'}&nbsp;&nbsp;
           {include file="CRM/common/jcalendar.tpl" elementName=deceased_date_low}&nbsp;&nbsp;&nbsp;
           {$form.deceased_date_high.label}&nbsp;&nbsp;
           {include file="CRM/common/jcalendar.tpl" elementName=deceased_date_high}
        </td>    
      </tr>
      <tr>
         <td>
            {$form.gender.label}<br />
            {$form.gender.html} &nbsp;<a href="#" title="unselect" onclick="unselectRadio('gender', 'Advanced'); return false;" >unselect</a>
         </td>
      </tr>
    </table>            
</div>

