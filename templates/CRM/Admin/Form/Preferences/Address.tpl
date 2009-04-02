<div class="form-item">

	<fieldset><legend>{ts}Mailing Labels{/ts}</legend>
		<table class="form-layout">
			<tr>
				<td>
					{$form.mailing_format.label}
					{$form.mailing_format.html|crmReplace:class:huge}
					<span class="description">{ts}Address format for mailing labels. Use the {literal}{contact.state_province}{/literal} token for state/province abbreviation or {literal}{contact.state_province_name}{/literal} for full name.{/ts}{help id='label-tokens'}</span>

					{$form.individual_name_format.label}
					{$form.individual_name_format.html|crmReplace:class:huge}
					<span class="description">{ts}Formatting for individual contact names when {literal}{contact.contact_name}{/literal} token is included in mailing labels.{/ts} {help id='name-tokens'}</span>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset><legend>{ts}Address Display{/ts}</legend>
		<table class="form-layout">
			<tr>
				<td class="label">
					{$form.address_format.label}
					{$form.address_format.html|crmReplace:class:huge}
					<span class="description">{ts}Format for displaying addresses in the Contact Summary and Event Information screens.<br />Use {literal}{contact.state_province}{/literal} for state/province abbreviation or {literal}{contact.state_province_name}{/literal} for state province name.{/ts}{help id='address-tokens'}</span>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset><legend>{ts}Address Editing{/ts}</legend>
		<table class="form-layout">
			<tr>
				<td class="label">{$form.address_options.label}</td>
				<td>{$form.address_options.html}<br />
					<span class="description">{ts}Select the fields to be included when editing a contact or event address.{/ts}</span>
					</td>
				</tr>
			<tr>
				<td class="label">{$form.location_count.label}</td>
				<td>{$form.location_count.html|crmReplace:class:two}<br />
					<span class="description">{ts}Enter the maximum number of different locations/addresses that can be entered for a contact.{/ts}</span>
					</td>
				</tr>
			</table>
		</fieldset>
	<fieldset><legend>{ts}Address Standardization{/ts}</legend>
		<div class="description">
			{ts 1=http://www.usps.com/webtools/address.htm}CiviCRM includes an optional plugin for interfacing the the United States Postal Services (USPS) Address Standardization web service. You must register to use the USPS service at <a href='%1'>%1</a>. If you are approved, they will provide you with a User ID and the URL for the service.{/ts}
			</div>
		<table class="form-layout">
			<tr>
				<td class="label">{$form.address_standardization_provider.label}</td>
				<td>{$form.address_standardization_provider.html}<br />
					<span class="description">{ts}Address Standardization Provider. Currently, only 'USPS' is supported.{/ts}</span>
					</td>
				</tr>
			<tr>
				<td class="label">{$form.address_standardization_userid.label}</td>
				<td>{$form.address_standardization_userid.html}<br />
					<span class="description">{ts}USPS-provided User ID.{/ts}</span>
					</td>
				</tr>
			<tr>
				<td class="label">{$form.address_standardization_url.label}</td>
				<td>{$form.address_standardization_url.html}<br />
					<span class="description">{ts}USPS-provided web service URL.{/ts}</span>
					</td>
				</tr>
			</table>
		</fieldset>
	<table class="form-layout">
		<tr><td>&nbsp;</td><td>{$form.buttons.html}</td></tr>
		</table>
	<div class="spacer"></div>
	</div>
