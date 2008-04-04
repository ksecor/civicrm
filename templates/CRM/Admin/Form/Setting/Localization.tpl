<div class="form-item">
    <fieldset><legend>{ts}Language and Currency{/ts}</legend>    
        <dl>
            <dt>{$form.lcMessages.label}</dt><dd>{$form.lcMessages.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts 1="http://civicrm.org/download"}Language used for this installation. Installed 'translations' are listed in langageCode_countryCode format. Check the <a href='%1' target='_blank'>CiviCRM downloads page</a> for additional translations if the one you need is not listed.{/ts}</dd>
            <dt>{$form.defaultCurrency.label}</dt><dd>{$form.defaultCurrency.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Default currency assigned to contributions and other monetary transactions.{/ts}</dd>
            <dt>{$form.lcMonetary.label}</dt><dd>{$form.lcMonetary.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Locale for monetary display (affects formatting specifiers below).{/ts}</dd>
            <dt>{$form.moneyformat.label}</dt><dd>{$form.moneyformat.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Format for displaying monetary values.{/ts}</dd>
            <dt>{$form.legacyEncoding.label}</dt><dd>{$form.legacyEncoding.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}If import files are NOT encoded as UTF-8, specify an alternate character encoding for these files. The default of <strong>Windows-1252</strong> will work for Excel-created .CSV files on many computers.{/ts}</dd>
        </dl>
    </fieldset>
    <fieldset><legend>{ts}Contact Address Fields - Selection Values{/ts}</legend>
        <dl>
            <dt>{$form.defaultContactCountry.label}</dt><dd>{$form.defaultContactCountry.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}This value is selected by default when adding a new contact address.{/ts}</dd>
            <dt>{$form.countryLimit.label}</dt><dd>{$form.countryLimit.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Which countries are available in the Country selection field when adding or editing contact addresses. To include ALL countries, leave the right-hand box empty.{/ts}</dd>
            <dt>{$form.provinceLimit.label}</dt><dd>{$form.provinceLimit.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Which countries' states and/or provinces are available in the State / Province selection field <strong>for Custom Fields and Profile forms</strong>. (Standard contact address editing forms automatically display corresponding state / province options for the selected country.){/ts}</dd>
        </dl>
    </fieldset>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</div>
