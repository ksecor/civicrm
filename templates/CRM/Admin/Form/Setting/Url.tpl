<div id="help">
    {ts}These settings define the URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). Default values will be inserted the first time you access CiviCRM - based on the CIVICRM_UF_BASEURL specified in your installation's settings file (civicrm.settings.php).{/ts}
</div>
<div class="form-item">
<fieldset>
<table class="form-layout">
    <tr>
        <td class="label">
            {$form.userFrameworkResourceURL.label}
        </td>
        <td>
            {$form.userFrameworkResourceURL.html|crmReplace:class:'huge40'} {help id='id-resource_url'}
        </td>
    </tr>
    <tr>
        <td class="label">
            {$form.imageUploadURL.label}
        </td>
        <td>
            {$form.imageUploadURL.html|crmReplace:class:'huge40'} {help id='id-image_url'}
        </td>
    </tr>
    <tr>
        <td class="label">
            {$form.customCSSURL.label}
        </td>
        <td>
            {$form.customCSSURL.html|crmReplace:class:'huge40'} {help id='id-css_url'}
        </td>
    </tr>
    <tr>
        <td class="label">
            {$form.enableSSL.label}
        </td>
        <td>
            {$form.enableSSL.html} {help id='id-enable_ssl'}
        </td>
    </tr>
</table>
</fieldset>
</div>
<div class="html-adjust">{$form.buttons.html}</div>

