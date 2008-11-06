{if $countryID && $stateID}
{literal}
<script language="JavaScript" type="text/javascript">
cj(function()
{
{/literal}
        countryID       = '#{$countryID[$index]}',
	stateProvinceID = '#{$stateID[$index]}'
        callbackURL     = '{$callbackURL}'
{literal}
	cj(countryID).chainSelect(stateProvinceID,
                                  callbackURL,
				  null);
});
</script>
{/literal}
{/if}
{if $form.location.$index.address.country_id}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.country_id.label}
    </span>
    <span class="fields">
        {$form.location.$index.address.country_id.html}
    </span>
</div>
{/if}


