<div id="help">
    {ts}These settings define the URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). Default values will be inserted the first time you access CiviCRM - based on the CIVICRM_UF_BASEURL specified in your installation's settings file (civicrm.settings.php).{/ts}
</div>
<fieldset><legend>{ts}Site URLs{/ts}</legend>
  <div class="form-item">
        <dl>
            <dt>{$form.userFrameworkResourceURL.label}</dt><dd>{$form.userFrameworkResourceURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Absolute URL of the location where the civicrm module or component has been installed.{/ts}</dd>
            <dt>&nbsp;</dt><dd class="description">
            <table class="form-layout-compressed">
            <tr><td>
            <strong>{ts}Drupal Example{/ts}</strong><br />
            {ts 1=http://www.example.com/}If your site's home url is %1 ... then your CiviCRM Resource URL would be:{/ts} 
            <div class="font-italic description">
             &nbsp;&nbsp; http://www.example.com/sites/all/modules/civicrm/
            </div>
            
            <strong>{ts}Joomla Example{/ts}</strong><br />
            {ts 1=http://www.example.com/}If your site's home url is %1 ... then your CiviCRM Resource URL would be:{/ts} 
            <div class="font-italic description">
             &nbsp;&nbsp; http://www.example.com/administrator/components/com_civicrm/civicrm/
            </div>
            </td></tr>
            </table>
            </dd>
            <dt>{$form.imageUploadURL.label}</dt><dd>{$form.imageUploadURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}URL of the location for uploaded image files.{/ts}</dd>
            <dt>{$form.enableSSL.label}</dt><dd>{$form.enableSSL.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Redirect online contribution / member / event page requests to a secure (https) URL?{/ts} {help id='enable-ssl'}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
  <div class="spacer"></div>
  </div>
</fieldset>
