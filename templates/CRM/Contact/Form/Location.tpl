
{* STARTING UNIT gx3 LOCATION ENGINE *}
{* STARTING UNIT gx3 LOCATION ENGINE *}

{assign var = "pid" value = ""}


{* ################# STARTING SECTION LOCATIONT ############### *}
{* ################# STARTING SECTION LOCATIONT ############### *}
 
{section name = locationt start = 1 loop = $locloop}
{assign var = "index" value = "`$smarty.section.locationt.index`"}
 
{if $locloop - 1  > 1} 
{assign var = "pid" value = "`$smarty.section.locationt.index`"}
{*$pid|truncate:$pid|count_characters:"`$smarty.section.locationt.index`"*}
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
			{* Plugging the Email block *}
			{include file="CRM/Contact/Form/Email.tpl"}
			</td>
		</tr>
 
		<tr>
			<td>&nbsp;</td>
			<td colspan = 2>		
			{* Plugging the Im block *}
			{include file="CRM/Contact/Form/IM.tpl"}
			</td>
		</tr>
 
		<tr>
			<td>&nbsp;</td>
			<td colspan = 2>
			{* Plugging the Address block *}
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

