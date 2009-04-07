<div class="ui-widget">


    <div class="ui-widget-header" style="padding: 8px;"><h1 style="font-size: 1.3em;">{ts}Settings - Resource URLs{/ts}</h1></div>

    <div class="ui-widget-content">

    <div class="ui-state-highlight" style="padding: 0 .7em; font-size: .9em;">
        <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span> {ts}These settings define the URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). Default values will be inserted the first time you access CiviCRM - based on the CIVICRM_UF_BASEURL specified in your installation's settings file (civicrm.settings.php).{/ts}</p>
    </div>
    <table style="margin: 0">
        <tr class="border-bottom">
            <th scope="row" class="label">
                {$form.userFrameworkResourceURL.label}
            </th>
            <td>
                {$form.userFrameworkResourceURL.html|crmReplace:class:'huge'} {help id='id-resource_url'}
            </td>
        </tr>
        <tr class="border-bottom">
            <th scope="row" class="label">
                {$form.imageUploadURL.label}
            </th>
            <td>
                {$form.imageUploadURL.html|crmReplace:class:'huge'} {help id='id-image_url'}
            </td>
        </tr>
        <tr class="border-bottom">
            <th scope="row" class="label">
                {$form.customCSSURL.label}
            </th>
            <td>
                {$form.customCSSURL.html|crmReplace:class:'huge'} {help id='id-css_url'}
            </td>
        </tr>
        <tr class="border-bottom">
            <th scope="row" class="label">
                {$form.enableSSL.label}
            </th>
            <td>
                <div class="checkboxgroup">{$form.enableSSL.html} {help id='id-enable_ssl'}</div>
            </td>
        </tr>
    </table>

    </div> <!-- end widget content -->
    <div class="submit">{$form.buttons.html}</div>

</div>