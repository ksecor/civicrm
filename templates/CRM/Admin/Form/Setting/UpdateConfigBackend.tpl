<div class="form-item">
<fieldset><legend>{ts}Update Config Backend{/ts}</legend>
    
        <dl>
            <dt>{ts}Old Base URL{/ts}</dt><dd>{$oldBaseURL}</dd>
            <dt>{$form.newBaseURL.label}</dt><dd>{$form.newBaseURL.html|crmReplace:class:'huge'}</dd>
            <dt>{ts}Old Base Dir{/ts}</dt><dd>{$oldBaseDir}</dd>
            <dt>{$form.newBaseDir.label}</dt><dd>{$form.newBaseDir.html|crmReplace:class:'huge'}</dd>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
   
<div class="spacer"></div>
</fieldset>
</div>
