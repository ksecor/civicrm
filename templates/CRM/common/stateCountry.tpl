{if $countryID && $stateID}
{literal}
<script language="JavaScript" type="text/javascript">
cj(function()
{
{/literal}
        countryID       = '#{$countryID[$index]}',
	stateProvinceID = '#{$stateID[$index]}'
        callbackURL     = '{crmURL p="civicrm/ajax/jqState"}'
{literal}
	cj(countryID).chainSelect(stateProvinceID,
                                  callbackURL,
				  null);
});
</script>
{/literal}
{/if}
