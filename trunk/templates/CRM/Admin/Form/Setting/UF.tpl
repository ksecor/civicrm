<div id="help">
    {ts}These settings define the CMS variables that are used with CiviCRM.{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}CMS Settings{/ts}</legend>

        <dl>
            <dt>{$form.userFrameworkVersion.label}</dt><dd>{$form.userFrameworkVersion.html}</dd>
            <dt>{$form.userFrameworkUsersTableName.label}</dt><dd>{$form.userFrameworkUsersTableName.html}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>

{if $tablePrefixes}
<div class="form-item">
<fieldset>
    <legend>{ts}Views integration settings{/ts}</legend>
    <div>{ts}To enable CiviCRM Views integration, add the following to the site <code>settings.php</code> file:{/ts}</div>
    <pre>{$tablePrefixes}</pre>
</fieldset>
</div>
{/if}
