{* This file provides the templating for the Location block *}
{* The phone, Email, Instant messenger and the Address blocks have been plugged in from external source files*}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $pid Contains the index of the location block under the locationt loop *}

 {assign var = "pid" value = ""}


{* The locationt section displays the location block *}
{* The section loops as many times as indicated by the variable $locloop to give as many phone blocks *}

{* @var $locloop Gives the number of location loops to be displayed, assigned in the Location.tpl file*}
{* $index contains the current index of the locationt section *}
{* $smarty.section.locationt.index contains the current index of the locationt section *}
{* The section loops to display as many location blocks as contained in the $locloop variable *}
{* @var $lid Contains the current location id in evaluation *}
{* @var $width Contains the width setting for the first column in the table *} 
{* @var $exloc Contains the name of the location expansion link *}
{* @var $hideloc Contains the name of the location hide link *}

 {section name = locationt start = 1 loop = $locloop}
 {assign var = "index" value = "`$smarty.section.locationt.index`"}
 
 {if $locloop - 1  > 1} 
 {assign var = "pid" value = "`$smarty.section.locationt.index`"}

 {/if}

 {assign var = "lid" value = "location`$pid`"}

 {assign var = "exloc" value = "exloc`$index`"} 
 {assign var = "hideloc" value = "hideloc`$index`"} 
 {assign var = "width" value = "200"}
 

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
 <table id = "location{$pid}" border="0" cellpadding="2" cellspacing="2">

	<tr><td>
	<fieldset><legend>Location{$pid}</legend>

	<table border="0" cellpadding="2" cellspacing="2" width="750">

		<tr>		
			<td class="form-item" width = "50">
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

		<tr>
			<td>&nbsp;</td>
			<td colspan = 2>
			{* Plugging the phone block *}
			{include file="CRM/Contact/Form/Phone.tpl"}
			</td>
		</tr> 

		<tr>
			<td>&nbsp;</td>
			<td colspan = 2>
			{* Plugging the email block *}
			{include file="CRM/Contact/Form/Email.tpl"}
			</td>
		</tr>
 
		<tr>
			<td>&nbsp;</td>
			<td colspan = 2>		
			{* Plugging the instant messenger block *}
			{include file="CRM/Contact/Form/IM.tpl"}
			</td>
		</tr>
 
		<tr>
			<td>&nbsp;</td>
			<td colspan = 2>
			{* Plugging the address block *}
			{include file="CRM/Contact/Form/Address.tpl"} 
		 	</td>
		</tr>

	 	<tr>
			<td>&nbsp;</td>
			<td colspan = "2">
			{if $pid > 1 }
 			<table id = "expand_loc{$index}" border="0" cellpadding="2" cellspacing="2" width="100%">	
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

