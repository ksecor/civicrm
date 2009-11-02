{* Template for "Change Case Type" activities *}
    <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>        
    <tr><td class="label">{ts}Current Start Date{/ts}</td><td>{$current_start_date|crmDate}</td></tr>
    <tr><td class="label">{$form.start_date.label}</td><td>{include file="CRM/common/jcalendar.tpl" elementName=start_date}</td></tr>	  
