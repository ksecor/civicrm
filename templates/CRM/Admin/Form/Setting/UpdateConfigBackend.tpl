<div class="form-item">
<fieldset><legend>{ts}Update Directory Path and URL{/ts}</legend>
<div id="help">
    <p>
    {ts}Use this form if you need to reset the Base Directory Path and Base URL settings for your CiviCRM
    installation. These settings are stored in the database, and generally need adjusting after moving a
    CiviCRM installation to another location in the file system and/or to another URL.{/ts}</p>
    <p>
    {ts}CiviCRM will attempt to detect the new values that should be used. These are provided below as
    the default values for the <strong>New Base Directory</strong> and <strong>New Base URL</strong> fields.{/ts}</p>
</div>    
        <dl>
            <dt>{ts}Old Base Directory{/ts}</dt><dd>{$oldBaseDir}</dd>
            <dt>{$form.newBaseDir.label}</dt><dd>{$form.newBaseDir.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}For Drupal installs, this is the absolute path to the location of the 'files' directory. For Joomla installs this is the absolute path to the location of the 'media' directory.{/ts}</dd>
            <dt>{ts}Old Base URL{/ts}</dt><dd>{$oldBaseURL}</dd>
            <dt>{$form.newBaseURL.label}</dt><dd>{$form.newBaseURL.html|crmReplace:class:'huge'}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}This is the URL for your Drupal or Joomla site URL (e.g. http://www.mysite.com/drupal/).{/ts}</dt>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
   
<div class="spacer"></div>
</fieldset>
</div>
