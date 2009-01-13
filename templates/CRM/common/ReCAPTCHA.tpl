{if $recaptchaHTML}
  <tr>
     <td class="label">&nbsp;</td>
     <td>
{$recaptchaHTML}
{$form.recaptcha_challenge_field.html}
{$form.recaptcha_response_field.html}
</noscript>
     </td>
  </tr>
{/if}