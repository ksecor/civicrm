{* smarty *}
{literal}
<script type="text/javascript" src="/js/ORG.js"></script>
{/literal}

{$form.javascript}

<form {$form.attributes}>

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
	
<br/>
<div id="core">
<label><i><h1>Organization</h1></i></label>
<table border = "1" cellpadding="2" cellspacing="2">
	<tr>
		<td class="form-item"><label>Name:</label></td>
		<td>{$form.organization_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>Legal Name:</label></td>
		<td>{$form.legal_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>Nick Name:</label></td>
		<td>{$form.nick_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>Enter contact id:</label></td>
		<td>{$form.primary_contact_id.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>SIC Code:</label></td>
		<td>{$form.sic_code.html}</td>
	</tr>

</table>

<br/>
<table cellpadding="2" cellspacing="2">		
	<tr>
		<td><label><i><h1>Communication Preferences</h1></i></label></td>
		<td></td>
	</tr>
	<tr>	
		<td>
		<table border="1" cellpadding="2" cellspacing="2" width="90%">
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
			</table>
			
		</td>

	</tr> 
</table>
<br/>

<label><i><h1>Location</h1></i></label>

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

<br />
{if $pid > 1}
<table id = "expand_loc{$pid}" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td>
		{$form.$exloc.html}
		</td>
	<tr>
</table>

<br />
{/if}


<table id = "location{$pid}" border="1" cellpadding="2" cellspacing="2" width="90%">
	<tr>
		<td colspan = "2" class = "form-item">Location{$pid}:</td>
	</tr>
	<tr>	
		<td class="form-item">
		{$form.$lid.context_id.html}</td>
		<td colspan=2 class="form-item">	
		{$form.$lid.is_primary.html}<label>Primary location for this contact</label></td>
	</tr>

<!-- LOADING PHONE BLOCK -->
	<tr>
		
		<td class="form-item">
		<label>Preferred Phone:</label></td>
		<td class="form-item">
		{$form.$lid.phone_type_1.html}{$form.$lid.phone_1.html}
		</td>
	</tr>

	<tr><!-- Second phone block.-->
		
		<td colspan="2">
		<table id="expand_phone0_2_{$pid}">
		<tr>
			<td>
			{$form.$exph02.html}
			</td>
		</tr>
		</table></td>
	</tr>

	<tr>
	
		<td colspan="2">	

		<table id="phone0_2_{$pid}">
		<tr>
			<td class="form-item">
			<label>Other Phone:</label>
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
		</table></td>
	</tr>

	<tr><!-- Third phone block.-->
	
		<td colspan=2>
		<table id="expand_phone0_3_{$pid}">
			<tr>	<td>
				{$form.$exph03.html}
				</td>
			</tr>
		</table>
	       	</td>
	</tr>
	<tr>

		<td colspan="2">
		<table id="phone0_3_{$pid}">
		<tr>
			<td class="form-item">
			<label>Other Phone:</label></td>
			<td class="form-item">
			{$form.$lid.phone_type_3.html}{$form.$lid.phone_3.html}
			</td>
		</tr>

		<tr>
			<td colspan="2">
			{$form.$hideph03.html}
			</td>
		</tr>
		</table></td>
	</tr>



<!-- LOADING EMAIL BLOCK -->

	<tr>
		<td class="form-item">
		<label>Email:</label></td>
		<td class = "form-item">
		{$form.$lid.email.html}</td>
	</tr>
	<tr><!-- email 2.-->
		<td colspan="2">
		<table id="expand_email0_2_{$pid}" >
		<tr>
			<td>
			{$form.$exem02.html}
			</td>
		</tr>
		</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<table id="email0_2_{$pid}">
		<tr>
			<td class="form-item">
			<label>Other Email:</label>
			</td>
			<td class = "form-item">
			{$form.$lid.email_secondary.html}
			</td>
		</tr>
		<tr>
			<td colspan="2">
			{$form.$hideem02.html}
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr><!-- email 3.-->

		<td colspan="2">
		<table id="expand_email0_3_{$pid}" >
		<tr>
			<td>
			{$form.$exem03.html}
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<table id="email0_3_{$pid}">
		<tr>
			<td class="form-item">
			<label>Other Email:</label></td>
			<td class = "form-item">{$form.$lid.email_tertiary.html}
			</td>
		</tr>

		<tr>
			<td colspan="2">
			{$form.$hideem03.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr><!-- LOADING IM BLOCK -->
		
		<td class="form-item">
		<label>Instant Message:</label>
		</td>
		<td class="form-item">
		{$form.$lid.im_service_id_1.html}{$form.$lid.im_screenname_1.html}
		<div class="description">Select IM service and enter screen-name / user id.</div>
		</td>
	</tr>
	<tr><!-- IM 2.-->
		
		<td colspan="2">
		<table id="expand_IM0_2_{$pid}" >
		<tr>
			<td>
			{$form.$exim02.html}
			</td>
		</tr>
		</table	></td>
	</tr>
	<tr>
		<td colspan="2">
		<table id="IM0_2_{$pid}">
		<tr>
			<td class="form-item">
			<label>Instant Message:</label></td>
			<td class="form-item">
			{$form.$lid.im_service_id_2.html}{$form.$lid.im_screenname_2.html}
			<div class="description">Select IM service and enter screen-name / user id.</div></td>
		</tr>
		<tr>
			<td colspan="2">
			{$form.$hideim02.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr><!-- IM 3.-->
		<td colspan="2">
		<table id="expand_IM0_3_{$pid}" >
		<tr>	<td>
			{$form.$exim03.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan="2">	
		<table id="IM0_3_{$pid}">
		<tr>
			<td class="form-item">
			<label>Instant Message:</label></td>
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
		</table></td>
	</tr>
	<tr>
		
		<td class="form-item">
		<label>Street Address:</label></td>
		<td class="form-item">
		{$form.$lid.street.html}<br/>
		<div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
		</td>
	</tr>
	<tr>
		
		<td class="form-item">
		<label>Additional<br/>Address:</label></td>
		<td class="form-item">
		{$form.$lid.supplemental_address.html}<br/>
		<div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>City:</label>
		</td><td class="form-item">
		{$form.$lid.city.html}<br/>
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>State / Province:</label></td>
		<td class="form-item">
		{$form.$lid.state_province_id.html}
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>Zip / Postal Code:</label></td>
		<td class="form-item">
		{$form.$lid.postal_code.html}<br/>
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>Country:</label>
		</td><td class="form-item">
		{$form.$lid.country_id.html}
		</td>
	</tr>


	{if $pid > 1 }
	<tr>
		<td colspan = "2">
	
		{$form.$hideloc.html}
	
		</td>
	</tr>
	{/if}
</table>

{/section}


{* ENDING UNIT gx3 LOCATION ENGINE } */

{******************************** ENDIND THE DIV SECTION **************************************}
{******************************** ENDIND THE DIV SECTION **************************************}

</div> <!-- end 'core' section of contact form -->
<br>

<div id = "buttons">
<table cellpadding="2" cellspacing="2">
<tr>
	<td class="form-item">
	{$form.buttons.html}</td>
	
</tr>
</table>
</div>

</form>
	
{literal}
<script type="text/javascript">
on_load_execute();
</script>
{/literal}

{if count($form.errors) gt 0}
{literal}
<script type="text/javascript">
on_error_execute();
</script>
{/literal}
{/if}

