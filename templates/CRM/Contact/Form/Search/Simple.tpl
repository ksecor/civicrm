{literal}
<script language="JavaScript" type="text/javascript">
cj(function()
{
{/literal}
        countryID       = '#country_id'
	stateProvinceID = '#state_province_id'
        callbackURL     = '{$stateCountryURL}'
{literal}
	cj(countryID).chainSelect(stateProvinceID,
                                  callbackURL,
				  null);
});
</script>
{/literal}
<table>
        <tr> 
            <td class="label">{$form.country_id.label}</td>
            <td class="nowrap">{$form.country_id.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.state_province_id.label}</td> 
            <td class="nowrap">{$form.state_province_id.html}</td>
        </tr>
        <tr> 
            <td colspan=2>{$form.buttons.html}</td>
        </tr>

</table>
