 {* smarty *}

 {literal}
 <script type="text/javascript" src="/js/Individual.js"></script>
 {/literal}


 {$form.javascript}

 <form {$form.attributes}>

	 {$form.mdyx.html}

 <table border="0" width="100%" cellpadding="2" cellspacing="2">
 <tr><td>
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
</td></tr>


 <div id="core">
<tr><td>
 <!--label><i><h1>Name and Greeting</h1></i></label-->
 <fieldset><legend>Name and Greeting</legend>
 <table border = "0" cellpadding="2" cellspacing="2" width="100%">
	 <tr>
		 <td class="form-item" width="130"><label>{$form.first_name.label}</label></td>
		 <td>
		 {$form.prefix.html}
		 {$form.first_name.html}
		 {$form.last_name.html}
		 {$form.suffix.html}
		 </td>
	 </tr>
	 <tr>
		 <td class="form-item"><label>{$form.greeting_type.label}</label></td>
		 <td class="form-item">{$form.greeting_type.html}</td>
	 </tr>
	 <tr>
		 <td class="form-item"><label>{$form.job_title.label}</label></td>
		 <td class="form-item">{$form.job_title.html}</td>
	 </tr>
 </table>
 </fieldset>
 </td></tr>
 
 <tr><td>
 <fieldset><legend>Communication Preferences</legend>
 <table cellpadding="2" cellspacing="2" width="100%">		
	 <!--tr-->
		 <!--td><label><i><h1>Communication Preferences</h1></i></label></td-->
		 <!--td></td-->
	 <!--/tr-->
	 <tr>	
		 <td>
		 <table border="0" cellpadding="2" cellspacing="2" width="100%">
			 <tr>
				 <td class="form-item"><label>{$form.do_not_phone.label}</label></td>
				 <td>{$form.do_not_phone.html}{$form.do_not_phone.text} 
						       {$form.do_not_email.html}{$form.do_not_email.text}
						       {$form.do_not_mail.html}{$form.do_not_mail.text}
				 </td>
			 </tr>
			 <tr>
				 <td class="form-item"><label>{$form.preferred_communication_method.label}</label></td>
				 <td class="form-item">{$form.preferred_communication_method.html}
				 <div class="description">Preferred method of communicating with this individual</div></td>
			 </tr>
		 </table>
		 </td>
	</tr> 
 </table>
 </fieldset>
 </td></tr>
 
 <!--label><i><h1>Location</h1></i></label-->
 <!--tr><td-->
 <!--fieldset><legend>Location</legend-->

 {* STARTING UNIT gx3 LOCATION ENGINE *}

 {section name = locationt start = 1 loop = 4}
 {assign var = "lid" value = "location`$smarty.section.locationt.index`"}
 {assign var = "pid" value = `$smarty.section.locationt.index`}
 {assign var = "exloc" value = "exloc`$smarty.section.locationt.index`"} 
 {assign var = "hideloc" value = "hideloc`$smarty.section.locationt.index`"} 

 {assign var = "exph02" value = "exph02_`$smarty.section.locationt.index`"} 
 {assign var = "exem02" value = "exem02_`$smarty.section.locationt.index`"} 
 {assign var = "exim02" value = "exim02_`$smarty.section.locationt.index`"}
 {assign var = "hideph02" value = "hideph02_`$smarty.section.locationt.index`"}
 {assign var = "hideem02" value = "hideem02_`$smarty.section.locationt.index`"} 
 {assign var = "hideim02" value = "hideim02_`$smarty.section.locationt.index`"}

 {assign var = "exph03" value = "exph03_`$smarty.section.locationt.index`"} 
 {assign var = "exem03" value = "exem03_`$smarty.section.locationt.index`"}
 {assign var = "exim03" value = "exim03_`$smarty.section.locationt.index`"}
 {assign var = "hideph03" value = "hideph03_`$smarty.section.locationt.index`"} 
 {assign var = "hideem03" value = "hideem03_`$smarty.section.locationt.index`"}
 {assign var = "hideim03" value = "hideim03_`$smarty.section.locationt.index`"}

 

 <tr><td>
 {if $pid > 1}
 <table id = "expand_loc{$pid}" border="0" cellpadding="2" cellspacing="2" width="100%">
	 <tr>
		 <td>
		 {$form.$exloc.html}
		 </td>
	 <tr>
 </table>
 {/if}
 </td></tr>


 <tr><td>
 <table id = "location{$pid}" border="0" cellpadding="2" cellspacing="2" width="100%">

