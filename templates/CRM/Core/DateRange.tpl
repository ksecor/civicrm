<table class="form-layout-compressed">
   <tr>
       <td class="extra-long-hundred">{$form.relative_date_range.label}</td><td>{$form.relative_date_range.html}</td>
   </tr>
   <tr id = "absolute">
       <td>{$form.absolute_date_from.label} </td><td>{$form.absolute_date_from.html} &nbsp; 
	{include file="CRM/common/calendar/desc.tpl" trigger=trigger_absolute_date_1} 
	{include file="CRM/common/calendar/body.tpl" dateVar=absolute_date_from startDate=startYear endDate=endYear offset=5 trigger=trigger_absolute_date_1}</td>
       <td>{$form.absolute_date_to.label} </td><td>{$form.absolute_date_to.html} &nbsp; 
	{include file="CRM/common/calendar/desc.tpl" trigger=trigger_absolute_date_2} 
	{include file="CRM/common/calendar/body.tpl" dateVar=absolute_date_to startDate=startYear endDate=endYear offset=5 trigger=trigger_absolute_date_2}</td>
   </tr>
</table>

{literal}
<script type="text/javascript">
  
   function showAbsoluteRange( val ) {
      if ( val == "0" ) {
        cj('#absolute').show();
      } else {
        cj('#absolute').hide();
      }
   }
</script>
{/literal}