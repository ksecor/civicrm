{* smarty *}
 {literal}
 <script type="text/javascript" src="/js/CLIST.js"></script>
 {/literal}


{$form.javascript}

<form {$form.formx_head.label}>

<table>
<tr>
<td>
{$form.contact_select.label}
{$form.contact_select.html}
{$form.change_list_view.html}
</td>
</tr>
</table>
</form>

<form {$form.formx_body.label}>
<fieldset>
<table id = "linkheader">
<tr>
<td width = "150">
	{$form.export.html}
</td>
<!--td>
	{$form.first.html}
	{$form.previous.html}
	{$form.page_serial.html}
	{$form.next.html}
	{$form.previouspage.html}
</td-->

<td width = "300">
    
    	{$form.pager.label}
</td>
<td>
	{$form.page_no.label}
	{$form.page_no.html}
	{$form.gotopage.html}
</td>
</tr>
</table>
</fieldset>
</form>

<form {$form.attributes}>
{$form.hidden}

<table border=0 cellpadding=3 cellspacing=3 id = "datagrid" width = "100%" border-color="#000000">
<tr class="contact_listtable">
<td width = "250">{$form.name.html}</td>
<td>{$form.email.html}</td>
<td>{$form.phone.html}</td>
<td>{$form.address.html}</td>
<td>{$form.city.html}</td>
<td width = "30">{$form.state_province.html}</td>
<td></td>
</tr>


{assign var = "bgc" value =  "#EAEAEA"}

{section name = listing start = 0 loop = $form.row_count.label }

{assign var = "index" value = $smarty.section.listing.index}
{assign var = "name_link" value = "name_`$smarty.section.listing.index`"}
{assign var = "email_link" value = "email_`$smarty.section.listing.index`"}
{assign var = "phone_link" value = "phone_`$smarty.section.listing.index`"}
{assign var = "address_link" value = "address_`$smarty.section.listing.index`"}
{assign var = "city_link" value = "city_`$smarty.section.listing.index`"}
{assign var = "state_province_link" value = "state_province_`$smarty.section.listing.index`"}
{assign var = "checkbox"  value = "checkrecord_`$smarty.section.listing.index`"}

{if $bgc eq "#EAEAEA"}
{assign var = "bgc" value  =  "#FAFAF8"}
{else}
{assign var = "bgc" value  = "#EAEAEA"}
{/if}

<tr bgcolor = {$bgc}>
<td>{$form.name_link_group.$name_link.html}</td>
<td>{$form.email_link_group.$email_link.html}</td>
<td>{$form.phone_link_group.$phone_link.html}</td>
<td>{$form.address_link_group.$address_link.html}</td>
<td>{$form.city_link_group.$city_link.html}</td>
<td width = "10" align = "center">{$form.state_link_group.$state_province_link.html}</td>
<td width = "10">{$form.checkbox_group.$checkbox.html}</td>
</tr>

{/section}
</table>
</form>

<form  {$form.formx_crumb.label}>

<table id = "pagecrumb" width = "90%">
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>	{$form.show_25.label}
			{$form.show_25.html}
			{$form.show_50.label}
			{$form.show_50.html}
			{$form.show_100.label}
			{$form.show_100.html}
			{$form.show_all.label}
			{$form.show_all.html}

		</td>
		<td align = "right">
			{$form.action_select.label}
			{$form.action_select.html}
			{$form.do_action.html}
		</td>
	</tr>
		<td colspan = "2" align = "right">
			{$form.select_all.label}
			{$form.select_all.html}
			{$form.select_none.label}
			{$form.select_none.html}		
		</td>
	<tr>

	</tr>
</table>


</form>
