{if $config->stateCountryMap}
<script language="JavaScript" type="text/javascript">
{foreach from=$config->stateCountryMap item=stateCountryMap}
{literal}
cj(function()
{
{/literal}
        countryID       = "#{$stateCountryMap.country}"
	stateProvinceID = "#{$stateCountryMap.state_province}"
        callbackURL     = "{crmURL p='civicrm/ajax/jqState'}"
{literal}
	cj(countryID).chainSelect(stateProvinceID,
                                  callbackURL,
				  null);
});
{/literal}
{/foreach}
</script>
{/if}
