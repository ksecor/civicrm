

{* smarty *}
<html>
   <head>




	{literal}
	<script type="text/javascript">
	 // var i;
	  var sections = new Array('demographics','expand_phone0_2','phone0_2','expand_phone0_3','phone0_3',
				 'expand_email0_2','email0_2','expand_email0_3','email0_3',
				 'expand_IM0_2','IM0_2','expand_IM0_3','IM0_3','expand_demographics');

	var showit = new Array("core");

	/*function donotuseinit (fn,fld){alert("l");
	 for (var i = 0; i < showit.length; i++) 
	 {document.getElementById(showit[i]).style.display = 'block';}
	 for (var i = 0; i < 13; i++)
	{document.getElementById(sections[i]).style.display = 'none';}
	//document.forms[fn].elements[fld].focus();
	}*/

	function show(sectionx) {
	for (var i = 0; i < sections.length; i++) {
	if (sections[i] == sectionx) {
	document.getElementById(sections[i]).style.display = 'block';}}}

	function hide(sectionx) {
	for (var i = 0; i < sections.length; i++) {
	if (sections[i] == sectionx) {
	document.getElementById(sections[i]).style.display = 'none';}}}

	</script>
	{/literal}


   </head>
   <body>
	<form {$form.attributes}id = 'llk' >  
	{if $form.hidden}
	{$form.hidden}{/if}
	{if count($form.errors) gt 0}
	<table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#ff9900"><tr><td>
	<table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFCC"><tr><td align="center">
	<span class="error" style="font-size: 13px;">Please correct the errors below.</span>
	</td></tr></table>
	</td></tr></table>
	</p>
	{/if}
	</br>

	<div id="core">
	<label>Name and Greeting</label>
	<table cellpadding="2" cellspacing="2">
	<tr>
	<td class="form-item">{*{$form.buttons.html}*}</td>
	</tr>
	<tr>
	<td class="form-item"><label>First / Last:</label></td>
	<td>{$form.prefix.html}{$form.first_name.html}{$form.last_name.html}{$form.suffix.html}</td>
	</tr>
	<tr>
	<td class="form-item"><label>Greeting:</label></td>
	<td class="form-item">{$form.greeting_type.html}</td>
	</tr>
	<tr>
	<td class="form-item"><label>Job Title:</label></td>
	<td class="form-item">{$form.job_title.html}</td>
	</tr>

	<tr>
	<td><label>Communication Preferences</label></td>
		<td><table border="0" cellpadding="2" cellspacing="2" width="90%">
		<tr>
		<td class="form-item"><label>Privacy:</label></td>
		<td class="form-item">{$form.do_not_phone.html} Do not call
	                               {$form.do_not_email.html} Do not contact by email
                                       {$form.do_not_mail.html} Do not contact by postal mail</td>
		</tr>
		<tr>
		<td class="form-item"><label>Prefers:</label></td>
		<td class="form-item">{$form.preferred_communication_method.html}
			<div class="description">Preferred method of communicating with this individual</div></td>
		</tr>
		</table></td>
	</tr>

	<td><a id = "expand_demographics" 
	onclick="show('demographics'); hide('expand_demographics');
	return false;" href="#demographics">[+] show demographics...</a><br/><br/></td>
	</table>
	</div> <!-- end 'core' section of contact form --></br></br>
	
	

	<div id="demographics">
	<table border="0" cellpadding="2" cellspacing="2">
	 <label>Demographics</label>
	<tr>
	<td class="form-item"><label>Gender:</label></td>
	<td class="form-item">{$form.gender.female.html}
	{$form.gender.male.html}
	{$form.gender.transgender.html}</td>
	{*{html_radios options=$form.gender.values selected=$form.gender.selected separator="<br />"}*}
	</tr>
	<tr>
	<td class="form-item"><label>Date of Birth:</label></td>
	<td class="form-item">{$form.dd.html}{$form.mm.html}{$form.yy.html}</td>
	</tr>
	<tr>
	<td class="form-item" colspan=2>{$form.is_deceased.html}<label> Contact is Deceased </label></td>
	</tr>
	<tr>
	<td class="form-item"><label> Custom demographics flds </label></td>
	<td class="form-item">... go here ...</td>
	</tr>
	<tr>
	<td colspan=2><a tabindex="20" onclick="hide('demographics');show('expand_demographics');return false;" 
			 href="#demographics">[-] hide demographics...</a><br/><br/></td>
	</tr>
	</table>
	</div> <!-- end demographics div -->

	<table cellpadding="2" cellspacing="2"">
	<tr>
	<td class="form-item">
	   <td>{$form.buttons.html}</td>
	</td>
	</tr>
	</table>
	</form>
	
	{literal}<script type="text/javascript">
	 var sections = new Array('demographics','expand_phone0_2','phone0_2','expand_phone0_3','phone0_3',
				 'expand_email0_2','email0_2','expand_email0_3','email0_3',
				 'expand_IM0_2','IM0_2','expand_IM0_3','IM0_3','expand_demographics');
	 var showit = new Array("core");
	//function init (fn,fld) 
         {for (var i = 0; i < showit.length; i++) 
	 {document.getElementById(showit[i]).style.display = 'block';}
	 for (var i = 0; i < 13; i++)
	 {document.getElementById(sections[i]).style.display = 'none';}
	 document.getElementById('txt').focus();}
	</script>{/literal}

   </body>
</html>
