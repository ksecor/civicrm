<div class="form-item">
<fieldset><legend>{ts}File System Paths{/ts}</legend>
        <dl>
          <dt>{$form.uploadDir.label}</dt><dd>{$form.uploadDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}File system path where temporary CiviCRM files - such as
 import data files - are uploaded.{/ts}</dd>
          <dt>{$form.imageUploadDir.label}</dt><dd>{$form.imageUploadDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}File system path where image files are uploaded.{/ts}</dd>
          <dt>{$form.customFileUploadDir.label}</dt><dd>{$form.customFileUploadDir.html|crmReplace:class:'huge'}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}File system path where documents and images which are attachments to contacts records are stored (e.g. contact photos, resumes, contracts, etc.).{/ts}</dd>
        </dl>
        <dl>
          <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
