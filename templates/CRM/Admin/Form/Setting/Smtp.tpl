<div class="form-item">
<fieldset><legend>{ts}SMTP Server{/ts}</legend>
        <dl>
            <dt>{$form.smtpServer.label}</dt><dd>{$form.smtpServer.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Enter the (machine) name.{/ts}</dd>
            <dt>{$form.smtpPort.label}</dt><dd>{$form.smtpPort.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts} Standard STMP Port is 25, need to change that value if  SMTP server is running on a non-standard port.{/ts}</dd>
            <dt>{$form.smtpAuth.label}</dt><dd>{$form.smtpAuth.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}If server requires authentication,set true.{/ts}</dd>    
            <dt>{$form.smtpUsername.label}</dt><dd>{$form.smtpUsername.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Smtp username.{/ts}</dd>
        </dl>
        <dl>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div>
