<div id="help">
    {ts}CiviCRM includes plugins for Google and Yahoo mapping services which allow your users to display contact addresses
        on a map. To enable this feature, select your mapping provider and obtain a "key" for your site from that provider.{/ts} {help id='map-key'}
</div>
<div class="form-item">
<fieldset><legend>{ts}Mapping and Geocoding{/ts}</legend>
        <dl>
         <dt>{$form.mapProvider.label}</dt><dd>{$form.mapProvider.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts} Choose the provider that has the best coverage for your the majority of your contact addresses.{/ts}</dd>
         <dt>{$form.mapAPIKey.label}</dt><dd>{$form.mapAPIKey.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts}Enter your Google API Key OR your Yahoo Application ID.{/ts} {help id='map-key2'}</dd>
         <dt>{$form.mapGeoCoding.label}</dt><dd>{$form.mapGeoCoding.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts}If not using Google, make sure to set to No.{/ts}</dd>         
         {*<dt>{$form.geocodeMethod.label}</dt><dd>{$form.geocodeMethod.html}</dd>
         <dt>&nbsp</dt><dd class="description">{ts}CiviCRM can be configured to automatically GeoCode lookup and insert latitude & longitude for contact addresses.{/ts}</dd> *}
         <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
 <div class="spacer"></div>
</fieldset>
</div>
