<div id="help">
    {ts}These settings define the URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). 
    Default values will be inserted the first time you access CiviCRM - based on the CIVICRM_UF_BASEURL specified in
    your installation's settings file (civicrm.settings.php).{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}Site URLs{/ts}</legend>

        <dl>
            <dt>{$form.userFrameworkResourceURL.label}</dt><dd>{$form.userFrameworkResourceURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Absolute URL of the location where the civicrm module or component has been installed.{/ts} {help id='resource-url'}</dd>
            <dt>{$form.imageUploadURL.label}</dt><dd>{$form.imageUploadURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Absolute URL of the location for uploaded image files.{/ts}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