<!--fieldset><legend>Location</legend--><!--tr-->
<!--td colspan = "2" class = "form-item">Location{$pid}:</td--> <!--/tr-->

	<tr><td>
	<fieldset><legend>Location{$pid}</legend>

	<table border="0" cellpadding="2" cellspacing="2" width="100%">

	<tr>		
		 <td class="form-item">
		 {$form.$lid.location_type_id.html}
		</td>
		<td>&nbsp;</td><td>&nbsp;</td>
	</tr>
	<tr>
		 <td>&nbsp;</td>		
		 <td colspan=2 class="form-item">	
		 {$form.$lid.is_primary.html}{$form.$lid.is_primary.label}
		 </td>
	 </tr>

	 <!-- LOADING PHONE BLOCK -->
	 <tr>
		<td>&nbsp;</td>	
		 <td class="form-item">
		 <label>{$form.$lid.phone_1.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.phone_type_1.html}{$form.$lid.phone_1.html}
		 </td>

	 </tr>

	 <tr><!-- Second phone block.-->
		<td>&nbsp;</td>
		 <td colspan = "2">
			 <table id="expand_phone_{$pid}_2">
			 <tr>
				 <td>
				 {$form.$exph02.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
		
	 </tr>

	 <tr>
		<td>&nbsp;</td>
		 <td colspan = "2">	

		 <table id="phone_{$pid}_2">
			 <tr>
				 <td class="form-item">
				 <label>{$form.$lid.phone_2.label}</label>
				 </td>
				 <td class="form-item">
				 {$form.$lid.phone_type_2.html}{$form.$lid.phone_2.html}
				 </td>
			 </tr>	

			 <tr>
				 <td colspan="2">
				 {$form.$hideph02.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
		
	 </tr>

	 <tr><!-- Third phone block.-->
		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="expand_phone_{$pid}_3">
			 <tr>	<td>
				 {$form.$exph03.html}
				 </td>
			 </tr>
		 </table>
		 </td>
		
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="phone_{$pid}_3">
			 <tr>
				 <td class="form-item">
				 <label>{$form.$lid.phone_3.label}</label></td>
				 <td class="form-item">
				 {$form.$lid.phone_type_3.html}{$form.$lid.phone_3.html}
				 </td>
			 </tr>

			 <tr>
				 <td colspan="2">
				 {$form.$hideph03.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
	 </tr>



 <!-- LOADING EMAIL BLOCK -->

	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
			 <label>{$form.$lid.email_1.label}</label>
		 </td>
		 <td class = "form-item">
			 {$form.$lid.email_1.html}
		 </td>
		
	 </tr>
	 <tr><!-- email 2.-->
		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="expand_email_{$pid}_2" >
			 <tr>
				 <td>
				 {$form.$exem02.html}
				 </td>
			 </tr>
		 </table>
		 </td>
	 </tr>

	 <tr>
		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="email_{$pid}_2">
			 <tr>
				 <td class="form-item">
				 <label>{$form.$lid.email_2.label}</label>
				 </td>
				 <td class = "form-item">
				 {$form.$lid.email_2.html}
				 </td>
		 </tr>
		 <tr>

			 <td>
			 {$form.$hideem02.html}
			 </td>
		 </tr>
		 </table>
		 </td>
	 </tr>
	 <tr><!-- email 3.-->

		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="expand_email_{$pid}_3" >
			 <tr>
				 <td>
				 {$form.$exem03.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="email_{$pid}_3">
			 <tr>
				 <td class="form-item">
				 <label>{$form.$lid.email_3.label}</label>
				 </td>
				 <td class = "form-item">
				 {$form.$lid.email_3.html}
				 </td>
			 </tr>	

			 <tr>
				 <td colspan="2">
				 {$form.$hideem03.html}
				 </td>
			 </tr>
		 </table>
		 </td>
	 </tr>
	 <tr><!-- LOADING IM BLOCK -->

		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.im_service_id_1.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.im_service_id_1.html}{$form.$lid.im_screenname_1.html}
		 <div class="description">Select IM service and enter screen-name / user id.</div>
		 </td>
		 
	 </tr>
	 <tr><!-- IM 2.-->

		<td>&nbsp;</td>
		 <td colspan = "2">
			 <table id="expand_IM_{$pid}_2">
			 <tr>
				 <td>
				 {$form.$exim02.html}
				 </td>
			 </tr>
			 </table>
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="IM_{$pid}_2">
			 <tr>
				 <td class="form-item">
				 <label>{$form.$lid.im_service_id_2.label}</label></td>
				 <td class="form-item">
				 {$form.$lid.im_service_id_2.html}{$form.$lid.im_screenname_2.html}
				 <div class="description">Select IM service and enter screen-name / user id.</div></td>
			 </tr>
			 <tr>
				 <td colspan="2">
				 {$form.$hideim02.html}
				 </td>
			 </tr>
		 </table>
		 </td>
	 </tr>
	 <tr><!-- IM 3.-->

		<td>&nbsp;</td>
		 <td colspan = "2">
		 <table id="expand_IM_{$pid}_3" >
			 <tr>	
				 <td>
				 {$form.$exim03.html}
				 </td>
			 </tr>
		 </table>
		 </td> 
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td colspan = "2">	
		 <table id="IM_{$pid}_3">
			 <tr>
				 <td class="form-item">
				 <label>{$form.$lid.im_service_id_3.label}</label></td>
				 <td class="form-item">
				 {$form.$lid.im_service_id_3.html}{$form.$lid.im_screenname_3.html}
				 <div class="description">Select IM service and enter screen-name / user id.</div>
				 </td>
			 </tr>
			 <tr>
				 <td colspan="2">
				 {$form.$hideim03.html}
				 </td>
			 </tr>
		 </table>
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.street_address.label}</label>
		</td>
		 <td class="form-item">
		 {$form.$lid.street_address.html}<!--br/-->
		 <div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.supplemental_address_1.label}</label>
		</td>
		 <td class="form-item">
		 {$form.$lid.supplemental_address_1.html}<!--br/-->

		 <div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.city.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.city.html}<!--br/-->
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.state_province_id.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.state_province_id.html}
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.postal_code.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.postal_code.html}<!--br/-->
		 </td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td class="form-item">
		 <label>{$form.$lid.country_id.label}</label>
		 </td>
		 <td class="form-item">
		 {$form.$lid.country_id.html}
		 </td>
	 </tr>

	 </td>
	 </tr>

	 <tr>
		<td>&nbsp;</td>
		<td colspan = "2">
		{if $pid > 1 }
 		<table id = "expand_loc{$pid}" border="0" cellpadding="2" cellspacing="2" width="100%">	
		<tr>
		 	<td colspan = "2">
			 {$form.$hideloc.html}
		 	</td>
		</tr>
		</table> 
		{/if}
		</td>
		
	 </tr>

