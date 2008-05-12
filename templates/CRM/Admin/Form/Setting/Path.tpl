<div id="help">
    {ts}Default values will be supplied for these upload directories the first time you access CiviCRM - based on the CIVICRM_TEMPLATE_COMPILEDIR specified in civicrm.settings.php. If you need to modify the defaults, make sure that your web server has write access to the directories.{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}Upload Directories{/ts}</legend>
    <dl>
          <dt>{$form.uploadDir.label}</dt><dd>{$form.uploadDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp;</dt><dd class="description">{ts}File system path where temporary CiviCRM files - such as import data files - are uploaded.{/ts}</dd>
          <dt>{$form.imageUploadDir.label}</dt><dd>{$form.imageUploadDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp;</dt><dd class="description">{ts}File system path where image files are uploaded. Currently, this path is used for images associated with premiums (CiviContribute thank-you gifts).{/ts}</dd>
          <dt>{$form.customFileUploadDir.label}</dt><dd>{$form.customFileUploadDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp;</dt><dd class="description">{ts}Path where documents and images which are attachments to contact records are stored (e.g. contact photos, resumes, contracts, etc.). These attachments are defined using 'file' type custom fields.{/ts}</dd>
    </dl>
          <dt>{$form.customTemplateDir.label}</dt><dd>{$form.customTemplateDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp;</dt><dd class="description">{ts}Path where site specific templates are stored if any. This directory is searched first if set.{/ts}</dd>
          <dt>{$form.customPHPPathDir.label}</dt><dd>{$form.customPHPPathDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp;</dt><dd class="description">{ts}Path where site specific PHP code files are stored if any. This directory is searched first if set.{/ts}</dd>
    </dl>
    <dl>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
<div class="spacer"></div>
</fieldset>
</div>
