<table class="form-layout-compressed">
   <tr>
       <td class="extra-long-hundred">{$form.relative_date_range.label}</td><td>{$form.relative_date_range.html}</td>
   </tr>
   <tr id = "absolute">
       <td>{$form.absolute_date_from.label}</td><td>{$form.absolute_date_from.html}</td>
       <td>{$form.absolute_date_to.label}</td><td>{$form.absolute_date_to.html}</td>
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