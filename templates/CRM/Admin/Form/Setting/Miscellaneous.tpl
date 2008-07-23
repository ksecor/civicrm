<div class="form-item">
<fieldset>
        <dl>
          <dt>{$form.versionCheck.label}</dt><dd>{$form.versionCheck.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Do you want CiviCRM to automatically check availablity of a newer version of the software? If enabled, new version alerts will be displayed on the main CiviCRM Administration page..{/ts}</dd>
          <dt>{$form.includeWildCardInName.label}</dt><dd>{$form.includeWildCardInName.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Do you want CiviCRM to include the mysql wildcard when doing a name search? If disabled, it will speed up search significantly and enable mysql to use the relevant index{/ts}</dd>
          <dt>{$form.includeEmailInName.label}</dt><dd>{$form.includeEmailInName.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Do you want CiviCRM to search the email table when searching for a name? if disabled it will speed up search significantly and avoid additional left join's in the query.{/ts}</dd>
          <dt>{$form.includeNickNameInName.label}</dt><dd>{$form.includeNickNameInName.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Do you want CiviCRM to search the nickname field when searching for a name?{/ts}</dd>
          <dt>{$form.maxAttachments.label}</dt><dd>{$form.maxAttachments.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Maximum number of attachments to display in CiviMail / Activity?{/ts}</dd>
          <dt>{$form.recaptchaPublicKey.label}</dt><dd>{$form.recaptchaPublicKey.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Public Key obtained from recaptcha.net {/ts}</dd>
          <dt>{$form.recaptchaPrivateKey.label}</dt><dd>{$form.recaptchaPrivateKey.html}</dd>
          <dt>&nbsp</dt><dd class="description">{ts}Private Key obtained from recaptcha.net.{/ts}</dd>
          <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
   
 <div class="spacer"></div>
</fieldset>
</div>
