{* smarty *}
 {literal}
 <script type="text/javascript" src="/js/LIST.js"></script>
 {/literal}


{$form.javascript}
<form {$form.attributes}>

<table>
<tr>
<td>
{$form.contact_select.label}
{$form.contact_select.html}
{$form.change_list_view.html}
</td>
</tr>
</table>

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

<td>
    
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




<table border=1 cellpadding=1 cellspacing=0 id = "datagrid" width = "100%">
<tr bgcolor=#000000>
<td class="form-item">{$form.name.html}</td>
<td>{$form.email.html}</td>
<td>{$form.phone.html}</td>
<td>{$form.address.html}</td>
<td>{$form.city.html}</td>
<td>{$form.state_province.html}</td>
<td></td>
</tr>


{assign var = "bgc" value =  "white"}

{section name = listing start = 0 loop = $form.row_no.label }

{assign var = "index" value = $smarty.section.listing.index}
{assign var = "name_link" value = "name_`$smarty.section.listing.index`"}
{assign var = "email_link" value = "email_`$smarty.section.listing.index`"}
{assign var = "phone_link" value = "phone_`$smarty.section.listing.index`"}
{assign var = "address_link" value = "address_`$smarty.section.listing.index`"}
{assign var = "city_link" value = "city_`$smarty.section.listing.index`"}
{assign var = "state_province_link" value = "state_province_`$smarty.section.listing.index`"}
{assign var = "checkbox"  value = "checkrecord_`$smarty.section.listing.index`"}

{if $bgc eq "white"}
{assign var = "bgc" value  =  "silver"}
{else}
{assign var = "bgc" value  = "white"}
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
