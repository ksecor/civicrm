<div id="demographics" class="form-item">
    <table class="form-layout">
       <tr>
         <td class="label">
             {$form.gender.label}
        </td>
        <td colspan=3>
            {$form.gender.html}
        </td>
      </tr>
      <tr>
        <td class="label"><br />
            {$form.birth_date_low.label}
        </td>
        <td><br />
		    {$form.birth_date_low.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=birth_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_1}
        </td>
        <td><br />
            {$form.birth_date_high.label}
            {$form.birth_date_high.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=birth_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_2}
        </td>
      </tr>
      <tr>
        <td class="label"><br />
            {$form.deceased_date_low.label}
        </td>
        <td><br />
		    {$form.deceased_date_low.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_3}
            {include file="CRM/common/calendar/body.tpl" dateVar=deceased_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_3}
        </td>
        <td><br />
            {$form.deceased_date_high.label}
       		{$form.deceased_date_high.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_date_4}
            {include file="CRM/common/calendar/body.tpl" dateVar=deceased_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_date_4}
        </td>    
      </tr>
    </table>            
    </div>
</div>

