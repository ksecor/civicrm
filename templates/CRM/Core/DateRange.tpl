<table class="form-layout">
   <tr>
       {assign var=relativeName   value=$fieldName|cat:"_relative"}
       <td colspan=2>{$form.$relativeName.html}</td>
   </tr>
   <tr id="absolute">
       {assign var=fromName   value=$fieldName|cat:"_from"}
       <td>{$form.$fromName.label}&nbsp;{$form.$fromName.html} &nbsp; 
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_$fromName} 
           {include file="CRM/common/calendar/body.tpl" dateVar=$fromName startDate=startYear endDate=endYear offset=5 trigger=trigger_$fromName}
       </td>
       {assign var=toName   value=$fieldName|cat:"_to"}
       <td>{$form.$toName.label}&nbsp;{$form.$toName.html} &nbsp; 
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_$toName} 
           {include file="CRM/common/calendar/body.tpl" dateVar=$toName startDate=startYear endDate=endYear offset=5 trigger=trigger_$toName}
       </td>
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