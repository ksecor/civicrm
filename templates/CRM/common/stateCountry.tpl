{if $config->stateCountryMap}
<script language="JavaScript" type="text/javascript">
{foreach from=$config->stateCountryMap item=stateCountryMap}
{if $stateCountryMap.country && $stateCountryMap.state_province}
{literal}
$(function()
{
{/literal}
        countryID       = "#{$stateCountryMap.country}"
	    stateProvinceID = "#{$stateCountryMap.state_province}"
        callbackURL     = "{crmURL p='civicrm/ajax/jqState'}"
{literal}
	$(countryID).chainSelect(stateProvinceID, callbackURL, null );
});
{/literal}
{/if}
{/foreach}
</script>
{/if}
