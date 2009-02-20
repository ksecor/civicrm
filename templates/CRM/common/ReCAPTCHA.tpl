{if $recaptchaHTML}
<table class="form-layout-compressed">
    <tr><td class="recaptcha_label">&nbsp;</td>
    <td>{$recaptchaHTML}
        {$form.recaptcha_challenge_field.html}
        {$form.recaptcha_response_field.html}</noscript>
    </td></tr>
</table>
{/if}