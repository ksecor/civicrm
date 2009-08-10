{* this template is used for adding/editing Honoree Information *}
<div id="id-honoree" class="section-shown">
   <fieldset>
      <table class="form-layout-compressed">
	 <tr>
	    <td colspan="3">
	       {$form.honor_type_id.html}
	       &nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('honor_type_id', '{$form.formName}'); enableHonorType(); return false;">{ts}unselect{/ts}</a>&nbsp;)<br />
	       <span class="description">{ts}Please include the name, and / or email address of the person you are honoring,{/ts}</span>
	    </td>
	 </tr>
	 <tr id="honorType">
	    <td>{$form.honor_prefix_id.html}<br />
	       <span class="description">{$form.honor_prefix_id.label}</span></td>
	    <td>{$form.honor_first_name.html}<br />
	       <span class="description">{$form.honor_first_name.label}</span></td>
	    <td>{$form.honor_last_name.html}<br />
	       <span class="description">{$form.honor_last_name.label}</span></td>
	 </tr>
	 <tr id="honorTypeEmail">
	    <td></td>
	    <td colspan="2">{$form.honor_email.html}<br />
                <span class="description">{$form.honor_email.label}</td>
      </table>
   </fieldset>
</div>
{literal}
<script type="text/javascript">
   enableHonorType();
   function enableHonorType( ) {
      var element = document.getElementsByName("honor_type_id");      
      if ( element[0].checked == true ||
	   element[1].checked == true) {
	 show('honorType', 'table-row');
	 show('honorTypeEmail', 'table-row');
      } else {
	 cj('#honor_first_name').val('');
	 cj('#honor_last_name').val('');
	 cj('#honor_email').val('');
	 cj('#honor_prefix_id').val('');
	 hide('honorType', 'table-row');
	 hide('honorTypeEmail', 'table-row');
      }
   }
</script>
{/literal}