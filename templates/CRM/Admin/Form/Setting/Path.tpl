<div id="help">
    {ts}Default values will be supplied for these upload directories the first time you access CiviCRM - based on the CIVICRM_TEMPLATE_COMPILEDIR specified in civicrm.settings.php. If you need to modify the defaults, make sure that your web server has write access to the directories.{/ts}
</div>
    <fieldset><legend>{ts}Upload Directories{/ts}</legend>
        <table class="form-layout">
            <tr>
                <td class="label">{$form.uploadDir.label}</td>
                <td>{$form.uploadDir.html|crmReplace:class:'huge40'}<br />
                    <span class="description">{ts}File system path where temporary CiviCRM files - such as import data files - are uploaded.{/ts}</span>
                </td>
            </tr>
            <tr>
                <td class="label">{$form.imageUploadDir.label}</td>
                <td>{$form.imageUploadDir.html|crmReplace:class:'huge40'}<br />
                    <span class="description">{ts}File system path where image files are uploaded. Currently, this path is used for images associated with premiums (CiviContribute thank-you gifts).{/ts}</span>
                </td>    
            </tr>
            <tr>  
                <td class="label">{$form.customFileUploadDir.label}</td>
                <td>{$form.customFileUploadDir.html|crmReplace:class:'huge40'}<br />
                    <span class="description">{ts}Path where documents and images which are attachments to contact records are stored (e.g. contact photos, resumes, contracts, etc.). These attachments are defined using 'file' type custom fields.{/ts}</span>
                </td>
            </tr>
            <tr>  
                <td class="label">{$form.customTemplateDir.label}</td>
                <td>{$form.customTemplateDir.html|crmReplace:class:'huge40'}<br />
                    <span class="description">{ts}Path where site specific templates are stored if any. This directory is searched first if set.{/ts}</span>
                </td>
            </tr>
            <tr>  
                <td class="label">{$form.customPHPPathDir.label}</td>
                <td>{$form.customPHPPathDir.html|crmReplace:class:'huge40'}<br />
                    <span class="description">{ts}Path where site specific PHP code files are stored if any. This directory is searched first if set.{/ts}</span>
                </td>    
            </tr>
        </table>
    </fieldset>

<div class="html-adjust">{$form.buttons.html}</div>