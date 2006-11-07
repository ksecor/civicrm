<div class="form-item">
<fieldset><legend>{ts}Mapping and Geocoding{/ts}</legend>
        <dl>
         <dt>{$form.mapProvider.label}</dt><dd>{$form.mapProvider.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts} Choose the provider that has the best coverage by setting the Map to either 'Google' or 'Yahoo'.{/ts}</dd>
         <dt>{$form.mapAPIKey.label}</dt><dd>{$form.mapAPIKey.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts}Enter either Google API key OR Yahoo Application ID.{/ts}</dd>
         <dt>{$form.mapGeoCoding.label}</dt><dd>{$form.mapGeoCoding.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts}If not using Google, make sure to set to No.{/ts}</dd>         
         {*<dt>{$form.geocodeMethod.label}</dt><dd>{$form.geocodeMethod.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts}CiviCRM can be configured to automatically GeoCode lookup and insert latitude & longitude for contact addresses.{/ts}</dd> *}
         <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
 <div class="spacer"></div>
</fieldset>
</div>
