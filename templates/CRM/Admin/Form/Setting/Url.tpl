<div id="help">
    {ts}These settings define the URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). 
    Default values will be inserted the first time you access CiviCRM - based on the CIVICRM_UF_BASEURL specified in
    your installation's settings file (civicrm.settings.php).{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}Site URLs{/ts}</legend>

        <dl>
            <dt>{$form.userFrameworkResourceURL.label}</dt><dd>{$form.userFrameworkResourceURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}URL of the location where the civicrm module or component has been installed.{/ts}</dd>
            <dt>&nbsp;</dt><dd class="description">
            <table class="form-layout">
            <tr><td>
            {ts}
            <strong>Drupal Example</strong><br />
            If your site's home url is http://www.example.com/ ... then your CiviCRM Resource URL would be:
            <div class="font-italic description">
             &nbsp;&nbsp; http://www.example.com/modules/civicrm/
            </div>
            
            <strong>Joomla Example</strong><br />
            If your site's home url is http://www.example.com/ ... then your CiviCRM Resource URL would be:
            <div class="font-italic description">
             &nbsp;&nbsp; http://www.example.com/administrator/components/com_civicrm/civicrm/
            </div>
            {/ts}
            </td></tr>
            </table>
            </dd>
            <dt>{$form.imageUploadURL.label}</dt><dd>{$form.imageUploadURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}URL of the location for uploaded image files.{/ts}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
