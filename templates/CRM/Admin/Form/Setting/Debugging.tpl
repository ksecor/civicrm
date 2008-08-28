<div class="form-item">
<fieldset><legend>{ts}Debugging and Error Handling{/ts}</legend>
    
        <dl>
            <dt>{$form.debug.label}</dt><dd>{$form.debug.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Set this value to <strong>Yes</strong> if you want to use one of CiviCRM's debugging tools. <strong>This feature should NOT be enabled for production sites</strong>{/ts} {help id='debug'}</dd>
            <dt>{$form.backtrace.label}</dt><dd>{$form.backtrace.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Set this value to <strong>Yes</strong> if you want to display a backtrace listing when a fatal error is encountered. <strong>This feature should NOT be enabled for production sites</strong>{/ts}</dd>
            <dt>{$form.fatalErrorTemplate.label}</dt><dd>{$form.fatalErrorTemplate.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Enter the path and filename for a custom Smarty template if you want to define your own screen for displaying fatal errors.{/ts}</dd>
            <dt>{$form.fatalErrorHandler.label}</dt><dd>{$form.fatalErrorHandler.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}Enter the path and class for a custom PHP error-handling function if you want to override built-in CiviCRM error handling for your site.{/ts}</dd>
            <dt></dt><dd>{$form.buttons.html}</dd>
         </dl>
   
<div class="spacer"></div>
</fieldset>
</div>
