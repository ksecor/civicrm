<form {$form.attributes}>

{if $form.hidden}
{$form.hidden}
{/if}

{if count($form.errors) gt 0}
<table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#ff9900"><tr><td>
<table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFCC"><tr><td align="center">
<span class="error" style="font-size: 13px;">Please correct the errors below.</span>
</td></tr></table>
</td></tr></table>
<p />
{/if}

<table width="100%" cellpadding="0" cellspacing="3"> 

<table width="100%" bgcolor="white" cellpadding="0" cellspacing="0" border="1"> 

<tr><td class="label" width="28%" valign="top">{$form.first_name.label}</td><td valign="top">{$form.first_name.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.last_name.label}</td><td valign="top">{$form.last_name.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.address_line_1.label}</td><td valign="top">{$form.address_line_1.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.address_line_2.label}</td><td valign="top">{$form.address_line_2.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.city.label}</td><td valign="top">{$form.city.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.state.label}</td><td valign="top">{$form.state.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.zipcode.label}</td><td valign="top">{$form.zipcode.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.email.label}</td><td valign="top">{$form.email.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.telephone_no_home.label}</td><td valign="top">{$form.telephone_no_home.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.telephone_no_work.label}</td><td valign="top">{$form.telephone_no_work.html}</td></tr>
<tr><td class="label" width="28%" valign="top">{$form.telephone_no_cellular.label}</td><td valign="top">{$form.telephone_no_cellular.html}</td></tr>

</table>

<table width="100%">
<tr><td>&nbsp;</td><td align="right">{$form.buttons.html}</td></tr>
</table>

</table>

{$form.first_name.theme}
{$form.last_name.theme}
{$form.address_line_1.theme}

</form>


