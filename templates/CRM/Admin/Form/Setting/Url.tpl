<div id="help">
    {ts}These settings define the URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). Default values will be inserted the first time you access CiviCRM - based on the CIVICRM_UF_BASEURL specified in your installation's settings file (civicrm.settings.php).{/ts}
</div>
<fieldset><legend>{ts}Site URLs{/ts}</legend>
  <div class="form-item">
        <dl>
            <dt>{$form.userFrameworkResourceURL.label}</dt>
            <dd>{$form.userFrameworkResourceURL.html|crmReplace:class:'huge'} {help id='id-resource_url'}</dd>
            <dt>{$form.imageUploadURL.label}</dt><dd>{$form.imageUploadURL.html|crmReplace:class:'huge'} {help id='id-image_url'}</dd>
            <dt>{$form.customCSSURL.label}</dt><dd>{$form.customCSSURL.html|crmReplace:class:'huge'} {help id='id-css_url'}</dd>
            <dt>{$form.enableSSL.label}</dt><dd>{$form.enableSSL.html} {help id='id-enable_ssl'}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
  <div class="spacer"></div>
  </div>
</fieldset>
