<div class="form-item">
<fieldset><legend>{ts}Upload CSV File{/ts}</legend>
  <dl>
    <dt>{$form.uploadFile.label}</dt><dd>{$form.uploadFile.html}</dd>
    <dt>&nbsp;</dt>
    <dd class="description">{ts}File format must be comma-separated-values (CSV). File must be UTF8 encoded if it contains special characters (e.g. accented letters, etc.).{/ts}</dd>
    <dt>&nbsp;</dt>
    <dd>{ts 1=$uploadSize}Maximum Upload File Size: %1 MB{/ts}</dd>
    <dt> </dt><dd>{$form.skipColumnHeader.html} {$form.skipColumnHeader.label}</dd>
    <dt>&nbsp;</dt>
    <dd class="description">
        {ts}Check this box if the first row of your file consists of field names (Example: 'First Name','Last Name','Email'){/ts}
    </dd>
  </dl>
</fieldset>
</div>