</table>
</fieldset>
</td></tr>
</table>
</td></tr>
{/section}

<!--/td></tr-->
<!--/table-->
<!--/fieldset-->
<!--/td></tr-->

 {* ENDING UNIT gx3 LOCATION ENGINE *} 

 {******************************** ENDIND THE DIV SECTION **************************************}
 {******************************** ENDIND THE DIV SECTION **************************************}

 </div> <!--end 'core' section of contact form -->



<tr><td>
 <div id = "expand_demographics">
 <table>
	 <tr>
		 <td>
		 {$form.exdemo.html}
		 </td>
	 </tr>
 </table>
</div>
</td></tr>


 <tr><td>
 <div id="demographics">
 <fieldset><legend>Demographics</legend>
 <table border="0" cellpadding="2" cellspacing="2" width="100%">
	  <!--label><i><h1>Demographics</h1></i></label-->
	 <tr>
		 <td class="form-item"><label>{$form.gender.female.label}</label></td>
		 <td class="form-item">{$form.gender.female.html}
		 {$form.gender.male.html}
		 {$form.gender.transgender.html}</td>
	 {*{html_radios options=$form.gender.values selected=$form.gender.selected separator="<br />"*}
	 </tr>
	 <tr>
		 <td class="form-item"><label>{$form.birth_date.label}</label></td>
		 <td class="form-item">{$form.birth_date.html}</td>
	 </tr>
	 <tr>
		 <td class="form-item" colspan=2>{$form.is_deceased.html}<label>{$form.is_deceased.label} </label></td>
	 </tr>
	 <tr>
		 <td class="form-item"><label> Custom demographics flds </label></td>
		 <td class="form-item">... go here ...</td>
	 </tr>
	 <tr>
		 <td colspan=2>
		 {$form.hidedemo.html}
		 </td>
	 </tr>

 </table>
 </fieldset>
 </div>
 </td></tr>
  

 {******************************** ENDIND THE DEMOGRAPHICS SECTION **************************************}
 {******************************** ENDIND THE DEMOGRAPHICS SECTION **************************************}

 
<tr><td> 
<div id = "expand_notes">
<table border="0" cellpadding="2" cellspacing="2">
	 <tr>
		 <td>
		 {$form.exnotes.html}
		 </td>
	 <tr>
 </table>
</div>
</td></tr>



 <tr><td>
 <div id = "notes">
 <fieldset><legend>Notes</legend>
 <table border="0" cellpadding="2" cellspacing="2">
	 <tr>
		 <td class="form-item">{$form.address_note.label}</td>
		 <td class="form-item">{$form.address_note.html}
		 <div class = "description">
		  Record any descriptive comments about this contact. You may add an unlimited number of notes, and view or 
		 <br/>search on them at any time.</div>
		 </td>
	 </tr>
	 <tr>	
		 <td colspan=2>{$form.hidenotes.html}</td>
	 </tr>
 </table>
 </fieldset>
</div>
 </td></tr>
</table>



 <div id = "buttons">
 <table cellpadding="2" cellspacing="2" width="100%">
 <tr>
	 <td class="form-item">
	 {$form.buttons.html}
	 </td>

 </tr>
 </table>
 </div>

 </form>


 {literal}
 <script type="text/javascript">
 on_load_execute('Individual');
 </script>
 {/literal}


 {if count($form.errors) gt 0}
 {literal}
 <script type="text/javascript">
 on_error_execute('Individual');
 </script>
 {/literal}
 {/if}


