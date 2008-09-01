<fieldset>
    <table class="form-layout">
        <tr>
            <td style="white-space: normal;">{$form.versionCheck.label}</td>
            <td>{$form.versionCheck.html}<br />
                <p class="description">{ts}If enabled, CiviCRM automatically checks availablity of a newer version of the software. New version alerts will be displayed on the main CiviCRM Administration page.{/ts}</p>
                <p class="description">{ts}When enabled, statistics about your CiviCRM installation are reported anonymously to the CiviCRM team to assist in prioritizing ongoing development efforts. The following information is gathered: CiviCRM version, versions of PHP, MySQL and framework (Drupal/Joomla/standalone), and default language. Counts (but no actual data) of the following record types are reported: contacts, activities, cases, relationships, contributions, contribution pages, contribution products, contribution widgets, discounts, price sets, profiles, events, participants, tell-a-friend pages, grants, mailings, memberships, membership blocks, pledges, pledge blocks and active payment processor types.{/ts}</p></td>
        </tr>
        <tr>
            <td>{$form.maxAttachments.label}</td>
            <td>{$form.maxAttachments.html}<br />
                <span class="description">{ts}Maximum number of files (documents, images, etc.) which can attached to emails or activities.{/ts}</span></td>
        </tr>
    </table>
</fieldset>
<fieldset><legend>{ts}Contact Search{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td>{$form.includeWildCardInName.label}</td>
            <td>{$form.includeWildCardInName.html}<br />
                <span class="description">{ts}If enabled, wildcards are automatically added when users search for contacts by Name. EXAMPLE: Searching for 'ada' will return any contact whose name includes those letters - e.g. 'Adams, Janet', 'Nadal, Jorge', etc. Disabling this feature will speed up search significantly for larger databases, but users must use MySQL wildcard characters for partial name searches (e.g. '%' or '_').{/ts}</span></td>
        </tr>
        <tr>
            <td>{$form.includeEmailInName.label}</td>
            <td>{$form.includeEmailInName.html}<br />
                <span class="description">{ts}If enabled, email addresses are automatically included when users search by Name. Disabling this feature will speed up search significantly for larger databases, but users will need to use the Email search fields (from Advanced Search, Search Builder, or Profiles) to find contacts by email address.{/ts}</span></td>
        </tr>
        <tr>
            <td>{$form.includeNickNameInName.label}</td>
            <td>{$form.includeNickNameInName.html}<br />
                <span class="description">{ts}If enabled, nicknames are automatically included when users search by Name.{/ts}</span></td>
        </tr>
    </table>
</fieldset>
<fieldset><legend>{ts}ReCAPTCHA Keys{/ts}</legend>
    <div class="description">
        {ts}ReCAPTCHA is a free service that helps prevent automated abuse of your site. To use ReCAPTCHA on public-facing CiviCRM forms: sign up at <a href="http://recaptcha.net">recaptcha.net</a>; enter the provided public and private ReCAPTCHA keys here; then enable ReCAPTCHA under Advanced Settings in any Profile.{/ts}
    </div>
    <table class="form-layout">
        <tr>
            <td>{$form.recaptchaPublicKey.label}</td>
            <td>{$form.recaptchaPublicKey.html}</td>
        </tr>
        <tr>
            <td>{$form.recaptchaPrivateKey.label}</td>
            <td>{$form.recaptchaPrivateKey.html}</td>
        </tr>
        <tr>
            <td></td>
            <td>{$form.buttons.html}</td>
        </tr>
    </table>
</fieldset>
